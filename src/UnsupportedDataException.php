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

use InvalidArgumentException;

class UnsupportedDataException extends InvalidArgumentException
{
    private $data;

    /** @var Snapshotter */
    private $snapshotter;

    public function __construct(Snapshotter $snapshotter, $data)
    {
        parent::__construct('This data is not supported by this snapshotter');

        $this->data = $data;
        $this->snapshotter = $snapshotter;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getSnapshotter(): Snapshotter
    {
        return $this->snapshotter;
    }
}

