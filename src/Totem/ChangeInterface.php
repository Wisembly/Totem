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
 * Represents a change (either a Set, either a Change)
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
interface ChangeInterface
{
    /**
     * Get the old state of something
     *
     * @return mixed
     */
    public function getOld();

    /**
     * Get the new state of something
     *
     * @return mixed
     */
    public function getNew();
}

