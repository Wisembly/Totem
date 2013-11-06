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

use \Countable,
    \ArrayAccess,
    \ArrayIterator,
    \IteratorAggregate,

    \OutOfBoundsException,
    \InvalidArgumentException;

use Totem\Snapshot\ObjectSnapshot,
    Totem\Exception\IncomparableDataException;

/**
 * Represents a changeset
 *
 * @author Rémy Gazelot <rgazelot@gmail.com>
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
class Set implements ArrayAccess, Countable, ChangeInterface
{
    private $old;
    private $new;

    private $changes = null;

    public function __construct(array $old, array $new)
    {
        $this->old = $old;
        $this->new = $new;

        $this->compute($old, $new);
    }

    /**
     * Retrieve a property change
     *
     * @param  string $property
     *
     * @return ChangeInterface Set if it was a recursive change, Change otherwise
     * @throws OutOfBoundsException The property doesn't exist or wasn't changed
     */
    public function getChange($property)
    {
        if (!$this->hasChanged($property)) {
            throw new OutOfBoundsException('This property doesn\'t exist or wasn\'t changed');
        }

        return $this->changes[$property];
    }

    /**
     * Test if the given property has been changed
     *
     * @param  string  $property
     *
     * @return boolean
     */
    public function hasChanged($property)
    {
        return isset($this->changes[$property]);
    }

    /** {@inheritDoc} */
    public function offsetExists($offset)
    {
        return $this->hasChanged($offset);
    }

    /** {@inheritDoc} */
    public function offsetGet($offset)
    {
        return $this->getChange($offset);
    }

    /** {@inheritDoc} */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException('You cannot alter a changeset once it has been calculated');
    }

    /** {@inheritDoc} */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('You cannot alter a changeset once it has been calculated');
    }

    /** {@inheritDoc} */
    public function count()
    {
        return count($this->changes);
    }

    /** Gets the snapshot the new one is compared to */
    public function getOld()
    {
        return $this->old;
    }

    /** Gets the last snapshot */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * Calculate the changeset between two arrays
     *
     * Both arrays must have the same keys and the same size
     *
     * @param array $old Old array
     * @param array $new New array
     *
     * @internal
     * @throws InvalidArgumentException If the two arrays does not have the same keys
     */
    protected function compute(array $old, array $new)
    {
        if (array_keys($old) !== array_keys($new)) {
            throw new \InvalidArgumentException('You should compare two arrays with the same keys !');
        }

        $this->changes = [];

        foreach (array_keys($new) as $key) {
            // -- if it is not the same type, then we may consider it changed
            if (gettype($old[$key]) !== gettype($new[$key])) {
                $this->changes[$key] = new Change($old[$key], $new[$key]);
                continue;
            }

            // -- if it is an object, try to check the hashes and then the diff
            if (is_object($old[$key])) {
                try {
                    $oldSnapshot = new ObjectSnapshot($old[$key]);
                    $newSnapshot = new ObjectSnapshot($new[$key]);

                    $set = $oldSnapshot->diff($newSnapshot);

                    if (0 === count($set)) {
                        continue;
                    }

                    $this->changes[$key] = $set;
                } catch (IncomparableDataException $e) {
                    $this->changes[$key] = new Change($old[$key], $new[$key]);
                }

                continue;
            }

            // -- if it is an array several step to check up
            if (is_array($old[$key])) {
                // -- not the same size ? Then it changed, completely
                if (array_keys($old[$key]) !== array_keys($new[$key])) {
                    $this->changes[$key] = new Change($old[$key], $new[$key]);
                    continue;
                }

                // -- same size / same keys ; return only what has changed
                $set = new static($old[$key], $new[$key]);

                if (0 !== count($set)) {
                    $this->changes[$key] = $set;
                }

                continue;
            }

            // -- eventually, check if the two elements are equal
            if ($old[$key] !== $new[$key]) {
                $this->changes[$key] = new Change($old[$key], $new[$key]);
            }
        }
    }
}
