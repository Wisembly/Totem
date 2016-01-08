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
     * @return ChangeInterface|SetInterface
     */
    public function getChange($property);

    /**
     * Test if the given property has been changed
     *
     * @param string $property
     *
     * @return boolean
     */
    public function hasChanged($property) : bool;
}

