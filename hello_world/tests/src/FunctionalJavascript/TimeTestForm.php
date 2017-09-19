<?php

namespace Drupal\Tests\products\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\JavascriptTestBase;

/**
 * Testing the simple Javascript timer on the Hello World page.
 *
 * @group products
 */
class TimeTestForm extends JavascriptTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['hello_world'];

  /**
   * Tests the time component.
   */
  public function testTime() {
    $this->drupalGet('/hello');
    $this->assertSession()->pageTextContains('The time is');

    $config = $this->config('hello_world.custom_salutation');
    $config->set('salutation', 'Testing salutation');
    $config->save();

    $this->drupalGet('/hello');
    $this->assertSession()->pageTextNotContains('The time is');
  }
}