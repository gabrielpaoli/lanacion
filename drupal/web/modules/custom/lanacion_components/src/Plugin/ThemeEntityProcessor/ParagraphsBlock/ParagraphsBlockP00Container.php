<?php

namespace Drupal\lanacion_components\Plugin\ThemeEntityProcessor\ParagraphsBlock;


use Drupal\lanacion_components\Plugin\ThemeEntityProcessor\LaNacionThemeEntityProcessorBase;


/**
 * Returns the structured data of an entity.
 *
 * @ThemeEntityProcessor(
 *   id = "p00_container_home_sections",
 *   label = @Translation("News container"),
 *   entity_type = "paragraph",
 *   bundle = "p00_container_home_sections",
 *   view_mode = "default"
 * )
 */
class ParagraphsBlockP00Container extends LaNacionThemeEntityProcessorBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessItemData(&$variables) {
    $options['multiple'] = TRUE;
    $news_item = $this->getFieldData($variables['elements']['field_p_related_paragraph'], $options);
    $show_ads = $this->getFieldData($variables['elements']['field_p_show_ads'], $options);
    $variables['data']['news'] = $news_item;
    $variables['data']['show_ads'] = $show_ads;
  }
}

