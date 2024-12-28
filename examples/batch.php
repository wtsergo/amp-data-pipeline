<?php

namespace {
    include './bootstrap.php';
}

namespace Wtsergo\AmpDataPipeline {

    use Amp\Cancellation;
    use Amp\Future;
    use Amp\Pipeline\Queue;
    use Wtsergo\AmpDataPipeline\Batch\BatchProcessor;
    use Wtsergo\AmpDataPipeline\DataCast\CastProcessor;
    use Wtsergo\AmpDataPipeline\DataCast\MultiCastProcessor;
    use Wtsergo\AmpDataPipeline\DataItem\DataItem;
    use Wtsergo\AmpDataPipeline\DataItem\DataItemHandler;
    use Wtsergo\AmpDataPipeline\DataItem\DataItemImpl;
    use Wtsergo\AmpDataPipeline\DataItem\HandlerComposition;
    use Wtsergo\AmpDataPipeline\DataSource\ArraySource;
    use Wtsergo\AmpDataPipeline\Helper\ProcessorAssertion;
    use function Amp\delay;

    class ExtractColumns implements CastProcessor {
        use ProcessorAssertion;
        public function __construct(
            public readonly array $columns
        ) {
        }

        public function cast(\IteratorAggregate $source, \Closure $acceptCastItem): void
        {
            $this->assertSourceIterator($iterator = $source->getIterator());
            $result = DataItemImpl::fromArray([]);
            $result->addMeta($this->columns, 'columns');
            /** @var DataItem $item */
            $i=0;
            $queue = new Queue(10);
            $acceptCastItem($queue->iterate());
            foreach ($iterator as $item) {
                $data = $item->getData();
                foreach ($this->columns as $column) {
                    $result->addData($data[$column]??null);
                }
                if (++$i%3==0) {
                    delay(rand(1,10)*.01);
                    $queue->push($result);
                    $result = DataItemImpl::fromArray([]);
                    $result->addMeta($this->columns, 'columns');
                }
            }
            delay(rand(1,10)*.01);
            $queue->push($result);
            $queue->complete();
        }
    }

    class EchoHandler implements DataItemHandler {
        public function __construct(
            public readonly array $columns
        ) {
        }
        public function canHandle(DataItem $value): bool
        {
            return (bool)array_intersect($value->getMeta('columns')??[], $this->columns);
        }

        public function handle(DataItem $value): Future
        {
            static $i=0;
            return Future::complete($value);
        }

    }

    $multicast = new MultiCastProcessor([
        fn() => new ExtractColumns([0]),
        fn() => new ExtractColumns([1,2]),
    ]);

    $multicastFactory = fn() => new MultiCastProcessor([
        fn() => new ExtractColumns([0]),
        fn() => new ExtractColumns([1]),
    ]);

    $handlerFactory = fn(bool $ordered) => new HandlerComposition(
        [
            new EchoHandler([0]),
            new EchoHandler([1])
        ],
        $ordered
    );

    $batch = new BatchProcessor(
        $multicastFactory,//->setConcurrency(3),
        $handlerFactory,
        batchSize: 1,
        ordered: true,
        groupResults: true
    );

    $composition = new ProcessorComposition([
        $batch->setConcurrency(3)
    ]);

    $input = [];

    for ($i = 0; $i < 50; $i++) {
        //$input[] = DataItemImpl::fromArray(['aa'.$i,'bb'.$i,'cc'.$i]);
        $input[] = DataItemImpl::fromArray(['aa'.$i,'bb'.$i]);
    }

    $composition->setSource(new ArraySource($input));

    foreach ($composition as $value) {
        var_dump($value);
    }

    echo "\n\n----------END-----------\n\n";

}
