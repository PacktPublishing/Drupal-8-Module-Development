<?php

namespace Drupal\products\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Represents a Product entity.
 */
interface ProductInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Gets the Product name.
   *
   * @return string
   */
  public function getName();

  /**
   * Sets the Product name.
   *
   * @param string $name
   *
   * @return \Drupal\products\Entity\ProductInterface
   *   The called Product entity.
   */
  public function setName($name);

  /**
   * Gets the Product number.
   *
   * @return int
   */
  public function getProductNumber();

  /**
   * Sets the Product number.
   *
   * @param int $number
   *
   * @return \Drupal\products\Entity\ProductInterface
   *   The called Product entity.
   */
  public function setProductNumber($number);

  /**
   * Gets the Product remote ID.
   *
   * @return string
   */
  public function getRemoteId();

  /**
   * Sets the Product remote ID.
   *
   * @param string $id
   *
   * @return \Drupal\products\Entity\ProductInterface
   *   The called Product entity.
   */
  public function setRemoteId($id);

  /**
   * Gets the Product source.
   *
   * @return string
   */
  public function getSource();

  /**
   * Sets the Product source.
   *
   * @param string $source
   *
   * @return \Drupal\products\Entity\ProductInterface
   *   The called Product entity.
   */
  public function setSource($source);

  /**
   * Gets the Product image.
   *
   * @return \Drupal\file\FileInterface
   */
  public function getImage();

  /**
   * Sets the Product image.
   *
   * @param int $image
   *
   * @return \Drupal\products\Entity\ProductInterface
   *   The called Product entity.
   */
  public function setImage($image);

  /**
   * Gets the Product creation timestamp.
   *
   * @return int
   */
  public function getCreatedTime();

  /**
   * Sets the Product creation timestamp.
   *
   * @param int $timestamp
   *
   * @return \Drupal\products\Entity\ProductInterface
   *   The called Product entity.
   */
  public function setCreatedTime($timestamp);

}