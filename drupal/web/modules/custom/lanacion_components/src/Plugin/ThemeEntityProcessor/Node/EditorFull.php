<?php

namespace Drupal\lanacion_components\Plugin\ThemeEntityProcessor\Node;

use Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorBase;


/**
* Return the structured data of an entity.
*
*@ThemeEntityProcesor(
*
* id = "node__editor__full",
* label = @Translation("Node: Editor: Full"),
* entity_type = "node",
* bundle = "editor",
* view_mode = "full"
*
*)
*/

class EditorFull extends ThemeEntityProcessorBase {

  /**
   * {@inheritdoc}
   */

  public function preprocessItemData(&$variables) {
    $node = $variables['elements']['#node'];

    $body = $node->get('body')->value;

    $variables['data'] = [
      'title' => $node->getTitle(),
      'body' => $body
    ];


  }


}
