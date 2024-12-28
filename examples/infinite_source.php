<?php

namespace {
    include './bootstrap.php';
}

namespace Wtsergo\AmpDataPipeline {

    use Amp\Pipeline\Queue;
    use Revolt\EventLoop;
    use Wtsergo\AmpDataPipeline\DataItem\DataItem;
    use Wtsergo\AmpDataPipeline\DataItem\DataItemImpl;

    class InfiniteSource implements \IteratorAggregate
    {
        protected ?Queue $queue = null;
        public function __construct(
        )
        {
        }

        private function read()
        {
            $idx=0;
            while (true) {
                $this->queue->push(DataItemImpl::fromArray([$idx++]));
            }
            $this->queue->complete();
        }

        public function getIterator(): \Traversable
        {
            if (null === $this->queue) {
                $this->queue = new Queue();
                EventLoop::queue($this->read(...));
            }
            return $this->queue->iterate();
        }
    }

    class EchoProcessor extends ProcessorAbstract
    {
        public function __construct(
            public readonly int $index
        )
        {
        }

        protected function processDataItem(DataItem $value): void
        {
            var_dump($value->getData());
            $this->queue->push($value);
        }
    }

    $echoProcessor = new EchoProcessor(1);
    $echoProcessor->setSource(new InfiniteSource());

    foreach ($echoProcessor as $item) {
        echo '---'.memory_get_usage()."\n";
    }
}
