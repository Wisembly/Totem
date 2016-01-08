<?php
/**
 * This file is part of the Totem package
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 *
 * @copyright Baptiste Clavié <clavie.b@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT-License MIT License
 */

namespace Totem;

use stdClass;

use PHPUnit_Framework_TestCase;

use Totem\Snapshot\ArraySnapshot;
use Totem\Snapshot\ObjectSnapshot;
use Totem\Snapshot\CollectionSnapshot;

class SetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invalidEntryProvider
     */
    public function testSetChangesWithChangedStructure($old, $new, $class)
    {
        $old = new class($old) extends AbstractSnapshot {
            public function __construct($data) {
                $this->data = $data;
            }
        };

        $new = new class($new) extends AbstractSnapshot {
            public function __construct($data) {
                $this->data = $data;
            }
        };

        $set = new Set($old, $new);

        $this->assertInstanceOf('Totem\\Change\\' . $class, $set->getChange('1'));
    }

    public function invalidEntryProvider()
    {
        return [
            'addition' => [
                ['foo'],
                ['foo', 'bar'],
                'Addition'
            ],

            'removal' => [
                ['foo', 'bar'],
                ['foo'],
                'Removal'
            ]
        ];
    }

    public function testHasChanged()
    {
        $old = new class('foo', 'bar') extends AbstractSnapshot {
            public function __construct($foo, $bar) {
                $this->data = [
                    'foo' => $foo,
                    'bar' => $bar
                ];
            }
        };

        $new = new class('foo', 'baz') extends AbstractSnapshot {
            public function __construct($foo, $bar) {
                $this->data = [
                    'foo' => $foo,
                    'bar' => $bar
                ];
            }
        };

        $set = new Set($old, $new);

        $this->assertFalse($set->hasChanged('foo'));
        $this->assertFalse(isset($set['foo']));
        $this->assertTrue($set->hasChanged('bar'));
        $this->assertTrue(isset($set['bar']));
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testGetChangeWithInvalidProperty()
    {
        $old = new class extends AbstractSnapshot {
            public function __construct() {
                $this->data = ['foo' => 'bar'];
            }
        };

        $set = new Set($old, $old);
        $set->getChange('foo');
    }

    // @todo to break up
    public function testGetChange()
    {
        $o = [new stdClass, (object) ['foo' => 'bar']];

        $old = $new = ['foo'    => 'foo',
                       'bar'    => new ArraySnapshot(['foo', 'bar']),
                       'baz'    => new ObjectSnapshot($o[0]),
                       'qux'    => 'foo',
                       'fubar'  => new ObjectSnapshot($o[1]),
                       'fubaz'  => new ArraySnapshot(['foo', 'bar']),
                       'fuqux'  => new ArraySnapshot(['foo']),
                       'kludge' => new ArraySnapshot(['foo']),
                       'xyzzy'  => new ArraySnapshot(['foo'])];

        $o[1]->foo = 'baz';

        $new['foo']    = 'bar';
        $new['bar']    = new ArraySnapshot(['foo', 'baz']);
        $new['baz']    = new ObjectSnapshot($o[0]);
        $new['qux']    = 42;
        $new['fubar']  = new ObjectSnapshot($o[1]);
        $new['fubaz']  = new ArraySnapshot(['foo', 'bar', 'baz']);
        $new['fuqux']  = new ObjectSnapshot((object) []);
        $new['kludge'] = 42;
        $new['xyzzy']  = (object) [];

        $old = new class($old) extends AbstractSnapshot {
            public function __construct($old) {
                $this->data = $old;
            }
        };

        $new = new class($new) extends AbstractSnapshot {
            public function __construct($new) {
                $this->data = $new;
            }
        };

        $set = new Set($old, $new);

        $this->assertInstanceOf('Totem\\Change\\Modification', $set->getChange('fuqux'));
        $this->assertInstanceOf('Totem\\Change\\Modification', $set->getChange('foo'));
        $this->assertInstanceOf('Totem\\Set', $set->getChange('bar'));
        $this->assertInstanceOf('Totem\\Change\\Modification', $set['foo']);
        $this->assertInstanceOf('Totem\\Set', $set->getChange('fubar'));
        $this->assertInstanceOf('Totem\\Change\\Modification', $set->getChange('fubar')->getChange('foo'));
        $this->assertInstanceOf('Totem\\Change\\Modification', $set->getChange('kludge'));
        $this->assertInstanceOf('Totem\\Change\\Modification', $set->getChange('xyzzy'));
    }

    public function testIterator()
    {
        $snapshot = new class extends AbstractSnapshot {
            public function __construct() {
                $this->data = ['foo' => 'bar'];
            }
        };

        $set = new Set($snapshot, $snapshot);
        $this->assertInstanceOf('ArrayIterator', $set->getIterator());
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testForbidenSetter()
    {
        $set = new Set;
        $set[] = 'baz';
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testForbidenUnsetter()
    {
        $set = new Set;
        unset($set[0]);
    }

    /**
     * @expectedException         RuntimeException
     * @expectedExceptionMessage  The changeset was not computed yet !
     */
    public function testHasChangedNotComputedShouldThrowException()
    {
        $set = new Set;
        $set->hasChanged('foo');
    }

    /**
     * @expectedException         RuntimeException
     * @expectedExceptionMessage  The changeset was not computed yet !
     */
    public function testNotComputedCountShouldThrowException()
    {
        $set = new Set;
        count($set);
    }

    /**
     * @expectedException         RuntimeException
     * @expectedExceptionMessage  The changeset was not computed yet !
     */
    public function testNotComputedIteratorShouldThrowException()
    {
        $set = new Set;
        $set->getIterator();
    }

    public function testAlreadyComputedSetShouldNotRecompute()
    {
        $snapshot = new class extends AbstractSnapshot {};

        $set = new Set($snapshot, $snapshot);
        $set->compute($snapshot, $snapshot); // force recomputation
    }

    public function testComputeCollections()
    {
        $old = $new = [['foo' => 'bar', 'baz' => 'fubar'], ['foo' => 'baz', 'baz' => 'fubar']];
        $new[0]['baz'] = 'fubaz';

        $old = new CollectionSnapshot($old, '[foo]');
        $new = new CollectionSnapshot($new, '[foo]');

        $set = new Set;
        $set->compute($old, $new);

        $this->assertContainsOnly('integer', array_keys(iterator_to_array($set)));
    }

    /** @dataProvider unaffectedSnapshotComputerProvider */
    public function testUnaffectedCollections(AbstractSnapshot $origin, AbstractSnapshot $upstream)
    {
        $set = new Set($origin, $upstream);

        $this->assertNotContainsOnly('integer',array_keys(iterator_to_array($set)));
    }

    public function unaffectedSnapshotComputerProvider()
    {
        $old = ['foo' => 'bar', 'baz' => 'fubar'];

        return [[new CollectionSnapshot([$old], '[foo]'), new ArraySnapshot($old)],
                [new ArraySnapshot($old), new CollectionSnapshot([$old], '[foo]')],
                [new ArraySnapshot($old), new ArraySnapshot(array_merge($old, ['baz' => 'fubaz']))]];
    }
}

