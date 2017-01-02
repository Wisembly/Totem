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

use InvalidArgumentException;
use Totem\Snapshot;
use Totem\Snapshotter\RecursiveSnapshotter;

/**
 * Object snapshot
 *
 * @internal
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
final class ObjectSnapshot extends Snapshot implements MutableSnapshot
{
    /** @var string object spl hash */
    private $oid;

    public function __construct($raw, array $data)
    {
        if (!is_object($raw)) {
            throw new InvalidArgumentException(sprintf('Expected an object, got %s', gettype($raw)));
        }

        parent::__construct($raw, $data);
        $this->oid = spl_object_hash($raw);
    }

    /** @return string object hash */
    public function getObjectId(): string
    {
        return $this->oid;
    }

    /** {@inheritDoc} */
    public function isComparable(Snapshot $snapshot): bool
    {
        return $snapshot instanceof ObjectSnapshot && $snapshot->getObjectId() === $this->oid;
    }

    /** {@inheritDoc} */
    public function isMutable(): bool
    {
        return true;
    }

    /** {@inheritDoc} */
    public function mutate(RecursiveSnapshotter $recursiveSnapshotter): Snapshot
    {
        $clone = clone $this;

        foreach ($clone->data as &$data) {
            try {
                $data = $recursiveSnapshotter->getSnapshot($data);
            } catch (UnsupportedDataException $e) {
            }
        }

        return $clone;
    }
}

