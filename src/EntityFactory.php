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
   *
   * @throws \InvalidArgumentException
   */
  public static function get($type, $entity_id = NULL) {
    $class_name = ucfirst($type);
    $class = "\\EventBriteConnector\\Entity\\" . $class_name;

    if (class_exists($class)) {
      return new $class($entity_id);
    }
    $message = sprintf('Undefined Entity class name %s', $class_name);
    throw new \InvalidArgumentException($message);
  }

}
