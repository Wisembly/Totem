<?php

namespace Taluu\Domain\ChangeSet;

interface ChangeInterface extends ApiAbleInterface
{
    /**
     * Transform this object into an array
     * 
     * @param boolean $short short format or more verbose (more data) ?
     * @return array
     */
    public function toArray($short = true);
}
