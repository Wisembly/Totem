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

class UnchangedPropertyException extends InvalidArgumentException
{
    private $property;

    public function __construct(string $property)
    {
        $this->property = $property;
        parent::__construct('The property $property was not changed.');
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}

