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

use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;

use Totem\Snapshot;
use Totem\Snapshotter;
use Totem\UnsupportedDataException;
use Totem\UnsupportedSnapshotException;

use Totem\Snapshot\CollectionSnapshot;

final class CollectionSnapshotter implements Snapshotter
{
    /** @var Snapshotter */
    private $snapshotter;

    /** @var PropertyPath */
    private $primary;

    /** @var PropertyAccessor */
    private $accessor;

    public function __construct(Snapshotter $snapshotter, $primary)
    {
        $this->snapshotter = $snapshotter;
        $this->primary = new PropertyPath($primary);

        $this->accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor()
        ;
    }

    /** {@inheritDoc} */
    public function supports($raw): bool
    {
        if (!is_array($raw) && !$raw instanceof Traversable) {
            return false;
        }

        foreach ($raw as $value) {
            if (!$this->snapshotter->supports($value)) {
                return false;
            }

            if (!$this->accessor->isReadable($value, $this->primary)) {
                return false;
            }
        }

        return true;
    }

    /** {@inheritDoc} */
    public function getSnapshot($raw): Snapshot
    {
        if (!$this->supports($raw)) {
            throw new UnsupportedDataException($this, $raw);
        }

        $data = [];
        $link = [];

        foreach ($raw as $key => $value) {
            $primary = $this->accessor->getValue($value, $this->primary);

            $data[$primary] = $snapshot = $this->snapshotter->getSnapshot($value);
            $link[$primary] = $key;
        }

        return new CollectionSnapshot($raw, $data, $link, $snapshot instanceof Snapshot\MutableSnapshot && $snapshot->isMutable());
    }
}

