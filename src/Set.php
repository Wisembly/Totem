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

/**
 * Represents a changeset
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
class Set
{
    private $changes = [];

    public function __construct(array $changes)
    {
        $this->changes = $changes;
    }

    /**
     * Check if $property has changed
     *
     * @return bool
     */
    public function hasChanged(string $property): bool
    {
        return isset($this->changes[$property]);
    }

    /**
     * Get the changes that are affecting $property
     *
     * This methods have 2 types of returns ; either Set (like this interface)
     * if the properties in the 2 compared snapshots are snapshots, or a Change
     * object otherwise.
     *
     * @oaram string $property Property to the changes of
     *
     * @return Set|Change
     * @throws UnchangedPropertyException If the property did not change
     */
    public function getChange(string $property)
    {
        if (!$this->hasChanged($property)) {
            throw new UnchangedPropertyException($property);
        }

        return $this->changes[$property];
    }
}

