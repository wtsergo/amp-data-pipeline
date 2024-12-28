<?php

namespace Wtsergo\AmpDataPipeline\DataCast;

use Amp\Pipeline\Queue;
use Wtsergo\AmpDataPipeline\DataItem\DataItem;

interface MultiCastConsumer
{
    public function __construct(
        CastProcessor $processor,
        Queue $queue,
        array $castProcessors,
        \SplObjectStorage $castResults,
        \SplQueue $waitingQueue,
        /** @var \Closure(DataItem):void */
        \Closure $releaseDataItem,
        bool $groupResults,
        int $groupBufferSize
    );
    public function consume(): void;
}
