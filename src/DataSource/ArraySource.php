<?php

namespace Wtsergo\AmpDataPipeline\DataSource;

use Amp\Pipeline\ConcurrentIterator;
use Amp\Pipeline\Internal\ConcurrentArrayIterator;
use Wtsergo\AmpDataPipeline\DataItem\DataItem;
use Wtsergo\AmpDataPipeline\DataSource;

class ArraySource implements DataSource
{
    /**
     * @var ConcurrentArrayIterator<DataItem>
     */
    private ConcurrentArrayIterator $iterator;

    public function __construct(array $values)
    {
        $this->iterator = new ConcurrentArrayIterator($values);
    }

    public function getIterator(): ConcurrentIterator
    {
        return $this->iterator;
    }
}
