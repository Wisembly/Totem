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
 * Represents a snapshot of an object at a given time
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
class ObjectSnapshot extends Snapshot
{
    /** @var string object's hash */
    protected $oid;

    /**
     * Build this snapshot
     *
     * @param object $object Object to fix at the current moment
     * @throws InvalidArgumentException If this is not an object
     */
    public function __construct($object)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException('This is not an object');
        }

        parent::__construct($object);

        $this->data = [];
        $this->oid  = spl_object_hash($object);
        $refl       = new ReflectionObject($object);

        if ($refl->isCloneable()) {
            $this->raw  = clone $object;
        }

        foreach ($refl->getProperties() as $reflProperty) {
            $reflProperty->setAccessible(true);
            $value = $reflProperty->getValue($object);

            switch (gettype($value)) {
                case 'object':
                    $value = new static($value);
                    break;

                case 'array':
                    $value = new ArraySnapshot($value);
                    break;

                default:
                    $value = new Snapshot($value);
                    break;
            }

            $this->data[$reflProperty->getName()] = $value;
        }
    }

    /** {@inheritDoc} */
    protected function isComparable(Snapshot $snapshot)
    {
        if (!parent::isComparable($snapshot)) {
            return false;
        }

        return $snapshot->oid === $this->oid;
    }
}

