<?php

namespace Wtsergo\AmpDataPipeline\DataCast;

use Amp\Pipeline\ConcurrentIterator;
use Wtsergo\AmpDataPipeline\DataItem\DataItem;

interface CastProcessor
{
    /**
     * @param \IteratorAggregate<int,DataItem> $source
     * @param \Closure(ConcurrentIterator,?DataItem $origItem,?CastProcessor $castProcessor):void $acceptCastItem
     * @return void
     */
    public function cast(\IteratorAggregate $source, \Closure $acceptCastItem): void;
}
