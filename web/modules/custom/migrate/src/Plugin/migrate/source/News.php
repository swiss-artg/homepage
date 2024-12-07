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

    switch ($row->getSourceProperty('author')) {
      case '':
      case 'Vorstand':
      case 'Vorstand ':
        $row->setSourceProperty('author', null);
        break;

      case 'HB3YZE / OE8VIK':
        $row->setSourceProperty('author', 'hb3yze');
        break;

      case 'HB9AUR/HB3YZE':
        $row->setSourceProperty('author', 'hb9aur');
        break;

      case 'HB9CJD, HB9DSN':
        $row->setSourceProperty('author', 'hb9cjd');
        break;

      case 'HB9CTB, HB9CJD':
        $row->setSourceProperty('author', 'hb9ctb');
        break;

      case 'HB9GKL, Eduard Luzi':
        $row->setSourceProperty('author', 'hb9gkl');
        break;

      case 'HB9PAE und HB9BXQ':
      case 'HB9PAE, DB7GV':
      case 'HB9PAE, HB9CZF':
      case 'HB9PAE, Peter':
        $row->setSourceProperty('author', 'hb9pae');
        break;

      case 'Marc Balmer HB9SSB':
        $row->setSourceProperty('author', 'hb9ssb');
        break;

      case 'Michi HB3YZE':
        $row->setSourceProperty('author', 'hb3yze');
        break;

      default:
        $row->setSourceProperty('author', strtolower($row->getSourceProperty('author')));
        break;
    }

    return parent::prepareRow($row);
  }
}
