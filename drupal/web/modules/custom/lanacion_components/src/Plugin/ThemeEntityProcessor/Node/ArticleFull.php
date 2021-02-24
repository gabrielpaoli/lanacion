<?php

namespace Drupal\lanacion_components\Plugin\ThemeEntityProcessor\Node;

use Drupal\file\Entity\File;
use Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorBase;
use Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorManager;
use Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns the structured data of an entity.
 *
 * @ThemeEntityProcessor(
 *   id = "node__article__full",
 *   label = @Translation("Node: Article: Full"),
 *   entity_type = "node",
 *   bundle = "article",
 *   view_mode = "full"
 * )
 */
class ArticleFull extends ThemeEntityProcessorBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.handlebars_theme_handler_entity_processor'),
      $container->get('plugin.manager.handlebars_theme_handler_field_processor')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ThemeEntityProcessorManager $themeEntityProcessorManager, ThemeFieldProcessorManager $themeFieldProcessorManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $themeEntityProcessorManager, $themeFieldProcessorManager);
  }

  /**
   * {@inheritdoc}
   */
  public function preprocessItemData(&$variables) {
    $node = $variables['elements']['#node'];

    $editor = $node->get('field_related_editor')->referencedEntities();

    $mvp = \Drupal::service('lanacion_mvpages.mostviewedpages');

    $relatedArticleNids = $mvp->getPagesMV($node);
    $relatedArticleNidsLifestyle = $mvp->getPagesMV($node, 3, 4);
    $relatedArticlesNidsEconomia = $mvp->getPagesMV($node, 4, 4);
    $relatedArticleNidsOtrosTemas = $mvp->getPagesMV($node, 4);

    $relatedArticles = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($relatedArticleNids);

    $relatedArticleLifestyle = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($relatedArticleNidsLifestyle);

    $relatedArticlesEconomia = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($relatedArticlesNidsEconomia);

    $relatedArticleOtrosTemas = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($relatedArticleNidsOtrosTemas);

    if(!empty($editor[0])){
      $editor_name = $editor[0]->getTitle();
    }

    $body = $node->get('body')->value;

    $variables['data'] = [
      'title' => $node->getTitle(),
      'related_articles' => $this->orderRelatedArticles($relatedArticles),
      'lifestyle_articles' => $this->orderRelatedArticles($relatedArticleLifestyle),
      'economia_articles' => $this->orderRelatedArticles($relatedArticlesEconomia),
      'others_articles' => $this->orderRelatedArticles($relatedArticleOtrosTemas),
      'body' =>  $body,
      'editor' => $editor_name
    ];
    
  }

  private function orderRelatedArticles($relatedArticles) {
    $data = [];

    foreach ($relatedArticles as $article) {
      $id = $article->id();
      $title = $article->getTitle();
      $image = $this->getImage($article);
      $url_alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/'. $id);
      

      $data[] = [
        'title' => $title,
        'image' => $image,
        'url_alias' => $url_alias
      ];
    }

    return $data;
  }

  private function getImage($article){
    $imageArray = [];

    $image = (!empty($article->get('field_image')->getValue()[0]) ? $article->get('field_image')->getValue()[0]['target_id'] : null);

    if(!empty($image)){
      $file = File::load($image);
      $imageArray = [
        'uri' => $file->getFileUri(),
        'alt' => $article->get('field_image')->getValue()[0]['alt']
      ];
    }

    return $imageArray;
  }


}
