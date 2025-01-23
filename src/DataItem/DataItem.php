<?php

namespace Wtsergo\AmpDataPipeline\DataItem;

interface DataItem
{
    public static function fromArray(array $data, array $meta): self;

    public function getMeta($key=null): mixed;
    public function setMeta(array $meta): self;
    public function addMeta(mixed $value, $key): self;

    public function getData($key=null): mixed;
    public function setData(array $data): self;
    public function addData(mixed $value, $key): self;
}
