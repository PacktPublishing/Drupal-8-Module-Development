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
   * @var Calculator
   */
  protected $calculatorOne;

  /**
   * @var Calculator
   */
  protected $calculatorTwo;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->calculatorOne = new Calculator(10, 5);
    $this->calculatorTwo = new Calculator(10, 2);
  }

  /**
   * Tests the Calculator::add() method.
   */
  public function testAdd() {
    $this->assertEquals(15, $this->calculatorOne->add());
    $this->assertEquals(12, $this->calculatorTwo->add());
  }

  /**
   * Tests the Calculator::subtract() method.
   */
  public function testSubtract() {
    $this->assertEquals(5, $this->calculatorOne->subtract());
    $this->assertEquals(8, $this->calculatorTwo->subtract());
  }

  /**
   * Tests the Calculator::multiply() method.
   */
  public function testMultiply() {
    $this->assertEquals(50, $this->calculatorOne->multiply());
    $this->assertEquals(20, $this->calculatorTwo->multiply());
  }

  /**
   * Tests the Calculator::divide() method.
   */
  public function testDivide() {
    $this->assertEquals(2, $this->calculatorOne->divide());
    $this->assertEquals(5, $this->calculatorTwo->divide());
  }

}