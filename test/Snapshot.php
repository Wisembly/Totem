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

class Snapshot extends AbstractSnapshot
{
    public function __construct(array $args = [])
    {
        $this->raw = isset($args['raw']) ? $args['raw'] : null;
        $this->data = isset($args['data']) ? $args['data'] : [];
        $this->comparable = isset($args['comparable']) ? true === $args['comparable'] : null;
    }

    public function isComparable(AbstractSnapshot $s)
    {
        return null === $this->comparable ? parent::isComparable($s) : $this->comparable;
    }
}

