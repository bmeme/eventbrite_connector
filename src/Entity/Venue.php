<?php

namespace EventBriteConnector\Entity;

/**
 * Class Venue.
 *
 * @package EventBriteConnector\Entity
 */
class Venue extends Entity {

  /**
   * {@inheritdoc}
   */
  public function getEntityApiType() {
    return 'venues';
  }

}
