<?php

/**
 * Implements hook_drush_command().
 */
function products_drush_command() {
  $items = [];

  $items['products-import-run'] = [
    'description' => 'Run the product importers',
    'options' => [
      'importer' => 'The Importer configuration entity to run.',
    ],
    'aliases' => ['pir'],
    'callback' => 'products_products_import_run'
  ];

  return $items;
}

/**
 * Callback for the products-import-run Drush command.
 */
function products_products_import_run() {
  $importer = drush_get_option('importer', NULL);
  /** @var \Drupal\products\Plugin\ImporterManager $manager */
  $manager = \Drupal::service('products.importer_manager');
  if (!is_null($importer)) {
    $plugin = $manager->createInstanceFromConfig($importer);
    if (is_null($plugin)) {
      drupal_set_message(t('The specified importer does not exist'), 'error');
      return;
    }

    _products_products_import_run_plugin($plugin);
    return;
  }

  $plugins = $manager->createInstanceFromAllConfigs();
  if (!$plugins) {
    drupal_set_message(t('There are no importers to run'), 'error');
    return;
  }
  
  foreach ($plugins as $plugin) {
    _products_products_import_run_plugin($plugin);
  }
}

/**
 * Runs an individual Importer plugin.
 *
 * @see products_products_import_run().
 *
 * @param \Drupal\products\Plugin\ImporterInterface $plugin
 */
function _products_products_import_run_plugin(\Drupal\products\Plugin\ImporterInterface $plugin) {
  $lock = \Drupal::lock();
  if (!$lock->acquire($plugin->getPluginId())) {
    drush_log(t('The plugin %plugin is already running. Waiting for it to finish.', ['%plugin' => $plugin->getPluginDefinition()['label']]));
    if ($lock->wait($plugin->getPluginId())) {
      drush_log(t('The wait is killing me. Giving up.'));
      return;
    }
  }
  $result = $plugin->import();

  $message_values = ['@importer' => $plugin->getConfig()->label()];
  $message = $result ? t('The "@importer" importer has been run.', $message_values) : t('There was a problem running the "@importer" importer.', $message_values);
  $message_status = $result ? 'success' : 'error';
  drush_log($message, $message_status);
  $lock->release($plugin->getPluginId());
}