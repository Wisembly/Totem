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

namespace Totem\Snapshotter;

use InvalidArgumentException;

use Totem\Snapshot;
use Totem\Snapshotter;
use Totem\UnsupportedDataException;
use Totem\UnsupportedSnapshotException;

use Totem\Snapshot\ObjectSnapshot;

final class ObjectSnapshotter implements Snapshotter
{
    /** {@inheritDoc} */
    public function getSnapshot($raw): Snapshot
    {
        if (!$this->supports($raw)) {
            throw new UnsupportedDataException($this, $raw);
        }

        $data = [];
        $export = (array) $raw;
        $class = get_class($raw);

        foreach ($export as $property => $value) {
            $property = str_replace(["\x00*\x00", "\x00{$class}\x00"], '', $property); // not accessible properties
            $data[$property] = $value;
        }

        return new class($raw, $data) extends Snapshot implements ObjectSnapshot {
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

            /** {@inheritDoc} */
            public function getObjectId()
            {
                return $this->oid;
            }

            /** {@inheritDoc} */
            public function isComparable(Snapshot $snapshot): bool
            {
                return $snapshot instanceof ObjectSnapshot && $snapshot->getObjectId() === $this->oid;
            }
        };
    }

    /** {@inheritDoc} */
    public function supports($data): bool
    {
        return is_object($data);
    }

    /** {@inheritDoc} */
    public function setData(Snapshot $snapshot, array $data)
    {
        if (!$snapshot instanceof ObjectSnapshot) {
            throw new UnsupportedSnapshotException($this, $snapshot);
        }

        // from http://ocramius.github.io/blog/accessing-private-php-class-members-without-reflection/
        $callback = function () use ($data) {
            $this->data = $data;
        };

        $callback->call($snapshot);
    }
}

