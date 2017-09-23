<?php

namespace Drupal\Tests\products\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the CSV Product Importer
 *
 * @group products
 */
class CsvImporterTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['system', 'csv_importer_test', 'products', 'image', 'file', 'user'];

  /**
   * Tests the import of the CSV based plugin.
   */
  public function testImport() {
    $this->installEntitySchema('product');
    $this->installEntitySchema('file');
    $this->installSchema('file', 'file_usage');
    $manager = $this->container->get('entity_type.manager');
    $products = $manager->getStorage('product')->loadMultiple();
    $this->assertEmpty($products);

    $csv_path = drupal_get_path('module', 'csv_importer_test') . '/products.csv';
    $csv_contents = file_get_contents($csv_path);
    $file = file_save_data($csv_contents, 'public://simpletest-products.csv', FILE_EXISTS_REPLACE);
    $config = $manager->getStorage('importer')->create([
      'id' => 'csv',
      'label' => 'CSV',
      'plugin' => 'csv',
      'plugin_configuration' => [
        'file' => [$file->id()]
      ],
      'source' => 'Testing',
      'bundle' => 'goods',
      'update_existing' => true
    ]);
    $config->save();

    $plugin = $this->container->get('products.importer_manager')->createInstanceFromConfig('csv');
    $plugin->import();
    $products = $manager->getStorage('product')->loadMultiple();
    $this->assertCount(2, $products);

    $products = $manager->getStorage('product')->loadByProperties(['number' => 45345]);
    $this->assertNotEmpty($products);
    $this->assertCount(1, $products);
  }
}