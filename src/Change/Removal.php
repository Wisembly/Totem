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

namespace Totem\Change;

use BadMethodCallException;
use Totem\Change;

/**
 * Represents something that was removed from the original data
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
final class Removal extends Change
{
    public function __construct($old)
    {
        parent::__construct($old, null);
    }

    public function getNew()
    {
        throw new BadMethodCallException('A removal does not have a new state');
    }
}

