<?php

namespace Drupal\products\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Importer plugins.
 */
interface ImporterInterface extends PluginInspectionInterface {

  /**
   * Performs the import. Returns TRUE if the import was successful or FALSE otherwise.
   *
   * @return bool
   */
  public function import();

  /**
   * Returns the Importer configuration entity.
   *
   * @return \Drupal\products\Entity\ImporterInterface
   */
  public function getConfig();

  /**
   * Returns the form array for configuring this plugin.
   *
   * @param \Drupal\products\Entity\ImporterInterface $importer
   *
   * @return array
   */
  public function getConfigurationForm(\Drupal\products\Entity\ImporterInterface $importer);
}
