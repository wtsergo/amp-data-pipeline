<?php

namespace Wtsergo\AmpDataPipeline\DataSource;

use Amp\Pipeline\ConcurrentIterator;
use Amp\Pipeline\Queue;

class QueueSource implements \IteratorAggregate
{
    public function __construct(
        private readonly Queue $queue
    )
    {
    }

    public function getIterator(): ConcurrentIterator
    {
        return $this->queue->iterate();
    }
}
