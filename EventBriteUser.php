<?php
/**
 * EventBriteConnector.
 * Version: 1.0
 * Author: bmeme
 */

/**
 * Class EventBriteUser
 */
class EventBriteUser extends EventBriteEntity{

  /**
   * @param string $entity_id
   */
  public function __construct($entity_id = 'me') {
    parent::__construct($entity_id);
  }

  /**
   * @return string
   */
  public function getEntityAPIType() {
    return 'users';
  }
}