<?php

namespace Drupal\Tests\products\FunctionalJavascript;

use Drupal\file\Entity\File;
use Drupal\FunctionalJavascriptTests\JavascriptTestBase;
use Drupal\products\Entity\Importer;
use Drupal\products\Entity\ProductType;

/**
 * Testing the creation/edit of Importer configuration entities using the CSV importer
 *
 * @group products
 */
class ImporterFormTest extends JavascriptTestBase {

  /**
   * @var \Drupal\file\FileInterface
   */
  protected $file;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $admin;

  /**
   * @var \Drupal\products\Entity\ProductType
   */
  protected $bundle;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['image', 'file'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->container->get('module_installer')->install(['products', 'csv_importer_test']);
    $csv_path = drupal_get_path('module', 'csv_importer_test') . '/products.csv';
    $csv_contents = file_get_contents($csv_path);
    $this->file = file_save_data($csv_contents, 'public://simpletest-products.csv', FILE_EXISTS_REPLACE);
    $this->admin = $this->drupalCreateUser(['administer site configuration']);
    $this->bundle = ProductType::create(['id' => 'goods', 'label' => 'Goods']);
    $this->bundle->save();
  }

  /**
   * Tests the importer form.
   */
  public function testForm() {
    $this->drupalGet('/admin/structure/importer/add');
    $assert = $this->assertSession();
    $assert->statusCodeEquals(403);
    $this->drupalLogin($this->admin);
    $this->drupalGet('/admin/structure/importer/add');
    $assert->pageTextContains('Add importer');
    $assert->elementExists('css', '#edit-label');
    $assert->elementExists('css', '#edit-plugin');
    $assert->elementExists('css', '#edit-update-existing');
    $assert->elementExists('css', '#edit-source');
    $assert->elementExists('css', '#edit-bundle');
    $assert->elementNotExists('css', 'input[name="files[plugin_configuration_plugin_file]"]');

    $page = $this->getSession()->getPage();
    $page->selectFieldOption('plugin', 'csv');
    $this->assertSession()->assertWaitOnAjaxRequest();
    $assert->elementExists('css', 'input[name="files[plugin_configuration_plugin_file]"]');

    $page->fillField('label', 'Test CSV Importer');
    $page->fillField('id', 'test_csv_importer');
    $page->checkField('update_existing');
    $page->fillField('source', 'testing');
    $page->fillField('bundle', $this->bundle->id());
    $wrapper = $this->container->get('stream_wrapper_manager')->getViaUri($this->file->getFileUri());
    $page->attachFileToField('files[plugin_configuration_plugin_file]', $wrapper->realpath());
    $this->assertSession()->assertWaitOnAjaxRequest();
    $page->pressButton('Save');
    $assert->pageTextContains('Created the Test CSV Importer Importer.');

    $config = Importer::load('test_csv_importer');
    $this->assertInstanceOf('Drupal\products\Entity\ImporterInterface', $config);

    $fids = $config->getPluginConfiguration()['file'];
    $fid = reset($fids);
    $file = File::load($fid);
    $this->assertInstanceOf('Drupal\file\FileInterface', $file);

    $this->drupalGet('admin/structure/importer/test_csv_importer/edit');
    $assert->pageTextContains('Edit Test CSV Importer');
    $assert->fieldValueEquals('label', 'Test CSV Importer');
    $assert->fieldValueEquals('plugin', 'csv');
    $assert->checkboxChecked('update_existing');
    $assert->fieldValueEquals('source', 'testing');
    $page->hasLink('products.csv');
    $bundle_field = $this->bundle->label() . ' (' . $this->bundle->id() . ')';
    $assert->fieldValueEquals('bundle', $bundle_field);
  }
}