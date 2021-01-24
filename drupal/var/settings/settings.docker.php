<?php

// Show errors.
$config['system.logging']['error_level'] = 'all';

// Set no cache
$config['system.performance']['cache']['page']['max_age'] = 0;
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['cache']['bins']['render'] = 'cache.backend.null';

// Include services.
$settings['container_yamls'][] = "/var/www/var/settings/services/services.docker.yml";

// Activate dev config
$config['config_split.config_split.dev']['status'] = TRUE;

// Disable rename_admin_paths rewrites.
$config['rename_admin_paths.settings']['admin_path'] = TRUE;
$config['rename_admin_paths.settings']['user_path'] = TRUE;

$databases['default']['default'] = array (
  'database' => 'lanacion',
  'username' => 'lanacion',
  'password' => 'lanacion',
  'prefix' => '',
  'host' => 'mysql',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$config['system.performance']['css']['gzip'] = FALSE;
$config['system.performance']['js']['gzip'] = FALSE;
$config['system.performance']['response']['gzip'] = FALSE;
