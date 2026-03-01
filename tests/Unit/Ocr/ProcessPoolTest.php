<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Ocr;

use PHPUnit\Framework\TestCase;
use Paperdoc\Ocr\ProcessPool;

class ProcessPoolTest extends TestCase
{
    public function test_empty_pool_returns_empty_array(): void
    {
        $pool = new ProcessPool;
        $this->assertSame([], $pool->run());
    }

    public function test_single_job_returns_output(): void
    {
        $pool = new ProcessPool;
        $pool->submit('echo "hello world"', 'job1');

        $results = $pool->run();

        $this->assertArrayHasKey('job1', $results);
        $this->assertSame('hello world', $results['job1']);
    }

    public function test_multiple_jobs_return_keyed_results(): void
    {
        $pool = new ProcessPool(4);
        $pool->submit('echo "alpha"', 'a');
        $pool->submit('echo "beta"', 'b');
        $pool->submit('echo "gamma"', 'c');

        $results = $pool->run();

        $this->assertCount(3, $results);
        $this->assertSame('alpha', $results['a']);
        $this->assertSame('beta', $results['b']);
        $this->assertSame('gamma', $results['c']);
    }

    public function test_concurrent_execution_is_faster_than_sequential(): void
    {
        $pool = new ProcessPool(4);

        for ($i = 0; $i < 4; $i++) {
            $pool->submit('sleep 0.3 && echo "done"', "job{$i}");
        }

        $start = microtime(true);
        $results = $pool->run();
        $elapsed = microtime(true) - $start;

        $this->assertCount(4, $results);
        foreach ($results as $result) {
            $this->assertSame('done', $result);
        }

        // 4 jobs of 0.3s each: sequential = ~1.2s, parallel = ~0.3s
        $this->assertLessThan(1.0, $elapsed, "Parallel pool should finish in < 1s, took {$elapsed}s");
    }

    public function test_pool_respects_max_workers(): void
    {
        $pool = new ProcessPool(2);

        for ($i = 0; $i < 4; $i++) {
            $pool->submit('echo "ok"', "job{$i}");
        }

        $results = $pool->run();
        $this->assertCount(4, $results);
    }

    public function test_multiline_output_is_preserved(): void
    {
        $pool = new ProcessPool;
        $pool->submit('echo "line1"; echo "line2"; echo "line3"', 'multi');

        $results = $pool->run();
        $this->assertSame("line1\nline2\nline3", $results['multi']);
    }

    public function test_failed_command_returns_empty_string(): void
    {
        $pool = new ProcessPool;
        $pool->submit('exit 1', 'fail');

        $results = $pool->run();
        $this->assertArrayHasKey('fail', $results);
        $this->assertSame('', $results['fail']);
    }

    public function test_queue_is_cleared_after_run(): void
    {
        $pool = new ProcessPool;
        $pool->submit('echo "first"', 'a');

        $this->assertSame(1, $pool->getQueueSize());
        $pool->run();
        $this->assertSame(0, $pool->getQueueSize());
    }

    public function test_process_timeout(): void
    {
        $pool = new ProcessPool(2, 1);
        $pool->submit('sleep 10 && echo "should not appear"', 'slow');
        $pool->submit('echo "fast"', 'fast');

        $start = microtime(true);
        $results = $pool->run();
        $elapsed = microtime(true) - $start;

        $this->assertSame('fast', $results['fast']);
        $this->assertLessThan(5.0, $elapsed, 'Timeout should kill the slow process');
    }

    public function test_detect_cpu_cores_returns_positive_int(): void
    {
        $cores = ProcessPool::detectCpuCores();
        $this->assertGreaterThan(0, $cores);
    }

    public function test_filters_corrupt_jpeg_warnings(): void
    {
        $pool = new ProcessPool;
        $pool->submit('echo "Corrupt JPEG data"; echo "actual text"; echo "Warning: foo"', 'filter');

        $results = $pool->run();
        $this->assertSame('actual text', $results['filter']);
    }

    public function test_large_batch_ordering(): void
    {
        $pool = new ProcessPool(4);

        for ($i = 0; $i < 10; $i++) {
            $pool->submit("echo \"{$i}\"", (string) $i);
        }

        $results = $pool->run();

        for ($i = 0; $i < 10; $i++) {
            $this->assertSame((string) $i, $results[(string) $i]);
        }
    }
}
