<?php

namespace Drupal\Tests\sports\Functional;

use Drupal\KernelTests\KernelTestBase;
use Drupal\sports\Plugin\QueueWorker\TeamCleaner;

/**
 * Test the TeamCleaner QueueWorker plugin.
 *
 * @group sports
 */
class TeamCleanerTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['sports'];

  /**
   * Tests the TeamCleaner::processItem() method.
   */
  public function testProcessItem() {
    $database = $this->container->get('database');
    $this->installSchema('sports', 'teams');
    $fields = ['name' => 'Team name'];
    $id = $database->insert('teams')
      ->fields($fields)
      ->execute();

    $worker = new TeamCleaner([], NULL, NULL, $database);
    $data = new \stdClass();
    $data->id = $id;
    $records = $database->query("SELECT id FROM {teams} WHERE id = :id", [':id' => $id])->fetchAll();
    $this->assertNotEmpty($records);
    $worker->processItem($data);
    $records = $database->query("SELECT id FROM {teams} WHERE id = :id", [':id' => $id])->fetchAll();
    $this->assertEmpty($records);
  }
}
