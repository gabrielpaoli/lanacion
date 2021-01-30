<?php

namespace Drupal\lanacion_test\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
* An example test controller.
*/
class TestController extends ControllerBase {

  /**
  * Returns a render-able array for a test page.
  */
  public function content() {

    $entity_type = 'node';

    $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load(4);
    //kint($entity);


    $testData = [
      'greeting' => 'Hello rabbit',
    ];

    return [
      '#theme' => 'test_custom_theme',
      '#testData' =>  $testData,
    ];

  }

}
