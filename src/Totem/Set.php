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
    \BadMethodCallException,
    \InvalidArgumentException;

use Totem\AbstractSnapshot,
    Totem\Exception\IncomparableDataException,

    Totem\Change\Addition,
    Totem\Change\Deletion,
    Totem\Change\Modification,

    Totem\Snapshot\ArraySnapshot,
    Totem\Snapshot\ObjectSnapshot;

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

    public function __construct(AbstractSnapshot $old, AbstractSnapshot $new)
    {
        $this->old = $old;
        $this->new = $new;

        $this->compute();
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

    /**
     * {@inheritDoc}
     *
     * @throws BadMethodCallException if a unset is tried on a snapshot property
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException('You cannot alter a changeset once it has been calculated');
    }

    /**
     * {@inheritDoc}
     *
     * @throws BadMethodCallException if a unset is tried on a snapshot property
     */
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
        return $this->old->getRawData();
    }

    /** Gets the last snapshot */
    public function getNew()
    {
        return $this->new->getRawData();
    }

    /**
     * Calculate the changeset between two snapshots
     *
     * The two snapshots must be of the same snapshot type
     *
     * @internal
     * @throws InvalidArgumentException If the two snapshots does not have the same data keys
     */
    protected function compute()
    {
        if (array_keys($this->old->getComparableData()) !== array_keys($this->new->getComparableData())) {
            throw new \InvalidArgumentException('You can\'t compare two snapshots having a different structure');
        }

        $this->changes = [];

        foreach ($this->new->getDataKeys() as $key) {
            $old = $this->old[$key];
            $new = $this->new[$key];

            if ($old instanceof AbstractSnapshot) {
                $old = $old->getRawData();
                $new = $new->getRawData();
            }

            // -- if it is not the same type, then we may consider it changed
            if (gettype($old) !== gettype($new) || ($this->old[$key] instanceof AbstractSnapshot && !$this->new[$key] instanceof $this->old[$key])) {
                $this->changes[$key] = new Modification($old, $new);
                continue;
            }

            switch (true) {
                // known type (object / array) : do a deep comparison
                case $this->old[$key] instanceof ArraySnapshot:
                case $this->old[$key] instanceof ObjectSnapshot:
                    if (!$this->old[$key]->isComparable($this->new[$key])) {
                        $this->changes[$key] = new Modification($old, $new);
                        continue;
                    }

                    $set = new static($this->old[$key], $this->new[$key]);

                    if (0 < count($set)) {
                        $this->changes[$key] = $set;
                    }

                    continue;

                // unknown type : compare raw data
                default:
                    if ($old !== $new) {
                        $this->changes[$key] = new Modification($old, $new);
                    }
            }
        }
    }
}

