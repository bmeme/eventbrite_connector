<?php

namespace EventBriteConnector\Entity;

use EventBriteConnector\Entity\Crud\EntityCreateTrait;
use EventBriteConnector\Entity\Crud\EntityUpdateTrait;

/**
 * Class Venue.
 *
 * @package EventBriteConnector\Entity
 */
class Venue extends Entity {

  use EntityCreateTrait;
  use EntityUpdateTrait;

  /**
   * {@inheritdoc}
   */
  public function getEntityApiType() {
    return 'venues';
  }

}
