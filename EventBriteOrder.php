<?php
/**
 * EventBriteConnector.
 * Version: 1.0
 * Author: bmeme
 */

/**
 * Class EventBriteOrder
 */
class EventBriteOrder extends EventBriteEntity{

  /**
   * @return string
   */
  public function getEntityAPIType() {
    return 'orders';
  }

  /**
   * @param array $conditions
   * @param bool $reset
   * @return $this
   */
  public function load(array $conditions = array(), $reset = FALSE) {
    return parent::load('', $conditions, $reset);
  }
}