<?php

namespace Drupal\lanacion_components\Plugin\ThemeEntityProcessor\Paragraphs;

use Drupal\lanacion_components\Plugin\ThemeEntityProcessor\LaNacionThemeEntityProcessorBase;
use Drupal\file\Entity\File;

/**
 * Returns the structured data of an entity.
 *
 * @ThemeEntityProcessor(
 *   id = "p03_news",
 *   label = @Translation("News"),
 *   entity_type = "paragraph",
 *   bundle = "p03_news",
 *   view_mode = "default"
 * )
 */
class newsItem extends LaNacionThemeEntityProcessorBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessItemData(&$variables) {

    $title = $this->getFieldData($variables['elements']['field_p_title']);
    $show_as = $variables['elements']['#paragraph']->get('field_p_show_as')->value;
    $news = $variables['elements']['#paragraph']->get('field_p_related_news')->referencedEntities();

    $variables['data'] = [
      'title' => $title,
      'show_as' => $show_as,
      'news' => $this->getNewsData($news),
    ];

  }

  public function getNewsData($news){
    $data = [];

    foreach($news as $indNews){
      $url_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/'. $indNews->id());
      $fid = $indNews->get('field_image')->getValue()[0]['target_id'];

      $file = File::load($fid);
      $uri = $file->getFileUri();

      $data[] = [
        'title' => $indNews->getTitle(),
        'link' => $url_alias,
        'image_url' => $uri,
        'image_alt' => $indNews->get('field_image')->getValue()[0]['alt']
      ];
    }

    return $data;

  }



}
