<?php

namespace Wtsergo\AmpDataPipeline\DataSource;

class IteratorSource implements \IteratorAggregate
{
    public function __construct(
        private readonly \Traversable $iterator,
    )
    {
    }

    public function getIterator(): \Traversable
    {
        return $this->iterator;
    }
}
