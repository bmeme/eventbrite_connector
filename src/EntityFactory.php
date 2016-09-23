<?php

namespace EventBriteConnector;
use EventBriteConnector\Entity\Entity;

/**
 * Class EntityFactory.
 *
 * @package EventBriteConnector
 */
class EntityFactory {

  /**
   * Get Entity instance.
   *
   * @param string $type
   *   The entity type name.
   * @param string $entity_id
   *   The entity id.
   *
   * @return Entity
   *   The Entity instance.
   */
  public static function get($type, $entity_id = NULL) {
    $class = "\\EventBriteConnector\\Entity\\" . ucfirst($type);
    return new $class($entity_id);
  }

}
