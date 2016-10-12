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
 * Represents something that was added in the original data
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
final class Addition extends Change
{
    public function __construct($new)
    {
        parent::__construct(null, $new);
    }

    public function getOld()
    {
        throw new BadMethodCallException('An addition does not have an old state');
    }
}

