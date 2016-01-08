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

namespace Totem\Change;

use Totem\ChangeInterface;

/**
 * Represents something that was added in the original data
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
class Addition implements ChangeInterface
{
    /** @var mixed */
    private $new;

    public function __construct($new)
    {
        $this->new = $new;
    }

    public function getOld()
    {
        return null;
    }

    public function getNew()
    {
        return $this->new;
    }
}

