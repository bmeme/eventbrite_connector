<?php

namespace EventBriteConnector\Entity;

/**
 * Class Order.
 *
 * @package EventBriteConnector\Entity
 */
class Order extends Entity {

  /**
   * @return string
   */
  public function getEntityApiType() {
    return 'orders';
  }

  /**
   * Load entity.
   *
   * @param array $conditions
   *   An array of conditions.
   * @param bool $reset
   *   A boolean indicating whether or not to rest stored data.
   *
   * @return Entity
   *   An entity instance.
   */
  public function load(array $conditions = array(), $reset = FALSE) {
    return parent::load('', $conditions, $reset);
  }

}
