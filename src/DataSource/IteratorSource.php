<?php

namespace Wtsergo\AmpDataPipeline\DataSource;

use Amp\Pipeline\ConcurrentIterator;
use Amp\Pipeline\Internal\ConcurrentIterableIterator;

class IteratorSource implements \IteratorAggregate
{
    public function __construct(
        private readonly iterable $iterator,
    )
    {
    }

    public function getIterator(): ConcurrentIterator
    {
        return new ConcurrentIterableIterator($this->iterator);
    }
}
