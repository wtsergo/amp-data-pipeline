<?php

namespace Wtsergo\AmpDataPipeline\Helper;

use Amp\Pipeline\ConcurrentIterator;
use Amp\Pipeline\Queue;
use Amp\Future;
use Wtsergo\AmpDataPipeline\DataItem\DataItemHandler;
use Wtsergo\AmpDataPipeline\Processor;

trait ProcessorAssertion
{
    protected function assertFutureComplete(?Future $future=null)
    {
        if ($future) {
            if (!$future->isComplete()) {
                throw new \RuntimeException('Processor future is not complete');
            }
        }
    }
    protected function assertQueueComplete(?Queue $queue=null): void
    {
        if ($queue) {
            if (!$queue->isComplete()) {
                throw new \RuntimeException('Processor queue is not complete');
            }
        }
    }
    protected function assertSource($source): void
    {
        if ($source === null) {
            throw new \RuntimeException('Undefined processor source');
        }
    }

    protected function assertSourceIterator($iterator): void
    {
        if (!$iterator instanceof ConcurrentIterator) {
            throw new \RuntimeException(sprintf(
                'Processor source iterator must be instance of %s, [%s] provided',
                ConcurrentIterator::class,
                \get_debug_type($iterator)
            ));
        }
    }

    protected function assertProcessor($processor): void
    {
        if (!$processor instanceof Processor) {
            throw new \RuntimeException(sprintf(
                'Processor must be instance of %s, [%s] provided',
                    Processor::class,
                    \get_debug_type($processor)
            ));
        }
    }

    protected function assertHandler($handler): void
    {
        if (!$handler instanceof DataItemHandler) {
            throw new \RuntimeException(sprintf(
                'Data item handler must be instance of %s, [%s] provided',
                    DataItemHandler::class,
                    \get_debug_type($handler)
            ));
        }
    }
}
