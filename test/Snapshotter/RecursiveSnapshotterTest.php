<?php
namespace Totem\Snapshotter;

use PHPUnit_Framework_TestCase;

use Totem\Snapshot;
use Totem\Snapshotter as SnapshotterInterface;

class RecursiveSnapshotterTest extends PHPUnit_Framework_TestCase
{
    public function testItIsConstructible()
    {
        $snapshotter = new RecursiveSnapshotter;

        $this->assertInstanceOf(SnapshotterInterface::class, $snapshotter);
        $this->assertInstanceOf(RecursiveSnapshotter::class, $snapshotter);
    }

    public function testItSupportsAnythingItsSnapshottersSupports()
    {
        $sub = $this->prophesize(SnapshotterInterface::class);
        $sub->supports([])->willReturn(true)->shouldBeCalled();
        $sub->supports('foo')->willReturn(false)->shouldBeCalled();

        $snapshotter = new RecursiveSnapshotter;
        $snapshotter->addSnapshotter($sub->reveal());

        $this->assertTrue($snapshotter->supports([]), 'The snapshotter supports arrays');
        $this->assertFalse($snapshotter->supports('foo'), 'The main snapshotter doesn\'t support strings');
    }

    public function testGetSupportedSnapshot()
    {
        $snapshotter = new RecursiveSnapshotter;

        $snapshot = $this->prophesize(Snapshot::class);
        $snapshot->getData()->willReturn(['foo' => 'bar'])->shouldBeCalled();
        $snapshot->setData(['foo' => 'bar'])->willReturn($snapshot)->shouldBeCalled();

        $sub = $this->prophesize(SnapshotterInterface::class);
        $sub->supports([])->willReturn(true)->shouldBeCalled();
        $sub->supports('bar')->willReturn(false)->shouldBeCalled();
        $sub->getSnapshot([])->willReturn($snapshot)->shouldBeCalled();

        $snapshotter->addSnapshotter($sub->reveal());

        $snapshot = $snapshotter->getSnapshot([]);

        $this->assertInstanceOf(Snapshot::class, $snapshot);
    }

    /** @expectedException Totem\UnsupportedDataException */
    public function testUnsupportedData()
    {
        $snapshotter = new RecursiveSnapshotter;
        $snapshotter->getSnapshot('foo');
    }
}

