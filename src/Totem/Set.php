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

use Totem\Change\Removal,
    Totem\Change\Addition,
    Totem\Change\Modification,

    Totem\AbstractSnapshot,
    Totem\Snapshot\ArraySnapshot,
    Totem\Snapshot\ObjectSnapshot;

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
     * @throws InvalidArgumentException If the two snapshots does not have the same data keys
     */
    private function compute(AbstractSnapshot $old, AbstractSnapshot $new)
    {
        $this->changes = [];

        foreach ($old->getDataKeys() as $key) {
            if (!in_array($key, $new->getDataKeys())) {
                $this->changes[$key] = new Removal($old[$key] instanceof AbstractSnapshot ? $old[$key]->getRawData() : $old[$key]);
                continue;
            }

            $current = ['old' => $old[$key] instanceof AbstractSnapshot ? $old[$key]->getRawData() : $old[$key],
                        'new' => $new[$key] instanceof AbstractSnapshot ? $new[$key]->getRawData() : $new[$key]];

            switch (true) {
                // known type (object / array) : do a deep comparison
                case $old[$key] instanceof ArraySnapshot:
                case $old[$key] instanceof ObjectSnapshot:
                    if (!$old[$key]->isComparable($new[$key])) {
                        $this->changes[$key] = new Modification($current['old'], $current['new']);
                        continue;
                    }

                    $set = new static($old[$key], $new[$key]);

                    if (0 < count($set)) {
                        $this->changes[$key] = $set;
                    }

                    continue;

                // unknown type : compare raw data
                default:
                    if ($current['old'] !== $current['new']) {
                        $this->changes[$key] = new Modification($current['old'], $current['new']);
                    }
            }
        }

        // added elements
        foreach (array_diff($new->getDataKeys(), $old->getDataKeys()) as $key) {
            $this->changes[$key] = new Addition($new[$key] instanceof AbstractSnapshot ? $new[$key]->getRawData() : $new[$key]);
        }
    }
}

