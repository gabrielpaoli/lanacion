<?php

// @codingStandardsIgnoreFile

$databases = [];
$current_env = 'aws';
$settings['hash_salt'] = 'tWXPd2rQqdpIzgvMDPqtGE4vj1dPEl9dLaCzBMR2wUNjxRqV3UAeukNF28ckK7dxBUmIBA8wrw';
$settings['update_free_access'] = FALSE;
$settings['form_cache_expiration'] = 21600;
$settings['file_chmod_directory'] = 0775;
$settings['file_chmod_file'] = 0664;
$settings['file_temp_path'] = '/tmp';
$settings['session_write_interval'] = 180;
$settings['maintenance_theme'] = 'bartik';
$settings['file_scan_ignore_directories'] = [
  'node_modules',
  'bower_components',
];
$settings['entity_update_batch_size'] = 50;
$settings['entity_update_backup'] = TRUE;
$settings['migrate_node_migrate_type_classic'] = FALSE;

$settings['config_sync_directory'] = '/var/www/var/config/sync';

if (isset($_SERVER['DOCKER_ENV']) || isset($_SERVER['DOCKER_ENV'])) {
  $current_env = 'docker';
  require '/var/www/var/settings/settings.docker.php';
}

$settings['current_env'] = $current_env;

if($current_env == 'aws'){
  if (file_exists('/var/site-php/phoenix/prod-settings.inc')) {
    $currentEnv = 'aws';
    $envId = 'prod';
    require '/var/site-php/amsterdam-prod/prod-settings.inc';
  }
  $settings['container_yamls'][] = DRUPAL_ROOT . '/../var/settings/services/services.prod.yml';
}

//Global All Settings.
if (file_exists(DRUPAL_ROOT . '/../var/settings/settings.all.php')) {
  require DRUPAL_ROOT . '/../var/settings/settings.all.php';
}
