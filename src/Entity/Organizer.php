<?php

namespace EventBriteConnector\Entity;

use EventBriteConnector\Entity\Crud\EntityCreateTrait;
use EventBriteConnector\Entity\Crud\EntityUpdateTrait;

/**
 * Class Organizer.
 *
 * @package EventBriteConnector\Entity
 */
class Organizer extends Entity {

  use EntityCreateTrait;
  use EntityUpdateTrait;

  /**
   * {@inheritdoc}
   */
  public function getEntityApiType() {
    return 'organizers';
  }

}
