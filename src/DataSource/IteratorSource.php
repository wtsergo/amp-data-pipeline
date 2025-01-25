<?php

namespace Wtsergo\AmpDataPipeline\DataSource;

use Amp\Pipeline\ConcurrentIterator;
use Amp\Pipeline\Internal\ConcurrentIterableIterator;
use Wtsergo\AmpDataPipeline\DataItem\DataItem;
use Wtsergo\AmpDataPipeline\DataSource;

class IteratorSource implements DataSource
{
    /**
     * @var ConcurrentIterableIterator<DataItem>
     */
    protected ConcurrentIterableIterator $iterator;

    public function __construct(
        iterable $iterator,
    )
    {
        $this->iterator = new ConcurrentIterableIterator($iterator);
    }

    public function getIterator(): ConcurrentIterator
    {
        return $this->iterator;
    }
}
