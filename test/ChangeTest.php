<?php

namespace test\LinkSet\ChangeSet;

use LinkSet\Change;

class ChangeTest extends \PHPUnit_Framework_TestCase
{
    public function testChange()
    {
        $change = new Change('Old state', 'New state');

        $this->assertEquals('Old state', $change->getOld());
        $this->assertEquals('New state', $change->getNew());
    }
}

