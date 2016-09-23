<?php

namespace EventBriteConnector\Entity;

/**
 * Class Event.
 *
 * @package EventBriteConnector\Entity
 */
class Event extends Entity {

  /**
   * {@inheritdoc}
   */
  public function getEntityApiType() {
    return 'events';
  }

}
