<?php

namespace EventBriteConnector\Entity;

/**
 * Class Webhook.
 *
 * @package EventBriteConnector\Entity
 */
class Webhook extends Entity {

  /**
   * @return string
   */
  public function getEntityApiType() {
    return 'webhooks';
  }

  /**
   * Load entity.
   *
   * @param array $conditions
   *   An array of conditions.
   * @param bool $reset
   *   A boolean indicating whether or not to reset stored data.
   *
   * @return Entity
   *   An entity instance.
   */
  public function load(array $conditions = array(), $reset = FALSE) {
    return parent::load('', $conditions, $reset);
  }

}
