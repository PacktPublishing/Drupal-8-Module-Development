<?php

namespace Drupal\products\Plugin\Importer;

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
    foreach ($products as $product) {
      $this->persistProduct($product);
    }
    return TRUE;
  }

  /**
   * Loads the product data from the remote URL.
   *
   * @return \stdClass
   */
  private function getData() {
    /** @var ImporterInterface $config */
    $config = $this->configuration['config'];
    $request = $this->httpClient->get($config->getUrl()->toString());
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
    $product->save();
  }
}