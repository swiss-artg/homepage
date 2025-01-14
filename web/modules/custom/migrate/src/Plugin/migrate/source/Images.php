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

     //$group = $query->andConditionGroup()
     //  ->condition('deleted', 1, '!=')
     //  ->condition('hidden', 1, '!=')
     //  ->condition('endtime', 0);
     //$query->condition($group);

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
