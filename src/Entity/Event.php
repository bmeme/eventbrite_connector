<?php

namespace EventBriteConnector\Entity;

use EventBriteConnector\Entity\Crud\EntityCreateTrait;
use EventBriteConnector\Entity\Crud\EntityDeleteTrait;
use EventBriteConnector\Entity\Crud\EntityUpdateTrait;

/**
 * Class Event.
 *
 * @package EventBriteConnector\Entity
 */
class Event extends Entity {

  use EntityCreateTrait;
  use EntityUpdateTrait;
  use EntityDeleteTrait;

  /**
   * {@inheritdoc}
   */
  public function getEntityApiType() {
    return 'events';
  }

}
