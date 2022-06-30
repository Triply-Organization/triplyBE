<?php

namespace App\Transformer;

use App\Entity\Destination;

class DestinationTransformer extends BaseTransformer
{
    private const PARAMS = ['id', 'name'];

    public function toArray(Destination $destination): array
    {
        return $this->transform($destination, static::PARAMS);
    }
}
