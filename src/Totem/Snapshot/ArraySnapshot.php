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

namespace Totem\Snapshot;

use \ReflectionObject,
    \ReflectionProperty,

    \InvalidArgumentException;

use Totem\AbstractSnapshot;

/**
 * Represents a snapshot of an array at a given time
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
class ArraySnapshot extends AbstractSnapshot
{
    public function __construct(array $data)
    {
        $this->raw  = $data;
        $this->data = $data;

        parent::normalize();
    }
}

