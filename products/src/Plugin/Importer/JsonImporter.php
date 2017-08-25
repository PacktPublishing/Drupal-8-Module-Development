<?php

namespace Drupal\products\Plugin\Importer;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\products\Entity\ImporterInterface;
use Drupal\products\Entity\ProductInterface;
use Drupal\products\Plugin\ImporterBase;

/**
 * Product importer from a JSON format.
 *
 * @Importer(
 *   id = "json",
 *   label = @Translation("JSON Importer")
 * )
 */
class JsonImporter extends ImporterBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function import() {
    $data = $this->getData();
    if (!$data) {
      return FALSE;
    }

    if (!isset($data->products)) {
      return FALSE;
    }

    $products = $data->products;
    $batch = [
      'title' => $this->t('Importing products'),
      'operations' => [
        [[$this, 'clearMissing'], [$products]],
        [[$this, 'importProducts'], [$products]],
      ],
      'finished' => [$this, 'importProductsFinished'],
    ];
    batch_set($batch);
    if (PHP_SAPI == 'cli') {
      drush_backend_batch_process();
    }

    return TRUE;
  }

  /**
   * Batch operation to remove the products which are no longer in the list of
   * products coming from the JSON file.
   *
   * @param $products
   * @param $context
   */
  public function clearMissing($products, &$context) {
    if (!isset($context['results']['cleared'])) {
      $context['results']['cleared'] = [];
    }

    if (!$products) {
      return;
    }

    $ids = [];
    foreach ($products as $product) {
      $ids[] = $product->id;
    }

    $ids = $this->entityTypeManager->getStorage('product')->getQuery()
      ->condition('remote_id', $ids, 'NOT IN')
      ->execute();
    if (!$ids) {
      $context['results']['cleared'] = [];
      return;
    }

    $entities = $this->entityTypeManager->getStorage('product')->loadMultiple($ids);

    /** @var ProductInterface $entity */
    foreach ($entities as $entity) {
      $context['results']['cleared'][] = $entity->getName();
    }
    $context['message'] = $this->t('Removing @count products', ['@count' => count($entities)]);
    $this->entityTypeManager->getStorage('product')->delete($entities);
  }

  /**
   * Batch operation to import the products from the JSON file.
   *
   * @param $products
   * @param $context
   */
  public function importProducts($products, &$context) {
    if (!isset($context['results']['imported'])) {
      $context['results']['imported'] = [];
    }

    if (!$products) {
      return;
    }

    $sandbox = &$context['sandbox'];
    if (!$sandbox) {
      $sandbox['progress'] = 0;
      $sandbox['max'] = count($products);
      $sandbox['products'] = $products;
    }

    $slice = array_splice($sandbox['products'], 0, 3);
    foreach ($slice as $product) {
      $context['message'] = $this->t('Importing product @name', ['@name' => $product->name]);
      $this->persistProduct($product);
      $context['results']['imported'][] = $product->name;
      $sandbox['progress']++;
    }

    $context['finished'] = $sandbox['progress'] / $sandbox['max'];
  }

  /**
   * Callback for when the batch processing completes.
   *
   * @param $success
   * @param $results
   * @param $operations
   */
  public function importProductsFinished($success, $results, $operations) {
    if (!$success) {
      drupal_set_message($this->t('There was a problem with the batch'), 'error');
      return;
    }

    $cleared = count($results['cleared']);
    if ($cleared == 0) {
      drupal_set_message($this->t('No products had to be deleted.'));
    }
    else {
      drupal_set_message($this->formatPlural($cleared, '1 product had to be deleted.', '@count products had to be deleted.'));
    }

    $imported = count($results['imported']);
    if ($imported == 0) {
      drupal_set_message($this->t('No products found to be imported.'));
    }
    else {
      drupal_set_message($this->formatPlural($imported, '1 product imported.', '@count products imported.'));
    }
  }

  /**
   * Loads the product data from the remote URL.
   *
   * @return \stdClass
   */
  private function getData() {
    /** @var ImporterInterface $importer_config */
    $importer_config = $this->configuration['config'];
    $config = $importer_config->getPluginConfiguration();
    $url = isset($config['url']) ? $config['url'] : NULL;
    if (!$url) {
      return NULL;
    }
    $request = $this->httpClient->get($url);
    $string = $request->getBody();
    return json_decode($string);
  }

  /**
   * Saves a Product entity from the remote data.
   *
   * @param \stdClass $data
   */
  private function persistProduct($data) {
    /** @var ImporterInterface $config */
    $config = $this->configuration['config'];

    $existing = $this->entityTypeManager->getStorage('product')->loadByProperties(['remote_id' => $data->id, 'source' => $config->getSource()]);
    if (!$existing) {
      $values = [
        'remote_id' => $data->id,
        'source' => $config->getSource(),
        'type' => $config->getBundle(),
      ];
      /** @var ProductInterface $product */
      $product = $this->entityTypeManager->getStorage('product')->create($values);
      $product->setName($data->name);
      $product->setProductNumber($data->number);
      $this->handleProductImage($data, $product);
      $product->save();
      return;
    }

    if (!$config->updateExisting()) {
      return;
    }

    /** @var ProductInterface $product */
    $product = reset($existing);
    $product->setName($data->name);
    $product->setProductNumber($data->number);
    $this->handleProductImage($data, $product);
    $product->save();
  }

  /**
   * Imports the image of the product and adds it to the Product entity.
   *
   * @param $data
   * @param ProductInterface $product
   */
  private function handleProductImage($data, ProductInterface $product) {
    $name = $data->image;
    $image = file_get_contents('products://' . $name);
    if (!$image) {
      // Perhaps log something.
      return;
    }

    /** @var \Drupal\file\FileInterface $file */
    $file = file_save_data($image, 'public://product_images/' . $name, FILE_EXISTS_REPLACE);
    if (!$file) {
      // Something went wrong, perhaps log it.
      return;
    }

    $product->setImage($file->id());
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigurationForm(\Drupal\products\Entity\ImporterInterface $importer) {
    $form = [];
    $config = $importer->getPluginConfiguration();
    $form['url'] = [
      '#type' => 'url',
      '#default_value' => isset($config['url']) ? $config['url'] : '',
      '#title' => $this->t('Url'),
      '#description' => $this->t('The URL to the import resource'),
      '#required' => TRUE,
    ];
    return $form;
  }
}