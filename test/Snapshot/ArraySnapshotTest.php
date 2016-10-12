<?php
namespace Totem\Snapshot;

use PHPUnit_Framework_TestCase;

use Prophecy\Argument;

use Totem\Snapshot;
use Totem\Snapshotter;
use Totem\Snapshotter\RecursiveSnapshotter;

class ArraySnapshotTest extends PHPUnit_Framework_TestCase
{
    public function testItImplementsMutableInterface()
    {
        $snapshot = new ArraySnapshot([]);
        $this->assertInstanceOf(MutableSnapshot::class, $snapshot);
    }

    public function testItIsMutable()
    {
        $snapshot = new ArraySnapshot([]);
        $this->assertTrue($snapshot->isMutable());
    }

    public function testMutate()
    {
        $array = [
            'foo' => 'foo',
            'bar' => 'bar'
        ];

        $snapshotter = $this->prophesize(Snapshotter::class);
        $snapshotter->supports(Argument::cetera())->willReturn(true);
        $snapshotter->getSnapshot(Argument::cetera())->willReturn(new Snapshot('foo', ['bar' => 'baz']));
        $snapshotter = $snapshotter->reveal();

        $recursive = new RecursiveSnapshotter;
        $recursive->addSnapshotter($snapshotter);

        $snapshot = new ArraySnapshot($array);
        $snapshot = $snapshot->mutate($recursive);

        $this->assertInstanceOf(ArraySnapshot::class, $snapshot);

        foreach ($snapshot->getData() as $data) {
            $this->assertInstanceOf(Snapshot::class, $data);
        }
    }
}

