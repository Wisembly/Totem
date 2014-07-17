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

use Traversable,
    ArrayAccess,
    InvalidArgumentException;

use Symfony\Component\PropertyAccess\PropertyAccess;

use Totem\AbstractSnapshot;

/**
 * Represents a snapshot of a collection
 *
 * A collection is an array of numerical indexes of array elements
 *
 * BE CAREFUL, as this collection is _not_ recursive. Its elements will be
 * translated as either an ArraySnapshot, or an ObjectSnapshot if it fits, but
 * none of its child will be translated as a new CollectionSnapshot.
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
class CollectionSnapshot extends AbstractSnapshot
{
    /**
     * Construct the snapshot
     *
     * @param mixed $data Either an array or a traversable, data to take a snapshot of
     * @param mixed $pkey Key to use as a primary key
     *
     * @throws InvalidArgumentException if the $data is not an array or a Traversable
     * @throws InvalidArgumentException if the $data is not an at least 2 dimensional array
     * @throws InvalidArgumentException if one of the arrays of the collection does not have a $key key
     */
    public function __construct($data, $pkey)
    {
        $this->data = [];
        $this->raw  = $data;

        $accessor = PropertyAccess::createPropertyAccessor();

        if ($data instanceof Traversable) {
            $data = iterator_to_array($data);
        }

        if (!is_array($data)) {
            throw new InvalidArgumentException(sprintf('An array or a Traversable was expected to take a snapshot of a collection, "%s" given', is_object($data) ? get_class($data) : gettype($data)));
        }

        foreach ($data as $key => $value)
        {
            if (!is_int($key)) {
                throw new InvalidArgumentException('The given array / Traversable is not a collection as it contains non numeric keys');
            }

            if (!$accessor->isReadable($value, $pkey)) {
                throw new InvalidArgumentException(sprintf('The key "%s" is not defined or readable in one of the elements of the collection', $pkey));
            }

            $this->data[$accessor->getValue($value, $pkey)] = $value;
        }

        parent::normalize();
    }
}

