<?php

namespace Drupal\Tests\hello_world\Unit;

use Drupal\hello_world\Calculator;
use Drupal\Tests\UnitTestCase;

/**
 * Tests the Calculator class methods.
 *
 * @group hello_world
 */
class CalculatorTest extends UnitTestCase {

  /**
   * Tests the Calculator::add() method.
   */
  public function testAdd() {
    $calculator = new Calculator(10, 5);
    $this->assertEquals(15, $calculator->add());
  }

  /**
   * Tests the Calculator::add() method.
   */
  public function testSubtract() {
    $calculator = new Calculator(10, 5);
    $this->assertEquals(5, $calculator->subtract());
  }

}