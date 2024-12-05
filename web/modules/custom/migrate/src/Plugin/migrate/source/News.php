<?php

namespace Drupal\swissartg_migrate\Plugin\migrate\source;

use Drupal\migrate\Annotation\MigrateSource;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Minimalistic example for a SqlBase source plugin.
 *
 * @MigrateSource(
 *   id = "news",
 *   source_module = "swissartg_migrate",
 * )
 */
class News extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // TODO: WHERE deleted != 1 AND hidden != 1 AND endtime = 0

    $query = $this->select('tx_news_domain_model_news', 'n')
      ->fields('n', [
        'uid',
        'title',
        'teaser',
        'bodytext',
        'author',
        'datetime',
        'crdate',
      ]);

     $group = $query->andConditionGroup()
       ->condition('deleted', 1, '!=')
       ->condition('hidden', 1, '!=')
       ->condition('endtime', 0);

      $query->condition($group);

      return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'uid' => $this->t('uid' ),
      'title'   => $this->t('title' ),
      'teaser'    => $this->t('teaser'),
      'bodytext'    => $this->t('bodytext'),
      'datetime'   => $this->t('datetime' ),
      'author'   => $this->t('author' ),
      'crdate'   => $this->t('crdate' ),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'uid' => [
        'type' => 'integer',
        'alias' => 'n',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $row->setSourceProperty('body',
      '<p>'
      . $row->getSourceProperty('teaser')
      . '</p>'
      . $row->getSourceProperty('bodytext'));
    return parent::prepareRow($row);
  }
}
