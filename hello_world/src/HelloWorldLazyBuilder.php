<?php

namespace Drupal\hello_world;

/**
 * Lazy builder for the Hello World salutation.
 */
class HelloWorldLazyBuilder {

  /**
   * @var \Drupal\hello_world\HelloWorldSalutation
   */
  protected $salutation;

  /**
   * HelloWorldLazyBuilder constructor.
   *
   * @param \Drupal\hello_world\HelloWorldSalutation $salutation
   */
  public function __construct(HelloWorldSalutation $salutation) {
    $this->salutation = $salutation;
  }

  /**
   * Renders the Hello World salutation message.
   */
  public function renderSalutation() {
    return $this->salutation->getSalutationComponent();
  }
}