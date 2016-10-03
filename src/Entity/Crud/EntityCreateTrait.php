<?php

namespace EventBriteConnector\Entity\Crud;

use EventBriteConnector\Connector;
use EventBriteConnector\Entity\Entity;

/**
 * Class EntityCreateTrait.
 *
 * @package EventBriteConnector\Entity
 */
trait EntityCreateTrait {

  /**
   * Constructs and save a new entity object.
   *
   * @param Connector $connector
   *   An Eventbrite Connector instance.
   * @param array $values
   *   (optional) An array of values to set, keyed by property name.
   *
   * @return \EventBriteConnector\Entity\Entity
   *   The new entity object.
   */
  public static function create(Connector $connector, array $values = array()) {
    $type = self::getEntityTypeName();
    $class = self::getEntityClass($type);
    /** @var Entity $entity */
    $entity = new $class();
    $entity->setValues($values);
    $entity->setConnector($connector);
    $result = $entity->save();

    if (!empty($result['id'])) {
      $entity->setEntityId($result['id']);
      $entity->load();
    }
    return $entity;
  }

  /**
   * Save entity.
   *
   * @return mixed
   *   The response data.
   */
  public function save() {
    $params = array(
      'method' => 'POST',
      'url' => $this->getEntityEndpoint(),
      'data' => $this->getDataToBeSaved(),
    );
    return $this->getConnector()->request($params);
  }

  /**
   * Save entity property.
   *
   * @param string $property
   *   A string representing the property name.
   * @param array $values
   *   An array of values to be saved.
   *
   * @return mixed
   *   The response data.
   */
  public function saveProperty($property, array $values = array()) {
    $params = array(
      'method' => 'POST',
      'url' => $this->getEntityEndpoint() . "$property/",
      'data' => $values,
    );
    return $this->getConnector()->request($params);
  }

}
