<?php

namespace Wtsergo\AmpDataPipeline;

use Amp\Pipeline\ConcurrentIterator;
use Amp\Pipeline\Queue;
use Wtsergo\AmpDataPipeline\DataItem\DataItem;
use Wtsergo\AmpDataPipeline\Helper\ProcessorAssertion;
use Wtsergo\AmpDataPipeline\Helper\ProcessorHelper;
use function Amp\async;

abstract class ProcessorAbstract implements Processor
{
    use ProcessorAssertion;
    use ProcessorHelper;

    protected int $bufferSize = 0;
    protected int $concurrency = 1;

    protected ?\IteratorAggregate $source = null;

    protected ?Queue $queue = null;

    public function setSource(\IteratorAggregate $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function setConcurrency(int $concurrency): self
    {
        $this->concurrency = $concurrency;
        return $this;
    }

    public function setBufferSize(int $bufferSize): self
    {
        $this->bufferSize = $bufferSize;
        return $this;
    }

    protected function read(ConcurrentIterator $iterator)
    {
        foreach ($iterator as $value) {
            $this->processDataItem($value);
        }
    }

    /**
     * @param DataItem $value
     * @return void
     */
    abstract protected function processDataItem(DataItem $value): void;

    protected function releaseDataItem(DataItem $value): void
    {
        $this->queue->push($value);
    }

    public function getIterator(): \Traversable
    {
        $this->assertSource($this->source);
        if (null === $this->queue) {
            $bufferSize = $this->bufferSize;
            if ($bufferSize === 0) {
                $bufferSize = max($this->concurrency, $this->bufferSize);
            }
            $this->queue = $queue = new Queue($bufferSize);
            $futures = [];
            for ($i=0; $i<$this->concurrency; $i++) {
                $this->assertSourceIterator($iterator = $this->source->getIterator());
                $futures[] = async($this->read(...), $iterator);
            }
            $this->trackQueueFutures($queue, $futures);
        }
        return $this->queue->iterate();
    }

    public function reset(): self
    {
        $this->assertQueueComplete($this->queue);
        $this->queue = null;
        return $this;
    }

    public function run(?callable $itemCallback=null, ?callable $beforeRun=null, ?callable $afterRun=null): void
    {
        if ($beforeRun) $beforeRun();
        foreach ($this as $value) {
            if ($itemCallback) $itemCallback($value);
        }
        if ($afterRun) $afterRun();
    }

}
