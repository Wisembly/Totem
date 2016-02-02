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

namespace Totem;

class Snapshot
{
    /** @var mixed[] Snapshotted data */
    protected $data;

    /** @var mixed Raw data */
    private $raw;

    public function __construct($raw, array $data)
    {
        $this->raw = $raw;
        $this->data = $data;
    }

    /**
     * Checks if another snapshot is comparable to this one
     *
     * @return bool
     */
    public function isComparable(Snapshot $snapshot): bool
    {
        return true;
    }

    /**
     * Get the raw data associated with the snapshot
     *
     * @return mixed
     */
    final public function getRaw()
    {
        return $this->raw;
    }

    /**
     * Get the snapshotted data
     *
     * @return mixed[]
     */
    final public function getData(): array
    {
        return $this->data;
    }
}

