<?php

namespace Drupal\products\Plugin\views\field;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Field plugin that renders data about the Importer that imported the Product.
 *
 * @ViewsField("product_importer")
 */
class ProductImporter extends FieldPluginBase {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructs a ProductImporter object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManager $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    /** @var \Drupal\products\Entity\ProductInterface $product */
    $product = $values->_entity;
    $source = $product->getSource();
    $importers = $this->entityTypeManager->getStorage('importer')->loadByProperties(['source' => $source]);
    if (!$importers) {
      return NULL;
    }

    // We'll assume one importer per source.
    /** @var \Drupal\products\Entity\ImporterInterface $importer */
    $importer = reset($importers);
    return $this->sanitizeValue($importer->label());
  }
}
