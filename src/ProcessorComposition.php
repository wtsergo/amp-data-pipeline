<?php

namespace Wtsergo\AmpDataPipeline;

use Wtsergo\AmpDataPipeline\DataItem\DataItem;
use Wtsergo\AmpDataPipeline\Helper\ProcessorAssertion;
use Wtsergo\AmpDataPipeline\Helper\ProcessorHelper;

class ProcessorComposition extends ProcessorAbstract
{
    use ProcessorAssertion;
    use ProcessorHelper;

    protected ?\IteratorAggregate $lastSource = null;

    /**
     * @param list<Processor> $processors
     */
    public function __construct(
        public readonly array $processors
    )
    {
    }

    /**
     * @param list<Processor> $processors
     */
    public static function selfCreate(array $processors): self
    {
        return new self($processors);
    }

    protected function processDataItem(DataItem $value): void
    {
    }

    public function getIterator(): \Traversable
    {
        $this->assertSource($this->source);
        if ($this->lastSource === null) {
            $this->lastSource = $this->source;
            foreach ($this->processors as $processor) {
                $processor->setSource($this->lastSource);
                $this->lastSource = $processor;
            }
        }
        return $this->lastSource->getIterator();
    }

    public function reset(): self
    {
        foreach ($this->processors as $processor) {
            $processor->reset();
        }
        $this->lastSource = null;
        return $this;
    }

}
