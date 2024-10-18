<?php

namespace App\Serializer;

class CircularReferenceHandler
{
    public function handleCircularReference($object)
    {
        return $object->getId();
    }
}