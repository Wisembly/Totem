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

use Totem\AbstractSnapshot;

/**
 * Represents a set of changes between two data
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
interface SetInterface
{
    /**
     * Retrieve a property change
     *
     * @param string $property
     *
     * @return AbstractChange Set if it is a recursive change,
     *                        Addition if something was added,
     *                        Removal if something it was deleted, or
     *                        Modification otherwise
     */
    public function getChange($property);

    /**
     * Test if the given property has been changed
     *
     * @param string $property
     *
     * @return boolean
     */
    public function hasChanged($property);

    /**
     * Calculate the changeset between two snapshots
     *
     * The two snapshots must be of the same snapshot type
     *
     * @param AbstractSnapshot $old Old snapshot
     * @param AbstractSnapshot $new New snapshot
     */
    public function compute(AbstractSnapshot $old, AbstractSnapshot $new);
}

