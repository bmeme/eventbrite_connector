<?php

namespace EventBriteConnector\Entity;

/**
 * Class Subcategory.
 *
 * @package EventBriteConnector\Entity
 */
class Subcategory extends Entity {

  /**
   * {@inheritdoc}
   */
  public function getEntityApiType() {
    return 'subcategories';
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
