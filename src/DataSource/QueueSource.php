<?php

namespace Wtsergo\AmpDataPipeline\DataSource;

use Amp\Pipeline\Queue;

class QueueSource implements \IteratorAggregate
{
    public function __construct(
        private readonly Queue $queue
    )
    {
    }

    public function getIterator(): \Traversable
    {
        return $this->queue->iterate();
    }
}
