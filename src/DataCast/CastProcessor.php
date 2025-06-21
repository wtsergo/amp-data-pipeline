<?php

namespace Wtsergo\AmpDataPipeline\DataCast;

use Amp\Pipeline\ConcurrentIterator;
use Wtsergo\AmpDataPipeline\DataItem\DataItem;
use Wtsergo\AmpDataPipeline\DataSource;

interface CastProcessor
{
    /**
     * @param \Closure(ConcurrentIterator,?DataItem $origItem,?CastProcessor $castProcessor):void $acceptCastItem
     * @return void
     */
    public function cast(ConcurrentIterator $source, \Closure $acceptCastItem): void;
}
