<?php

namespace Taluu\Domain\ChangeSet;

/**
 * Represents a change into the entity
 *
 * @author Rémy Gazelot <remy@wisembly.com>
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Change implements ChangeInterface {
    private $old; // old state
    private $new; // new state

    public function __construct($old, $new)
    {
        $this->old = $old;
        $this->new = $new;
    }

    public function getOld()
    {
        return $this->old;
    }

    public function getNew()
    {
        return $this->new;
    }

    public function toArray($short = true)
    {
        return ['old' => $this->old,
                'new' => $this->new];
    }
}
