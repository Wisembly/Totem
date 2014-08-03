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

namespace Totem\Snapshot;

use ArrayObject,
    ReflectionProperty;

use PHPUnit_Framework_TestCase;

class CollectionSnapshotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage An array or a Traversable was expected to take a snapshot of a collection, "string" given
     */
    public function testSnapshotNotArray()
    {
        new CollectionSnapshot('foo', 'bar', ['snapshotClass' => 'Totem\\Snapshot']);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The snapshot class "Totem\Fubar" does not seem to be loadable
     */
    public function testSnapshotClassNotLoadable()
    {
        new CollectionSnapshot('foo', 'bar', ['snapshotClass' => 'Totem\\Fubar']);
    }

    /**
     * @dataProvider snapshotClassWrongReflectionProvider
     *
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage A Snapshot Class should be instantiable and extends abstract class Totem\AbstractSnapshot
     */
    public function testSnapshotWrongReflection($class)
    {
        new CollectionSnapshot('foo', 'bar', ['snapshotClass' => $class]);
    }

    public function snapshotClassWrongReflectionProvider()
    {
        return [['Totem\\AbstractSnapshot'],
                ['stdClass']];
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The given array / Traversable is not a collection as it contains non numeric keys
     */
    public function testNonCollection()
    {
        new CollectionSnapshot(new ArrayObject(['foo' => 'bar']), 'bar');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The key "baz" is not defined or readable in one of the elements of the collection
     */
    public function testKeyNotReadable()
    {
        new CollectionSnapshot([['foo' => 'bar']], 'baz');
    }

    /** @dataProvider allValidProvider */
    public function testAllValid($hasSnapshot)
    {
        $options = [];
        $class   = 'Totem\\Snapshot\\ArraySnapshot';

        if (true === $hasSnapshot) {
            $class                    = 'Totem\\Snapshot';
            $options['snapshotClass'] = 'Totem\\Snapshot';
        }

        $snapshot = new CollectionSnapshot([['foo' => 'bar', 'baz' => 'fubar']], '[foo]', $options);

        $refl = new ReflectionProperty('Totem\\AbstractSnapshot', 'data');
        $refl->setAccessible(true);

        $this->assertArrayHasKey('bar', $refl->getValue($snapshot));
        $this->assertInstanceOf($class, $refl->getValue($snapshot)['bar']);
    }

    public function allValidProvider()
    {
        return [[true],
                [false]];
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The primary key "baz" is not in the computed dataset
     */
    public function testOriginalKeyNotFound()
    {
        $snapshot = new CollectionSnapshot([['foo' => 'bar']], '[foo]');
        $snapshot->getOriginalKey('baz');
    }

    public function testOriginalKey()
    {
        $snapshot = new CollectionSnapshot([['foo' => 'bar']], '[foo]');

        $this->assertSame(0, $snapshot->getOriginalKey('bar'));
    }
}

