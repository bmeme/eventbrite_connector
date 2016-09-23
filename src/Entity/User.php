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
  public function __construct($entity_id) {
    if (empty($entity_id)) {
      $entity_id = 'me';
    }
    parent::__construct($entity_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityApiType() {
    return 'users';
  }

}
