<?php

namespace EventBriteConnector\Entity;

/**
 * Class Series.
 *
 * @package EventBriteConnector\Entity
 */
class Series extends Entity {

  /**
   * {@inheritdoc}
   */
  public function getEntityApiType() {
    return 'series';
  }

}
