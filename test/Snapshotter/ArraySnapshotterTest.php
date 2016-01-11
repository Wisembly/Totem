<?php
namespace Totem\Snapshotter;

use PHPUnit_Framework_TestCase;

use Totem\Snapshot;
use Totem\Snapshotter;

class ArraySnapshotterTest extends PHPUnit_Framework_TestCase
{
    public function testSupportsOnlyArrays()
    {
        $snapshotter = new ArraySnapshotter;

        $this->assertTrue($snapshotter->supports(['foo']));

        $this->assertFalse($snapshotter->supports('foo'));
        $this->assertFalse($snapshotter->supports((object) 'foo'));
        $this->assertFalse($snapshotter->supports(42));
    }

    public function testItGeneratesANewSnapshot()
    {
        $snapshotter = new ArraySnapshotter;
        $this->assertInstanceOf(Snapshot::class, $snapshotter->getSnapshot([]));
    }

    public function testGeneratedSnapshotsAreComparableIfTheyAreArrays()
    {
        $snapshotter = new ArraySnapshotter;

        $old = $snapshotter->getSnapshot([]);
        $new = $snapshotter->getSnapshot([]);

        $this->assertTrue($old->isComparable($new));
    }

    public function testGeneratedSnapshotsIsNotComparableWithNonArrays()
    {
        $snapshotter = new ArraySnapshotter;

        $prophecy = $this->prophesize(Snapshot::class);
        $prophecy->getRaw()->willReturn('foo')->shouldBeCalled();

        $new = $prophecy->reveal();
        $old = $snapshotter->getSnapshot([]);

        $this->assertFalse($old->isComparable($new));
    }

    public function testImmutable()
    {
        $snapshotter = new ArraySnapshotter;
        $snapshot = $snapshotter->getSnapshot([]);

        $copy = $snapshot->setData(['foo' => 'bar']);

        $this->assertNotSame($snapshot, $copy);
        $this->assertArrayNotHasKey('foo', $snapshot->getData());
        $this->assertArrayHasKey('foo', $copy->getData());
    }

    /** @expectedException Totem\UnsupportedDataException */
    public function testGeneratedSnapshotsFailsOnNonArrays()
    {
        $snapshotter = new ArraySnapshotter;
        $snapshotter->getSnapshot('foo');
    }

    public function testExportAllKeys()
    {
        $array = [
            'foo' => 'foo',
            'bar' => 'bar'
        ];

        $snapshotter = new ArraySnapshotter;
        $snapshot = $snapshotter->getSnapshot($array);
        $data = $snapshot->getData();

        $this->assertCount(2, $data);

        $this->assertArrayHasKey('foo', $data);
        $this->assertArrayHasKey('bar', $data);

        $this->assertSame('foo', $data['foo']);
        $this->assertSame('bar', $data['bar']);
    }
}

