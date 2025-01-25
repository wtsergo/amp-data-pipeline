<?php

namespace Wtsergo\AmpDataPipeline\DataCast;

use Amp\Pipeline\Queue;
use Wtsergo\AmpDataPipeline\DataSource;
use Wtsergo\AmpDataPipeline\Helper\ProcessorAssertion;

class SkipCastProcessor implements CastProcessor
{
    use ProcessorAssertion;
    public function cast(DataSource $source, \Closure $acceptCastItem): void
    {
        $this->assertSourceIterator($iterator = $source->getIterator());
        foreach ($iterator as $item) {
            $queue = new Queue();
            $acceptCastItem($queue->iterate());
            $queue->push($item);
            $queue->complete();
        }
    }
}
