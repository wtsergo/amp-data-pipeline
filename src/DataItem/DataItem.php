<?php

namespace Wtsergo\AmpDataPipeline\DataItem;

interface DataItem
{
    public static function fromArray(array $data, array $meta): self;

    public function getMeta($key=null);
    public function setMeta(array $meta): self;
    public function addMeta(mixed $value, $key): self;

    public function getData($key=null);
    public function setData(array $data): self;
    public function addData(mixed $value, $key): self;
}
