<?php

declare(strict_types=1);

namespace Paperdoc\Ocr;

use Paperdoc\Support\Cast;

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

    /** @var list<array{command: string, key: string}> */
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
        if ($this->queue === []) {
            return [];
        }

        $results = [];
        $pending = $this->queue;
        $this->queue = [];

        /** @var array<int, array{proc: resource, pipes: array<int, resource>, key: string, started: float}> $active */
        $active = [];
        /** @var array<int, string> $outputs */
        $outputs = [];
        $nextId = 0;

        while ($pending !== [] || $active !== []) {
            // Fill worker slots
            while ($pending !== [] && count($active) < $this->maxWorkers) {
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
                ];
                $outputs[$nextId] = '';
                $nextId++;
            }

            if ($active === []) {
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

            if ($readStreams !== []) {
                $write = null;
                $except = null;
                $changed = @stream_select($readStreams, $write, $except, 0, 50_000);

                if ($changed !== false && $changed > 0) {
                    foreach ($readStreams as $stream) {
                        $id = $streamMap[(int) $stream];
                        $chunk = fread($stream, 65536);
                        if ($chunk !== false && $chunk !== '') {
                            $outputs[$id] .= $chunk;
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
                    $stdout = $worker['pipes'][1];
                    stream_set_blocking($stdout, true);
                    $remaining = stream_get_contents($stdout);
                    if ($remaining !== false && $remaining !== '') {
                        $outputs[$id] .= $remaining;
                    }
                }

                fclose($worker['pipes'][1]);
                fclose($worker['pipes'][2]);
                proc_close($worker['proc']);

                $results[$worker['key']] = $timedOut
                    ? ''
                    : $this->filterOutput($outputs[$id] ?? '');
                unset($active[$id], $outputs[$id]);
            }
        }

        return $results;
    }

    public function getQueueSize(): int
    {
        return count($this->queue);
    }

    /**
     * @return array{process: resource, pipes: array<int, resource>}|null
     */
    private function startProcess(string $command): ?array
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        /** @var array<string, mixed> $env */
        $env = [];
        foreach ($_ENV as $key => $value) {
            if (is_string($key)) {
                $env[$key] = $value;
            }
        }
        $env['OMP_THREAD_LIMIT'] = '1';
        $env['OMP_NUM_THREADS'] = '1';

        $process = proc_open($command, $descriptors, $pipes, null, $env);

        if (! is_resource($process)) {
            return null;
        }

        fclose($pipes[0]);
        unset($pipes[0]);

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
            $content = Cast::asString(file_get_contents('/proc/cpuinfo'));
            $count = substr_count($content, 'processor');
            if ($count > 0) {
                return $count;
            }
        }

        $output = [];
        $code = 0;
        exec('nproc 2>/dev/null', $output, $code);
        if ($code === 0 && $output !== []) {
            return max(1, (int) $output[0]);
        }

        return 4;
    }
}
