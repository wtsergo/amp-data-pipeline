<?php

namespace Wtsergo\AmpDataPipeline\DataItem;

interface DataItem
{
    public static function fromArray(array $data, array $meta): static;

    public function getMeta($key=null): mixed;
    public function setMeta(array $meta): static;
    public function addMeta(mixed $value, $key): static;

    public function getData($key=null): mixed;
    public function setData(array $data): static;
    public function addData(mixed $value, $key): static;
}
