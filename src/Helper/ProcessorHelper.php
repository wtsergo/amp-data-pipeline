<?php

namespace Wtsergo\AmpDataPipeline\Helper;

use Amp\Future;
use Amp\Pipeline\Queue;
use Revolt\EventLoop;
use function Amp\Future\await;

trait ProcessorHelper
{
    /**
     * @param \SplObjectStorage $storage
     * @param \Closure(mixed, mixed, ...$args): void $tap
     * @return void
     */
    protected function tapObjectStorage(\SplObjectStorage $storage, \Closure $tap, mixed ...$args): void
    {
        $storage->rewind();
        while ($storage->valid()) {
            $processor = $storage->current();
            $info = $storage->getInfo();
            $tap($processor, $info, ...$args);
            $storage->next();
        }
    }

    /**
     * @param \SplObjectStorage $storage
     * @param \Closure(mixed, mixed, ...$args): mixed $collect
     * @param mixed ...$args
     * @return array
     */
    protected function collectObjectStorage(\SplObjectStorage $storage, \Closure $collect, mixed ...$args): array
    {
        $result = [];
        $storage->rewind();
        while ($storage->valid()) {
            $processor = $storage->current();
            $info = $storage->getInfo();
            $result[] = $collect($processor, $info, ...$args);
            $storage->next();
        }
        return $result;
    }

    /**
     * @param list<Future> $futures
     * @param Queue $queue
     * @return void
     */
    protected function trackQueueFutures(Queue $queue, $futures)
    {
        EventLoop::queue(static function () use ($futures, $queue): void {
            try {
                await($futures);
                if (!$queue->isComplete()) {
                    $queue->complete();
                }
            } catch (\Throwable $exception) {
                // uncomment if getting Amp\Pipeline\DisposedException
                //die("$exception");
                $queue->error($exception);
                throw $exception;
            }
        });
    }
}
