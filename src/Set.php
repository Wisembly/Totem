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
    \BadMethodCallException;

use Totem\Change\Removal,
    Totem\Change\Addition,
    Totem\Change\Modification,

    Totem\AbstractSnapshot;

/**
 * Represents a changeset
 *
 * @author Rémy Gazelot <rgazelot@gmail.com>
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
class Set extends AbstractChange implements ArrayAccess, Countable, IteratorAggregate
{
    protected $changes = null;

    public function __construct(AbstractSnapshot $old, AbstractSnapshot $new)
    {
        parent::__construct($old->getRawData(), $new->getRawData());

        $this->compute($old, $new);
    }

    /**
     * Retrieve a property change
     *
     * @param string $property
     *
     * @return AbstractChange Set if it is a recursive change,
     *                        Addition if something was added,
     *                        Removal if something it was deleted, or
     *                        Modification otherwise
     *
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
     * @param string $property
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

    /** {@inheritDoc} */
    public function getIterator()
    {
        return new ArrayIterator($this->changes);
    }

    /**
     * Calculate the changeset between two snapshots
     *
     * The two snapshots must be of the same snapshot type
     *
     * @param AbstractSnapshot $old Old snapshot
     * @param AbstractSnapshot $new New snapshot
     *
     * @internal
     */
    private function compute(AbstractSnapshot $old, AbstractSnapshot $new)
    {
        $this->changes = [];

        foreach (array_replace($old->getDataKeys(), $new->getDataKeys()) as $key) {
            $result = $this->computeEntry($old, $new, $key);

            if (null !== $result) {
                $this->changes[$key] = $result;
            }
        }
    }

    /**
     * Calculate the difference between two snapshots for a given key
     *
     * @param mixed $key Key to compare
     *
     * @return AbstractChange|null a change if a change was detected, null otherwise
     * @internal
     */
    private function computeEntry(AbstractSnapshot $old, AbstractSnapshot $new, $key)
    {
        if (!in_array($key, $old->getDataKeys())) {
            return new Addition($this->getRawData($new[$key]));
        }

        if (!in_array($key, $new->getDataKeys())) {
            return new Removal($this->getRawData($old[$key]));
        }

        $values = ['old' => $this->getRawData($old[$key]),
                   'new' => $this->getRawData($new[$key])];

        switch (true) {
            // type verification
            case gettype($old[$key]) !== gettype($new[$key]):
                return new Modification($values['old'], $values['new']);

            // could we compare two snapshots ?
            case $old[$key] instanceof AbstractSnapshot:
                if (!$new[$key] instanceof AbstractSnapshot) {
                    return new Modification($values['old'], $values['new']);
                }

                if (!$old[$key]->isComparable($new[$key])) {
                    return new Modification($values['old'], $values['new']);
                }

                $set = new static($old[$key], $new[$key]);

                if (0 < count($set)) {
                    return $set;
                }

                return null;

            // unknown type : compare raw data
            case $values['old'] !== $values['new']:
                return new Modification($values['old'], $values['new']);
        }
    }

    /**
     * Extracts the raw data for a given value
     *
     * @param mixed $value Value to extract
     * @return mixed value extracted
     */
    private function getRawData($value)
    {
        if ($value instanceof AbstractSnapshot) {
            return $value->getRawData();
        }

        return $value;
    }
}

