<?php

namespace Drupal\lanacion_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;

/**
* An example test controller.
*/
class TestController extends ControllerBase {

  /**
  * Returns a render-able array for a test page.
  */
  public function content(NodeInterface $node) {

    $data = [
      'title' => $node->getTitle(),
      'body' => $node->get('body')->value,
      'tags' => $node->get('field_tags')->referencedEntities()
    ];

    $testData = [
      'greeting' => 'Hello rabbit',
    ];

    return [
      '#theme' => 'test_custom_theme',
      '#testData' =>  $testData,
      '#data' => $data,
    ];

  }

}
