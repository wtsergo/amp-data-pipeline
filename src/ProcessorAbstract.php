<?php

namespace Wtsergo\AmpDataPipeline;

use Amp\Pipeline\ConcurrentIterator;
use Amp\Pipeline\Internal\ConcurrentQueueIterator;
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

    protected ?DataSource $source = null;

    /**
     * @var Queue<DataItem>|null
     */
    protected ?Queue $queue = null;

    /**
     * @var ConcurrentQueueIterator<DataItem>|null
     */
    private ?ConcurrentQueueIterator $iterator;

    public function setSource(DataSource $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function getSource(): DataSource
    {
        return $this->source ?? throw new \RuntimeException('Undefined processor source');
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

    /**
     * @param ConcurrentIterator<DataItem> $iterator
     * @return void
     */
    protected function read(ConcurrentIterator $iterator): void
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

    public function getIterator(): ConcurrentIterator
    {
        if (!isset($this->queue)) {
            $bufferSize = $this->bufferSize;
            if ($bufferSize === 0) {
                $bufferSize = max($this->concurrency, $this->bufferSize);
            }
            $this->queue = $queue = new Queue($bufferSize);
            $this->iterator = $this->queue->iterate();
            $futures = [];
            for ($i=0; $i<$this->concurrency; $i++) {
                $futures[] = async($this->read(...), $this->getSource()->getIterator());
            }
            $this->trackQueueFutures($queue, $futures);
        }
        return $this->iterator;
    }

    public function reset(): self
    {
        $this->assertQueueComplete($this->queue);
        $this->queue = null;
        $this->iterator = null;
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
