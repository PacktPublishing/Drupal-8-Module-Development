<?php

namespace Drupal\hello_world\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for the salutation message.
 */
class HelloWorldController extends ControllerBase {

  /**
   * Hello World.
   *
   * @return array
   */
  public function helloWorld() {
    return [
      '#lazy_builder' => ['hello_world.lazy_builder:renderSalutation', []],
      '#create_placeholder' => TRUE,
    ];
  }

  /**
   * Route callback for hiding the Salutation block.
   * Only works for Ajax calls.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function hideBlock(Request $request) {
    if (!$request->isXmlHttpRequest()) {
      throw new NotFoundHttpException();
    }

    $response = new AjaxResponse();
    $command = new RemoveCommand('.block-hello-world');
    $response->addCommand($command);
    return $response;
  }

  /**
   * Handles the access checking. It's not actually used anywhere anymore
   * since we opted for the service-based approach so this method is no longer
   * referenced in the route definition.
   *
   * @param AccountInterface $account
   *
   * @return AccessResultInterface
   */
  public function access(AccountInterface $account) {
    $url = Url::fromRoute('hello_world.hello');
    if ($url->access()) {
      // Do something.
    }
    return in_array('editor', $account->getRoles()) ? AccessResult::forbidden() : AccessResult::allowed();
  }

}
