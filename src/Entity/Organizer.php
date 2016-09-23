<?php

namespace EventBriteConnector\Entity;

/**
 * Class Organizer.
 *
 * @package EventBriteConnector\Entity
 */
class Organizer extends Entity {

  /**
   * {@inheritdoc}
   */
  public function getEntityApiType() {
    return 'organizers';
  }

}
