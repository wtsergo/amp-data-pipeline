<?php

namespace Wtsergo\AmpDataPipeline;

use Wtsergo\AmpDataPipeline\DataItem\DataItem;

interface Processor extends \IteratorAggregate
{
    /**
     * @param \IteratorAggregate $source
     * @return self
     */
    public function setSource(\IteratorAggregate $source): self;

    /**
     * @param int $concurrency
     * @return self
     */
    public function setConcurrency(int $concurrency): self;

    /**
     * @param int $bufferSize
     * @return self
     */
    public function setBufferSize(int $bufferSize): self;

    /**
     * @return \Traversable<int, DataItem>
     */
    public function getIterator(): \Traversable;

    public function reset(): self;

    public function run(?callable $itemCallback=null, ?callable $beforeRun=null, ?callable $afterRun=null): void;
}
