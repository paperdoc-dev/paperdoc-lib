<?php

declare(strict_types=1);

namespace Paperdoc\Ocr;

/**
 * Manages a pool of concurrent shell processes via proc_open + stream_select.
 *
 * Launches up to $maxWorkers processes simultaneously, monitors their stdout
 * using non-blocking I/O, and returns results keyed by submission order.
 */
class ProcessPool
{
    private int $maxWorkers;

    private int $timeout;

    /** @var array<int, array{command: string, key: string}> */
    private array $queue = [];

    public function __construct(int $maxWorkers = 4, int $timeout = 60)
    {
        $this->maxWorkers = max(1, $maxWorkers);
        $this->timeout = $timeout;
    }

    /**
     * Add a command to the pool queue.
     *
     * @param string $command Shell command to execute
     * @param string $key     Unique key to identify this job in results
     */
    public function submit(string $command, string $key): void
    {
        $this->queue[] = ['command' => $command, 'key' => $key];
    }

    /**
     * Execute all queued commands with bounded concurrency.
     *
     * @return array<string, string> key => stdout output
     *
     * @throws \RuntimeException on process failure
     */
    public function run(): array
    {
        if (empty($this->queue)) {
            return [];
        }

        $results = [];
        $pending = $this->queue;
        $this->queue = [];

        /** @var array<int, array{proc: resource, pipes: array, key: string, started: float, output: string}> */
        $active = [];
        $nextId = 0;

        while (! empty($pending) || ! empty($active)) {
            // Fill worker slots
            while (! empty($pending) && count($active) < $this->maxWorkers) {
                $job = array_shift($pending);
                $proc = $this->startProcess($job['command']);

                if ($proc === null) {
                    $results[$job['key']] = '';

                    continue;
                }

                $active[$nextId] = [
                    'proc'    => $proc['process'],
                    'pipes'   => $proc['pipes'],
                    'key'     => $job['key'],
                    'started' => microtime(true),
                    'output'  => '',
                ];
                $nextId++;
            }

            if (empty($active)) {
                break;
            }

            // Build read set from stdout pipes
            $readStreams = [];
            $streamMap = [];
            foreach ($active as $id => $worker) {
                $stream = $worker['pipes'][1];
                if (is_resource($stream)) {
                    $readStreams[] = $stream;
                    $streamMap[(int) $stream] = $id;
                }
            }

            if (! empty($readStreams)) {
                $write = null;
                $except = null;
                $changed = @stream_select($readStreams, $write, $except, 0, 50_000);

                if ($changed > 0) {
                    foreach ($readStreams as $stream) {
                        $id = $streamMap[(int) $stream];
                        $chunk = fread($stream, 65536);
                        if ($chunk !== false && $chunk !== '') {
                            $active[$id]['output'] .= $chunk;
                        }
                    }
                }
            }

            // Check for completed or timed-out processes
            foreach ($active as $id => $worker) {
                $status = proc_get_status($worker['proc']);
                $elapsed = microtime(true) - $worker['started'];
                $timedOut = $elapsed > $this->timeout;

                if ($status['running'] && ! $timedOut) {
                    continue;
                }

                if ($timedOut && $status['running']) {
                    proc_terminate($worker['proc'], 15);
                    usleep(10_000);
                    $recheck = proc_get_status($worker['proc']);
                    if ($recheck['running']) {
                        proc_terminate($worker['proc'], 9);
                    }
                } else {
                    // Process finished normally: drain remaining output
                    stream_set_blocking($worker['pipes'][1], true);
                    $remaining = stream_get_contents($worker['pipes'][1]);
                    if ($remaining !== false && $remaining !== '') {
                        $active[$id]['output'] .= $remaining;
                    }
                }

                fclose($worker['pipes'][1]);
                fclose($worker['pipes'][2]);
                proc_close($worker['proc']);

                $results[$worker['key']] = $timedOut
                    ? ''
                    : $this->filterOutput($active[$id]['output']);
                unset($active[$id]);
            }
        }

        return $results;
    }

    public function getQueueSize(): int
    {
        return count($this->queue);
    }

    /**
     * @return array{process: resource, pipes: array}|null
     */
    private function startProcess(string $command): ?array
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $env = array_merge($_ENV, [
            'OMP_THREAD_LIMIT' => '1',
            'OMP_NUM_THREADS'  => '1',
        ]);

        $process = proc_open($command, $descriptors, $pipes, null, $env);

        if (! is_resource($process)) {
            return null;
        }

        fclose($pipes[0]);

        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        return ['process' => $process, 'pipes' => $pipes];
    }

    private function filterOutput(string $output): string
    {
        $lines = explode("\n", $output);
        $filtered = array_filter($lines, fn (string $line) =>
            trim($line) !== ''
            && ! str_starts_with($line, 'Corrupt JPEG data')
            && ! str_starts_with($line, 'Warning:')
        );

        return trim(implode("\n", $filtered));
    }

    /**
     * Detect the number of CPU cores available.
     */
    public static function detectCpuCores(): int
    {
        if (PHP_OS_FAMILY === 'Linux' && is_readable('/proc/cpuinfo')) {
            $content = file_get_contents('/proc/cpuinfo');
            $count = substr_count($content, 'processor');
            if ($count > 0) {
                return $count;
            }
        }

        $output = [];
        $code = 0;
        exec('nproc 2>/dev/null', $output, $code);
        if ($code === 0 && ! empty($output)) {
            return max(1, (int) $output[0]);
        }

        return 4;
    }
}
