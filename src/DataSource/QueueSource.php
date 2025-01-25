<?php

namespace Wtsergo\AmpDataPipeline\DataSource;

use Amp\Pipeline\ConcurrentIterator;
use Amp\Pipeline\Internal\ConcurrentQueueIterator;
use Amp\Pipeline\Queue;
use Wtsergo\AmpDataPipeline\DataItem\DataItem;
use Wtsergo\AmpDataPipeline\DataSource;

class QueueSource implements DataSource
{
    /**
     * @var ConcurrentQueueIterator<DataItem>
     */
    private ConcurrentQueueIterator $iterator;
    public function __construct(
        private readonly Queue $queue
    )
    {
        $this->iterator = $this->queue->iterate();
    }

    public function getIterator(): ConcurrentIterator
    {
        return $this->iterator;
    }
}
