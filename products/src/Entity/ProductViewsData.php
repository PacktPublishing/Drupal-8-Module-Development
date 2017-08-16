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
    return $data;
  }
}
