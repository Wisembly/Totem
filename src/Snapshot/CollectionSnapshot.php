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
    ReflectionClass,
    InvalidArgumentException;

use Symfony\Component\PropertyAccess\PropertyPath,
    Symfony\Component\PropertyAccess\PropertyAccess;

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
     * Collection of the new keys (extracted from the primary key) => old keys
     *
     * @var integer[]
     */
    private $link = [];

    /**
     * Construct the snapshot
     *
     * The following options are taken into account :
     * - snapshotClass : snapshot class to use for each elements. If none are
     *                   given, the normalize() method will transform this into
     *                   either an ArraySnapshot or an ObjectSnapshot, depending
     *                   on the situation.
     *
     * @param mixed $data    Either an array or a traversable, data to take a snapshot of
     * @param mixed $primary Property path compatible value to use to get the primary key of each elements
     * @param array $options Array of options
     *
     * @throws InvalidArgumentException the $data is not an array or a Traversable
     * @throws InvalidArgumentException the $data is not an at least 2 dimensional array
     * @throws InvalidArgumentException the snapshotClass in the options is not loadable
     * @throws InvalidArgumentException the snapshotClass in the options is not a valid snapshot class
     * @throws InvalidArgumentException one of the elements of the collection does not have a $primary key
     */
    public function __construct($data, $primary, array $options = [])
    {
        $this->data = [];
        $this->raw  = $data;

        $primary = new PropertyPath($primary);

        $snapshot = null;
        $accessor = PropertyAccess::createPropertyAccessorBuilder()->enableExceptionOnInvalidIndex()->getPropertyAccessor();

        if (isset($options['snapshotClass'])) {
            if (!class_exists($options['snapshotClass'])) {
                throw new InvalidArgumentException(sprintf('The snapshot class "%s" does not seem to be loadable', $options['snapshotClass']));
            }

            $refl = new ReflectionClass($options['snapshotClass']);

            if (!$refl->isInstantiable() || !$refl->isSubclassOf('Totem\\AbstractSnapshot')) {
                throw new InvalidArgumentException('A Snapshot Class should be instantiable and extends abstract class Totem\\AbstractSnapshot');
            }

            $snapshot = $options['snapshotClass'];
        }

        if (!is_array($data) && !$data instanceof Traversable) {
            throw new InvalidArgumentException(sprintf('An array or a Traversable was expected to take a snapshot of a collection, "%s" given', is_object($data) ? get_class($data) : gettype($data)));
        }

        foreach ($data as $key => $value) {
            if (!is_int($key)) {
                throw new InvalidArgumentException('The given array / Traversable is not a collection as it contains non numeric keys');
            }

            if (!$accessor->isReadable($value, $primary)) {
                throw new InvalidArgumentException(sprintf('The key "%s" is not defined or readable in one of the elements of the collection', $primary));
            }

            $primaryKey = $accessor->getValue($value, $primary);

            $this->link[$primaryKey] = $key;
            $this->data[$primaryKey] = $this->snapshot($value, $snapshot);
        }

        parent::normalize();
    }

    /**
     * Snapshots a value
     *
     * If the value is already a snapshot, it won't be snapshotted ; otherwise,
     * if the class is null, then the value will be left as is.
     *
     * @param mixed  $value Value to snapshot
     * @param string $class Class to use to snapshot the value
     *
     * @return mixed A snapshot if the value was snapshotted, the original value otherwise
     */
    private function snapshot($value, $class = null)
    {
        if (null === $class || $value instanceof AbstractSnapshot) {
            return $value;
        }

        return new $class($value);
    }

    /**
     * Returns the original key for the primary key $primary
     *
     * @param mixed $primary Primary key to search
     *
     * @return integer original key
     * @throws InvalidArgumentException primary key not found
     */
    public function getOriginalKey($primary) {
        if (!isset($this->link[$primary])) {
            throw new InvalidArgumentException(sprintf('The primary key "%s" is not in the computed dataset', $primary));
        }

        return $this->link[$primary];
    }
}

