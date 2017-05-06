<?php

namespace Drupal\hello_world;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Prepares the salutation to the world.
 */
class HelloWorldSalutation {

  use StringTranslationTrait;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * HelloWorldSalutation constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Returns the salutation
   */
  public function getSalutation() {
    $config = $this->configFactory->get('hello_world.custom_salutation');
    $salutation = $config->get('salutation');
    if ($salutation != "") {
      return $salutation;
    }

    $time = new \DateTime();
    if ((int) $time->format('G') >= 06 && (int) $time->format('G') < 12) {
      return $this->t('Good morning world');
    }

    if ((int) $time->format('G') >= 12 && (int) $time->format('G') < 18) {
      return $this->t('Good afternoon world');
    }

    if ((int) $time->format('G') >= 18) {
      return $this->t('Good evening world');
    }
  }

  /**
   * Returns a the Salutation render array.
   */
  public function getSalutationComponent() {
    $render = [
      '#theme' => 'hello_world_salutation',
    ];

    $config = $this->configFactory->get('hello_world.custom_salutation');
    $salutation = $config->get('salutation');

    if ($salutation != "") {
      $render['#salutation'] = $salutation;
      $render['#overridden'] = TRUE;
      return $render;
    }

    $time = new \DateTime();
    $render['#target'] = $this->t('world');

    if ((int) $time->format('G') >= 06 && (int) $time->format('G') < 12) {
      $render['#salutation'] = $this->t('Good morning');
      return $render;
    }

    if ((int) $time->format('G') >= 12 && (int) $time->format('G') < 18) {
      $render['#salutation'] = $this->t('Good afternoon');
      return $render;
    }

    if ((int) $time->format('G') >= 18) {
      $render['#salutation'] = $this->t('Good evening');
      return $render;
    }
  }
}