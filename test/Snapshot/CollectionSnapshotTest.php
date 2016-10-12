<?php
namespace Totem\Snapshot;

use PHPUnit_Framework_TestCase;

use Prophecy\Argument;

use Totem\Snapshot;
use Totem\Snapshotter;
use Totem\Snapshotter\RecursiveSnapshotter;

class CollectionSnapshotTest extends PHPUnit_Framework_TestCase
{
    public function testItImplementsMutableInterface()
    {
        $snapshot = new CollectionSnapshot([], [], []);
        $this->assertInstanceOf(MutableSnapshot::class, $snapshot);
    }

    public function testItIsMutableIfSnapshotIsMutable()
    {
        $snapshot = $this->prophesize(Snapshot::class);
        $snapshot->willImplement(MutableSnapshot::class);
        $snapshot->willBeConstructedWith(['foo', ['bar' => 'baz']]);
        $snapshot->isMutable()->willReturn(true)->shouldBeCalled();

        $snapshot = new CollectionSnapshot(['foo'], [$snapshot->reveal()], []);
        $this->assertTrue($snapshot->isMutable());
    }

    public function testItIsNotMutableIfSnapshotIsNotMutable()
    {
        $snapshot = $this->prophesize(Snapshot::class);
        $snapshot->willImplement(MutableSnapshot::class);
        $snapshot->willBeConstructedWith(['foo', ['bar' => 'baz']]);
        $snapshot->isMutable()->willReturn(false)->shouldBeCalled();

        $snapshot = new CollectionSnapshot(['foo'], [$snapshot->reveal()], []);
        $this->assertFalse($snapshot->isMutable());
    }

    /** @expectedException Totem\Snapshot\ImmutableException */
    public function testMutateOnImmutableShouldThrowException()
    {
        $recursive = new RecursiveSnapshotter;
        $collection = new CollectionSnapshot(['foo'], [new Snapshot('foo', ['foo'])], []);

        $collection->mutate($recursive);
    }

    public function testMutate()
    {
        $recursive = new RecursiveSnapshotter;

        $snapshot = $this->prophesize(Snapshot::class);
        $snapshot->willImplement(MutableSnapshot::class);
        $snapshot->willBeConstructedWith(['foo', ['bar' => 'baz']]);
        $snapshot->isMutable()->willReturn(true)->shouldBeCalled();
        $snapshot->mutate($recursive)->willReturn($snapshot)->shouldBeCalled();

        $snapshotter = $this->prophesize(Snapshotter::class);
        $snapshotter->supports(Argument::cetera())->willReturn(true);
        $snapshotter->getSnapshot(Argument::cetera())->willReturn($snapshot);
        $snapshotter = $snapshotter->reveal();

        $recursive->addSnapshotter($snapshotter);

        $snapshot = new CollectionSnapshot(['foo'], [$snapshot->reveal()], []);
        $snapshot = $snapshot->mutate($recursive);

        $this->assertInstanceOf(CollectionSnapshot::class, $snapshot);
    }
}

