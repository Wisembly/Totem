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
 * Represent a change in a data, with both values (before and after it was modified)
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
interface ChangeInterface
{
    /** @return mixed the data before it was changed */
    public function getOld();

    /** @return mixed the data after it was changed */
    public function getNew();
}

