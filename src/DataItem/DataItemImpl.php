<?php

namespace Wtsergo\AmpDataPipeline\DataItem;

class DataItemImpl implements DataItem
{
    public function __construct(
        protected array $data = [],
        protected array $meta = []
    ) {
    }

    public static function fromArray(array $data, array $meta = []): self
    {
        return new static(data: $data, meta: $meta);
    }

    public function getData($key=null)
    {
        return $key ? $this->data[$key]??null : $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getMeta($key = null)
    {
        return $key ? $this->meta[$key]??null : $this->meta;
    }

    public function setMeta(array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    public function addMeta(mixed $value, $key): self
    {
        $this->meta[$key] = $value;
        return $this;
    }

    public function addData(mixed $value, $key=null): self
    {
        if ($key === null) {
            $this->data[] = $value;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }


}
