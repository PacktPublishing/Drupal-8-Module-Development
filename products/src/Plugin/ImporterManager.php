<?php

namespace Drupal\products\Plugin;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\products\Entity\ImporterInterface;
use Drupal\products\Plugin\ImporterInterface as ImporterPluginInterface;

/**
 * Provides the Importer plugin manager.
 */
class ImporterManager extends DefaultPluginManager {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * ImporterManager constructor.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, EntityTypeManager $entityTypeManager) {
    parent::__construct('Plugin/Importer', $namespaces, $module_handler, 'Drupal\products\Plugin\ImporterInterface', 'Drupal\products\Annotation\Importer');

    $this->alterInfo('products_importer_info');
    $this->setCacheBackend($cache_backend, 'products_importer_plugins');
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Creates an instance of ImporterInterface plugin based on the ID of a
   * configuration entity.
   *
   * @param $id
   *   Configuration entity ID
   *
   * @return null|ImporterPluginInterface
   */
  public function createInstanceFromConfig($id) {
    $config = $this->entityTypeManager->getStorage('importer')->load($id);
    if (!$config instanceof ImporterInterface) {
      return NULL;
    }

    return $this->createInstance($config->getPluginId(), ['config' => $config]);
  }

  /**
   * Creates an array of ImporterInterface from all the existing Importer
   * configuration entities.
   *
   * @return ImporterPluginInterface[]
   */
  public function createInstanceFromAllConfigs() {
    $configs = $this->entityTypeManager->getStorage('importer')->loadMultiple();
    if (!$configs) {
      return [];
    }
    $plugins = [];
    /** @var ImporterInterface $config */
    foreach ($configs as $config) {
      $plugin = $this->createInstanceFromConfig($config->id());
      if (!$plugin) {
        continue;
      }

      $plugins[] = $plugin;
    }

    return $plugins;
  }
}
