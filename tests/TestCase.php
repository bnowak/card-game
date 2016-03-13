<?php
declare(strict_types = 1);

namespace Bnowak\CardGame\Tests;

use PHPUnit_Framework_TestCase;

/**
 * TestCase
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    public function assertArrayIsSequential(array $array)
    {
        if (empty($array)) {
            $this->fail('Can\'t check empty array if is sequential');
        } else {
            $this->assertTrue(array_keys($array) === range(0, count($array) - 1), 'Array is not sequential');
        }
    }
}
