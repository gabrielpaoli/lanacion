<?php

namespace Drupal\lanacion_components\Plugin\ThemeEntityProcessor\Paragraphs;

use Drupal\lanacion_components\Plugin\ThemeEntityProcessor\LaNacionThemeEntityProcessorBase;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;

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
      'css_classes' => $this->getCssClasses($news, $show_as),
    ];

  }

  public function getCssClasses($news, $show_as){
    $css_classes = '';

    if($show_as[0] == 4){
      if(sizeof($news)%3==0){
        $css_classes = 'grid grid-cols-1 sm:grid-cols-3 gap-10 mr-10 mt-5';
      }elseif(sizeof($news) == 2){
        $css_classes = 'grid grid-cols-1 sm:grid-cols-2 gap-10 mr-10 mt-5';
      }elseif(sizeof($news) == 4){
        $css_classes = 'grid grid-cols-1 sm:grid-cols-4 gap-10 mr-10 mt-5 w-full';
      }
    }
    return $css_classes;

  }

  public function getImage($node){
    $data = [];

    $fid = (!empty($node->get('field_image')->getValue()[0]) ? $node->get('field_image')->getValue()[0]['target_id'] : null);

    if(!empty($fid)){
      $file = File::load($fid);
      $data = [
        'uri' => $file->getFileUri(),
        'alt' => $node->get('field_image')->getValue()[0]['alt']
      ];
    }

    return $data;
  }

  public function getTags($node){

    $term = (!empty($node->get('field_tags')->target_id) ? Term::load($node->get('field_tags')->target_id) : null);
    $term_name = (!empty($term) ? $term->getName() : null);

    return $term_name;
  }

  public function getNewsData($news){
    $data = [];
    $editor_name = '';
    $uri_editor = '';

    foreach($news as $indNews){
      $url_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/'. $indNews->id());

      $image_data = $this->getImage($indNews);
      $term_name = $this->getTags($indNews);

      $editor = $indNews->get('field_related_editor')->referencedEntities();
      $pre_title = $indNews->get('field_pre_title')->value;


      if(!empty($editor[0])){
        $editor_name = $editor[0]->getTitle();

        $fid_editor = $editor[0]->get('field_image')->getValue()[0]['target_id'];

        $file_editor = File::load($fid_editor);
        $uri_editor = $file_editor->getFileUri();
      }

      $data[] = [
        'title' => $indNews->getTitle(),
        'link' => $url_alias,
        'image_url' => $image_data['uri'],
        'image_alt' => $image_data['alt'],
        'editor_name' => $editor_name,
        'editor_image' => $uri_editor,
        'pre_title' => $pre_title,
        'tag' => $term_name
      ];
    }

    return $data;

  }



}
