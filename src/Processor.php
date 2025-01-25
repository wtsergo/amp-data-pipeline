<?php

namespace Wtsergo\AmpDataPipeline;

use Amp\Pipeline\ConcurrentIterator;
use Wtsergo\AmpDataPipeline\DataItem\DataItem;

interface Processor extends DataSource
{
    /**
     * @param DataSource $source
     * @return self
     */
    public function setSource(DataSource $source): self;

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
     * @return ConcurrentIterator<DataItem>
     */
    public function getIterator(): ConcurrentIterator;

    public function reset(): self;

    public function run(?callable $itemCallback=null, ?callable $beforeRun=null, ?callable $afterRun=null): void;
}
