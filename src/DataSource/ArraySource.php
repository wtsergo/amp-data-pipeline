<?php

namespace Wtsergo\AmpDataPipeline\DataSource;

use Amp\Pipeline\Queue;
use Wtsergo\AmpDataPipeline\Helper\ProcessorHelper;
use function Amp\async;

class ArraySource implements \IteratorAggregate
{
    use ProcessorHelper;

    protected ?Queue $queue = null;

    public function __construct(
        private readonly array $values
    )
    {
    }

    private function read()
    {
        foreach ($this->values as $value) {
            $this->queue->push($value);
        }
        $this->queue->complete();
    }

    public function getIterator(): \Traversable
    {
        if (null === $this->queue) {
            $this->queue = $queue = new Queue();
            $future = async($this->read(...));
            $this->trackQueueFutures($queue, [$future]);
        }
        return $this->queue->iterate();
    }
}
