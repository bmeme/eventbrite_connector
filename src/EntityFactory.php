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
    $class = self::getEntityClass($type);
    return new $class($entity_id);
  }

  public static function create($type, array $values = array()) {
    $class = self::getEntityClass($type);
    $entity = new $class();
    $entity->setValues($values);
    return $entity;
  }

  /**
   * Get Entity class.
   *
   * @param string $type
   *   The entity type name.
   *
   * @return string
   *   The Entity class name.
   *
   * @throws \InvalidArgumentException
   */
  public static function getEntityClass($type) {
    $class_name = ucfirst($type);
    $class = "\\EventBriteConnector\\Entity\\" . $class_name;

    if (class_exists($class)) {
      return $class;
    }
    $message = sprintf('Undefined Entity class name %s', $class_name);
    throw new \InvalidArgumentException($message);
  }

}
