<?php

namespace Drupal\swissartg_migrate\Plugin\migrate\source;

use Drupal\migrate\Annotation\MigrateSource;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Minimalistic example for a SqlBase source plugin.
 *
 * @MigrateSource(
 *   id = "users",
 *   source_module = "swissartg_migrate",
 * )
 */
class User extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('be_users', 'u')
      ->fields('u', [
        'username',
        'email',
      ]);

     $group = $query->andConditionGroup()
       ->condition('username', ['admin', 'dieter', 'mbalmer-admin', 'hb9heh0'], 'NOT IN')
       ->condition('username', '%-redaktor', 'NOT LIKE')
       ->condition('username', '%-vorstand', 'NOT LIKE');

      $query->condition($group);

      return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'username' => $this->t('username'),
      'email' => $this->t('email'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'username' => [
        'type' => 'string',
        'alias' => 'u',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Clean username outliers
    $user = $row->getSourceProperty('username');

    switch ($user) {
      case 'mbalmer':
        $row->setSourceProperty('username', 'hb9ssb');
        break;
    }

    return parent::prepareRow($row);
  }
}
