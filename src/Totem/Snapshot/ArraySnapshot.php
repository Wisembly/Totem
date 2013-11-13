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

use Totem\Snapshot;

/**
 * Represents a snapshot of an array at a given time
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
class ArraySnapshot extends Snapshot
{
    public function __construct(array $data)
    {
        parent::__construct($data);

        foreach ($data as &$value) {
            switch (gettype($value)) {
                case 'object':
                    $value = new ObjectSnapshot($value);
                    break;

                case 'array':
                    $value = new static($value);
                    break;
            }
        }

        $this->data = $data;
    }

    /** {@inheritDoc} */
    protected function isComparable(Snapshot $snapshot)
    {
        if (!parent::isComparable($snapshot)) {
            return false;
        }

        return array_keys($snapshot->data) === array_keys($this->data);
    }
}

