<?php
namespace test\LinkSet\Snapshot;

use \stdClass;

use \PHPUnit_Framework_TestCase;

use LinkSet\Snapshot\Object;

class ObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException LinkSet\Exception\IncomparableDataException
     */
    public function testDiffWrongOid()
    {
        $snapshot = new Object(new stdClass);
        $snapshot->diff(new Object(new stdClass));
    }

    public function testDiff()
    {
        $object = new stdClass;

        $snapshot = new Object($object);
        $set = $snapshot->diff($snapshot);

        $this->assertInstanceOf('LinkSet\\Set', $set);
    }
}

