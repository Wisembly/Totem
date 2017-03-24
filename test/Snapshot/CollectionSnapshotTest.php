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
        $snapshot = new CollectionSnapshot([], [], [], false);
        $this->assertInstanceOf(MutableSnapshot::class, $snapshot);
    }

    public function testItIsMutableIfDeclaredAsMutable()
    {
        $snapshot = new CollectionSnapshot(['foo'], [new Snapshot('foo', ['foo'])], [], true);
        $this->assertTrue($snapshot->isMutable());
    }

    /** @expectedException Totem\Snapshot\ImmutableException */
    public function testMutateOnImmutableShouldThrowException()
    {
        $recursive = new RecursiveSnapshotter;
        $collection = new CollectionSnapshot(['foo'], [new Snapshot('foo', ['foo'])], [], false);

        $collection->mutate($recursive);
    }

    public function testMutate()
    {
        $recursive = new RecursiveSnapshotter;

        $snapshot = $this->prophesize(Snapshot::class);
        $snapshot->willImplement(MutableSnapshot::class);
        $snapshot->willBeConstructedWith(['foo', ['bar' => 'baz']]);
        $snapshot->mutate($recursive)->willReturn(new Snapshot('foo', ['foo' => 'bar']))->shouldBeCalled();

        $collectionSnapshot = new CollectionSnapshot(['foo'], [$snapshot->reveal()], [], true);
        $mutatedSnapshot = $collectionSnapshot->mutate($recursive);

        $this->assertInstanceOf(CollectionSnapshot::class, $mutatedSnapshot);
        $this->assertNotSame($mutatedSnapshot, $collectionSnapshot);
    }

    public function testItIsComparableToOtherCollectionSnapshots()
    {
        $snapshot = new CollectionSnapshot([], [], [], false);
        $reference = new CollectionSnapshot([], [], [], false);

        $notCollection = new Snapshot(null, []);

        $this->assertTrue($snapshot->isComparable($reference));
        $this->assertFalse($snapshot->isComparable($notCollection));
    }

    /** @expectedException InvalidArgumentException */
    public function testItThrowsAnExceptionOnInvalidKey()
    {
        $snapshot = new CollectionSnapshot([], [], [], false);
        $snapshot->getOriginalKey('foo');
    }

    public function testItReturnsOriginalKey()
    {
        $snapshot = new CollectionSnapshot([], [], ['foo' => 1], false);

        $this->assertSame(1, $snapshot->getOriginalKey('foo'));
    }
}

