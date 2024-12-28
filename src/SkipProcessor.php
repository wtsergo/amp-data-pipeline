<?php

namespace Wtsergo\AmpDataPipeline;

use Wtsergo\AmpDataPipeline\DataItem\DataItem;

class SkipProcessor extends ProcessorAbstract
{
    protected function processDataItem(DataItem $value): void
    {
        $this->releaseDataItem($value);
    }
}
