<?php

namespace Drupal\lanacion_components\Plugin\ThemeEntityProcessor\Node;

use Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorBase;
use Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorManager;
use Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns the structured data of an entity.
 *
 * @ThemeEntityProcessor(
 *   id = "node__page__full",
 *   label = @Translation("Node: Page: Full"),
 *   entity_type = "node",
 *   bundle = "page",
 *   view_mode = "full"
 * )
 */
class PageFull extends ThemeEntityProcessorBase {

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
    $variables['data'] = [
      'title' => $node->getTitle(),
    ];
  }



}
