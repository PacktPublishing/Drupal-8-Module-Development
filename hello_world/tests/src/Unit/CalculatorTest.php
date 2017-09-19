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
   * Tests the Calculator::subtract() method.
   */
  public function testSubtract() {
    $calculator = new Calculator(10, 5);
    $this->assertEquals(5, $calculator->subtract());
  }

  /**
   * Tests the Calculator::multiply() method.
   */
  public function testMultiply() {
    $calculator = new Calculator(10, 5);
    $this->assertEquals(50, $calculator->multiply());
  }

  /**
   * Tests the Calculator::divide() method.
   */
  public function testDivide() {
    $calculator = new Calculator(10, 5);
    $this->assertEquals(2, $calculator->divide());
  }

}