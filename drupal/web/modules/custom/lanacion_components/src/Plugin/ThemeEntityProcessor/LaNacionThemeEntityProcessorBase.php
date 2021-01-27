<?php

namespace Drupal\lanacion_components\Plugin\ThemeEntityProcessor;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\lanacion_components\CacheTagsDependencyTrait;
use Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorBase;
use Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorManager;
use Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;
use Drupal\Core\Entity\ContentEntityInterface;

class LaNacionThemeEntityProcessorBase extends ThemeEntityProcessorBase {

  use CacheTagsDependencyTrait;

  /** @var \Drupal\Core\Cache\CacheableMetadata */
  protected $cache;

  /**
   * ParagraphsBlockC06Image constructor.
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorManager $themeEntityProcessorManager
   * @param \Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorManager $themeFieldProcessorManager
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ThemeEntityProcessorManager $themeEntityProcessorManager,
    ThemeFieldProcessorManager $themeFieldProcessorManager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $themeEntityProcessorManager, $themeFieldProcessorManager);

    $this->cache = CacheableMetadata::createFromRenderArray(['#cache' => [
      'tags' => [],
      'contexts' => [],
    ]]);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.handlebars_theme_handler_entity_processor'),
      $container->get('plugin.manager.handlebars_theme_handler_field_processor')
    );
  }

  /**
   * Returns structured data of a single field.
   *
   * @param array $variables
   *
   * @throws \Exception
   */
  public function preprocessItemData(&$variables) {
  }

  /**
   * @param $variables
   */
  public function applyCacheMetaData(&$variables) {
    // Add the cache metadata if available.
    if (isset($variables['#cache'])) {
      $meta = CacheableMetadata::createFromRenderArray($variables);
      $this->cache = $this->cache->merge($meta);
    }

    $this->cache->applyTo($variables);
  }

  /**
   * @param array $field_list
   * @param array $options
   *
   * @return array|string
   * @throws \Exception
   */
  protected function getFieldData($field_list, $options = []) {

    $data = $this->themeFieldProcessorManager->getFieldData($field_list, $options);

    if (isset($field_list['#items']) &&  $field_list['#items'] instanceof EntityReferenceFieldItemList) {
      $refs = $field_list['#items']->referencedEntities();

      // Render all referenced entities.
      foreach ($refs as $ref) {
        if ($ref instanceof ContentEntityInterface) {
          $renderedEntity = $this->getEntityData($ref, $options);

          // Add the cache metadata if available.
          $meta = CacheableMetadata::createFromRenderArray($renderedEntity);
          $this->cache = $this->cache->merge($meta);
        }

        $meta2 = CacheableMetadata::createFromObject($ref);
        $this->cache = $this->cache->merge($meta2);
      }
    }

    return $data;
  }

  /**
   * Returns data of an entity.
   */
  protected function getEntityData(ContentEntityInterface $entity, $options = []) {
    $bundle = $entity->getEntityTypeId();
    $view_mode = isset($options['view_mode']) ? $options['view_mode'] : 'default';

    try {
      // Load the plugin that matches the entity.
      $plugin_id = $this->themeEntityProcessorManager->getProcessor($entity, $options);

      if ($plugin_id) {
        /** @var \Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorBase $processor */
        $processor = $this->themeEntityProcessorManager->createInstance($plugin_id);
      }

      if (!empty($processor)) {
        $data = \Drupal::entityTypeManager()->getViewBuilder($bundle)->view($entity, $view_mode);
        $preparedData = \Drupal::entityTypeManager()->getViewBuilder($bundle)->build($data);

        $preparedData['elements'] = $preparedData;
        $preparedData = array_merge($preparedData, $options);

        // Get a plugin that matches entity type and bundle.
        $processor->preprocessItemData($preparedData);

        return $preparedData;
      }
    }
    catch (\Exception $e) {

    }

    return [];
  }

  /**
   * @param $value
   *
   * @return null|string|string[]
   */
  protected function spanPlaceholder($value) {
    return preg_replace("/\*([^\*]*)\*/", "<span>$1</span>", $value);
  }

  /**
   * @return array|mixed
   */
  protected function getYamlData() {
    $pluginID = $this->getPluginDefinition()['id'];
    $dataYamlFile = drupal_get_path('theme', 'friso') . '/templates/block/' . str_replace('_', '-', $pluginID) . '/data.yaml';
    if (!file_exists($dataYamlFile)) {
      return [];
    }

    return Yaml::parseFile($dataYamlFile);
  }


  /**
   * @param array $variables
   *
   * @return mixed
   */
  protected function getComponentTitle(array $variables) {
    if (isset($variables['data']['title'])) {
      return $variables['data']['title'];
    }

    return $this->getPluginDefinition()['label'];
  }

  /**
   * @param array $cta
   *
   * @return mixed
   */
  protected function getCtaTitle(array $cta) {
    if (isset($cta['text'])) {
      return $cta['text'];
    }

    if (!isset($cta['title'])) {
      return NULL;
    }

    return $cta['title'];
  }

  /**
   * @param array $cta
   * @param $variables
   *
   * @return string
   */
  protected function getCtaType(array $cta, $variables) {
    if (isset($cta['target']) && $cta['target'] == '_blank') {
      return 'external';
    }

    return 'internal';
  }

  /**
   * @param array $variables
   *
   * @return mixed
   */
  protected function getModuleType(array $variables) {
    return str_replace('Node: ', '', $this->getPluginDefinition()['label']);
  }

  /**
   * @param $variables
   */
  protected function isNew(&$variables) {
    /** @var \Drupal\node\Entity\Node $node */
    $node = $variables['elements']['#node'];
    $currentTime = time();
    $publishedTime = $node->getChangedTime();
    $timeDifference = $currentTime - $publishedTime;
    if ($timeDifference < (30 * 24 * 60 * 60)) {
      $variables['data']['new'] = TRUE;
    }
  }


  /**
   * @param \Drupal\node\Entity\Node $node
   *
   * @return string
   */
  protected function identifyLinkTarget($node){

    if(strpos($node->toUrl()->toString(), 'sites/default/files') !== false || $node->hasField('field_gated_content')){
      return '_blank';
    }

    return '_self';
  }

}
