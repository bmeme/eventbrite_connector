<?php

namespace EventBriteConnector\Entity;

/**
 * Class User.
 *
 * @package EventBriteConnector\Entity
 */
class User extends Entity {

  /**
   * {@inheritdoc}
   */
  public function __construct($entity_id = 'me') {
    parent::__construct($entity_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityApiType() {
    return 'users';
  }

}
