<?php

namespace Drupal\swissartg_migrate\Plugin\migrate\process;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Attribute\MigrateProcess;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateLookupInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Processes Typo 3 file URLs and replace them with the URL of an imported file.
 *
 * @MigrateProcessPlugin(
 *  id = "typo3_inline_file"
 * )
 */
class Typo3InlineFile extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The migration to be executed.
   *
   * @var \Drupal\migrate\Plugin\MigrationInterface
   */
  protected $migration;

  /**
   * The migrate lookup service.
   *
   * @var \Drupal\migrate\MigrateLookupInterface
   */
  protected $migrateLookup;

  /**
   * @var Drupal\Core\Entity\ContentEntityStorageInterface
   */
  protected ContentEntityStorageInterface $fileStorage;

  /**
   * Constructs a Typo3InlineFile migration process plugin.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The Migration the plugin is being used in.
   * @param \Drupal\migrate\MigrateLookupInterface $migrate_lookup
   *   The migrate lookup service.
   * @param Drupal\Core\Entity\ContentEntityStorageInterface $file_storage
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, MigrateLookupInterface $migrate_lookup, ContentEntityStorageInterface $file_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->migration = $migration;
    $this->migrateLookup = $migrate_lookup;
    $this->fileStorage = $file_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('migrate.lookup'),
      $container->get('entity_type.manager')->getStorage('file'),
    );
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\migrate\MigrateException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value)) {
      return $value;
    }

    $crawler = new Crawler($value);
    $lookup_migration_id = $this->configuration['migration'];

    foreach ($crawler->filter('a') as $node) {
      $url = $node->getAttribute('href');

      if (!str_starts_with($url, 't3://file')) {
        continue;
      }

      parse_str(parse_url($url, PHP_URL_QUERY), $query);

      if (!array_key_exists('uid', $query)) {
        continue;
      }

      $node->setAttribute('data-entity-typo3-uid', $query['uid']);
      $node->setAttribute('data-entity-type', 'file');
      $result = $this->migrateLookup->lookup($lookup_migration_id, array($query['uid']));

      if (empty($result[0]['fid'])) {
        continue;
      }

      $file = $this->fileStorage->load($result[0]['fid']);
      $node->setAttribute('data-entity-uuid', $file->uuid());
      $node->setAttribute('href', $file->createFileUrl());
    }

    return $crawler->html();
  }
}
