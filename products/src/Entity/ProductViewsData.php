<?php

namespace Drupal\products\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Product entities.
 */
class ProductViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['product']['importer'] = [
      'title' => t('Importer'),
      'help' => t('Information about the Product importer.'),
      'field' => array(
        'id' => 'product_importer',
      ),
    ];

    return $data;
  }
}
