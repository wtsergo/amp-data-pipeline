<?php

namespace Wtsergo\AmpDataPipeline;

use Amp\Pipeline\ConcurrentIterator;
use Wtsergo\AmpDataPipeline\DataItem\DataItem;

/**
* @extends \IteratorAggregate<int, DataItem>
 */
interface DataSource extends \IteratorAggregate
{
    /**
     * @return ConcurrentIterator<DataItem>
     */
    public function getIterator(): ConcurrentIterator;
}
