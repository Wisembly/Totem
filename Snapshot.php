<?php

namespace Taluu\Domain\ChangeSet;

use \InvalidArgumentException;

/**
 * Represents a snapshot of an object at a given time
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Snapshot
{
    /** @var string object's hash */
    private $oid;

    /** @var array object's array */
    private $array;

    public function __construct(ChangeInterface $object)
    {
        $this->array = $object->toArray(false);
        $this->oid   = spl_object_hash($object);
    }

    public function diff(self $data)
    {
        if ($data->oid !== $this->oid) {
            throw new InvalidArgumentException('this object is not comparable with the base');
        }

        return new Set($this->array, $data->array);
    }
}
