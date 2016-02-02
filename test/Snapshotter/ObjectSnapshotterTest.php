<?php
namespace Totem\Snapshotter;

use PHPUnit_Framework_TestCase;

use Totem\Snapshot;
use Totem\Snapshotter;

class ObjectSnapshotterTest extends PHPUnit_Framework_TestCase
{
    public function testSupportsOnlyObjects()
    {
        $snapshotter = new ObjectSnapshotter;

        $this->assertTrue($snapshotter->supports((object) 'foo'));

        $this->assertFalse($snapshotter->supports('foo'));
        $this->assertFalse($snapshotter->supports(['foo']));
        $this->assertFalse($snapshotter->supports(42));
    }

    public function testItGeneratesANewSnapshot()
    {
        $object = new class {};
        $snapshotter = new ObjectSnapshotter;

        $this->assertInstanceOf(Snapshot::class, $snapshotter->getSnapshot($object));
    }

    public function testGeneratedSnapshotsAreComparableIfTheSameObjectIsCompared()
    {
        $object = new class {};
        $snapshotter = new ObjectSnapshotter;

        $old = $snapshotter->getSnapshot($object);
        $new = $snapshotter->getSnapshot($object);

        $this->assertTrue($old->isComparable($new));
    }

    public function testGeneratedSnapshotsAreDifferentForDifferentObjects()
    {
        $snapshotter = new ObjectSnapshotter;

        $old = $snapshotter->getSnapshot(new class {});
        $new = $snapshotter->getSnapshot(new class {});

        $this->assertFalse($old->isComparable($new));
    }

    public function testGeneratedSnapshotsIsNotComparableWithNonObjects()
    {
        $snapshotter = new ObjectSnapshotter;

        $new = new Snapshot('foo', []);
        $old = $snapshotter->getSnapshot(new class {});

        $this->assertFalse($old->isComparable($new));
    }

    public function testDataChangerChangesData()
    {
        $snapshotter = new ObjectSnapshotter;
        $snapshot = $snapshotter->getSnapshot(new class {});

        $snapshotter->setData($snapshot, ['foo' => 'bar']);

        $this->assertArrayHasKey('foo', $snapshot->getData());
    }

    /** @expectedException Totem\UnsupportedDataException */
    public function testGeneratedSnapshotsFailsOnNonObjects()
    {
        $snapshotter = new ObjectSnapshotter;
        $snapshotter->getSnapshot('foo');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Expected an object, got string
     */
    public function testGeneratedSnapshotFailsOnNonObjectsAfterConstruction()
    {
        $snapshotter = new ObjectSnapshotter;
        $snapshot = $snapshotter->getSnapshot(new class {});

        $snapshot->__construct('foo', []);
    }

    public function testExportAllProperties()
    {
        $object = new class {
            public $foo = 'foo';
            protected $bar = 'bar';
            private $baz = 'baz';
        };

        $snapshotter = new ObjectSnapshotter;
        $snapshot = $snapshotter->getSnapshot($object);
        $data = $snapshot->getData();

        $this->assertCount(3, $data);

        $this->assertArrayHasKey('foo', $data);
        $this->assertArrayHasKey('bar', $data);
        $this->assertArrayHasKey('baz', $data);

        $this->assertSame('foo', $data['foo']);
        $this->assertSame('bar', $data['bar']);
        $this->assertSame('baz', $data['baz']);
    }
}

