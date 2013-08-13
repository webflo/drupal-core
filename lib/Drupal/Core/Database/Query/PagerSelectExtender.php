<?php

/**
 * @file
 * Definition of Drupal\Core\Database\Query\PagerSelectExtender
 */

namespace Drupal\Core\Database\Query;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\SelectExtender;
use Drupal\Core\Database\Query\SelectInterface;

/**
 * Query extender for pager queries.
 *
 * This is the "default" pager mechanism.  It creates a paged query with a fixed
 * number of entries per page.
 */
class PagerSelectExtender extends SelectExtender {

  /**
   * The highest element we've autogenerated so far.
   *
   * @var int
   */
  static $maxElement = 0;

  /**
   * The number of elements per page to allow.
   *
   * @var int
   */
  protected $limit = 10;

  /**
   * The unique ID of this pager on this page.
   *
   * @var int
   */
  protected $element = NULL;

  /**
   * The count query that will be used for this pager.
   *
   * @var SelectQueryInterface
   */
  protected $customCountQuery = FALSE;

  public function __construct(SelectInterface $query, Connection $connection) {
    parent::__construct($query, $connection);

    // Add pager tag. Do this here to ensure that it is always added before
    // preExecute() is called.
    $this->addTag('pager');
  }

  /**
   * Override the execute method.
   *
   * Before we run the query, we need to add pager-based range() instructions
   * to it.
   */
  public function execute() {

    // Add convenience tag to mark that this is an extended query. We have to
    // do this in the constructor to ensure that it is set before preExecute()
    // gets called.
    if (!$this->preExecute($this)) {
      return NULL;
    }

    // A NULL limit is the "kill switch" for pager queries.
    if (empty($this->limit)) {
      return;
    }
    $this->ensureElement();

    $total_items = $this->getCountQuery()->execute()->fetchField();
    $current_page = pager_default_initialize($total_items, $this->limit, $this->element);
    $this->range($current_page * $this->limit, $this->limit);

    // Now that we've added our pager-based range instructions, run the query normally.
    return $this->query->execute();
  }

  /**
   * Ensure that there is an element associated with this query.
   * If an element was not specified previously, then the value of the
   * $maxElement counter is taken, after which the counter is incremented.
   *
   * After running this method, access $this->element to get the element for this
   * query.
   */
  protected function ensureElement() {
    if (!isset($this->element)) {
      $this->element = self::$maxElement++;
    }
  }

  /**
   * Specify the count query object to use for this pager.
   *
   * You will rarely need to specify a count query directly.  If not specified,
   * one is generated off of the pager query itself.
   *
   * @param \Drupal\Core\Database\Query\SelectInterface $query
   *   The count query object.  It must return a single row with a single column,
   *   which is the total number of records.
   */
  public function setCountQuery(SelectInterface $query) {
    $this->customCountQuery = $query;
  }

  /**
   * Retrieve the count query for this pager.
   *
   * The count query may be specified manually or, by default, taken from the
   * query we are extending.
   *
   * @return \Drupal\Core\Database\Query\SelectInterface
   *   A count query object.
   */
  public function getCountQuery() {
    if ($this->customCountQuery) {
      return $this->customCountQuery;
    }
    else {
      return $this->query->countQuery();
    }
  }

  /**
   * Specify the maximum number of elements per page for this query.
   *
   * The default if not specified is 10 items per page.
   *
   * @param $limit
   *   An integer specifying the number of elements per page.  If passed a false
   *   value (FALSE, 0, NULL), the pager is disabled.
   */
  public function limit($limit = 10) {
    $this->limit = $limit;
    return $this;
  }

  /**
   * Specify the element ID for this pager query.
   *
   * The element is used to differentiate different pager queries on the same
   * page so that they may be operated independently.  If you do not specify an
   * element, every pager query on the page will get a unique element.  If for
   * whatever reason you want to explicitly define an element for a given query,
   * you may do so here.
   *
   * Setting the element here also increments the static $maxElement counter,
   * which is used for determining the $element when there's none specified.
   *
   * Note that no collision detection is done when setting an element ID
   * explicitly, so it is possible for two pagers to end up using the same ID
   * if both are set explicitly.
   *
   * @param $element
   */
  public function element($element) {
    $this->element = $element;
    if ($element >= self::$maxElement) {
      self::$maxElement = $element + 1;
    }
    return $this;
  }
}
