<?php

namespace Drupal\swissartg_migrate\Plugin\migrate\source;

use Drupal\migrate\Annotation\MigrateSource;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * @MigrateSource(
 *   id = "images",
 *   source_module = "swissartg_migrate",
 * )
 */
class Images extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('sys_file', 'f')
      ->fields('f', [
        'uid',
        'name',
        'identifier',
      ]);

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'uid' => $this->t('uid' ),
      'name'   => $this->t('name' ),
      'identifier'    => $this->t('identifer'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'uid' => [
        'type' => 'integer',
        'alias' => 'f',
      ],
    ];
  }
}
