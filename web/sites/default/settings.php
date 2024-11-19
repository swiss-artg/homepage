<?php

// phpcs:ignoreFile

/**
 * Database settings:
 *
 * IMPORTANT: Do not add database connections here.
 *
 * Database connectsion are provided by ddev in `settings.ddev.php` or can be
 * provided manually in `settings.local.php`.
 */
$databases = [];

/**
 * Location of the site configuration files.
 */
$settings['config_sync_directory'] = $app_root . '/../config/default/';

/**
 * Salt for one-time login links, cancel links, form tokens, etc.
 *
 * TODO: Securly configure hash salt.
 */
$settings['hash_salt'] = 'OD6jgpd7SWW8Afs9WKz2qWvqNQZx60PNc8DWYuy0HcJhqAxdn42OI5H-65Zf3qwChzjlDTkQGQ';

/**
 * Deployment identifier.
 */
$settings['deployment_identifier'] = \Drupal::VERSION;

/**
 * Access control for update.php script.
 */
$settings['update_free_access'] = FALSE;


/**
 * Authorized file system operations:
 *
 * Disable autorized file system operations enforcing new modules to be added
 * and updates only to be pushed trhough a deployment process from source code.
 */
$settings['allow_authorize_operations'] = FALSE;


/**
 * Private file path:
 */
$settings['file_private_path'] = $app_root . '/../private';


/**
 * Load services definition file.
 */
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';

// Automatically generated include for settings managed by ddev.
if (getenv('IS_DDEV_PROJECT') == 'true' && file_exists($app_root . '/' . $site_path . '/settings.ddev.php')) {
  include $app_root . '/' . $site_path . '/settings.ddev.php';
}

/**
 * Load local development override configuration, if available.
 *
 * Keep this code block at the end of this file to take full effect.
 */

if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}
