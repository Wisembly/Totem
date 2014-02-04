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

namespace Totem\Exception;

use \InvalidArgumentException;

class IncomparableDataException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('This data is not comparable with the base');
    }
}
