<?php

namespace EventBriteConnector\Entity;

use EventBriteConnector\Connector;

/**
 * Class Entity.
 *
 * @package EventBriteConnector\Entity
 */
abstract class Entity {

  /**
   * The entity id.
   *
   * @var string $entityId
   */
  protected $entityId;

  /**
   * The entity id.
   *
   * @var Connector $entityId
   */
  protected $connector;

  /**
   * The entity id.
   *
   * @var string $entityId
   */
  protected $data;

  /**
   * Entity constructor.
   *
   * @param $entity_id
   *   The entity id.
   */
  public function __construct($entity_id) {
    $this->setEntityId($entity_id);
    $this->data = array();
  }

  /**
   * Set entity id.
   *
   * @param string $entity_id
   *   The entity id.
   */
  public function setEntityId($entity_id) {
    $this->entityId = $entity_id;
  }

  /**
   * Get entity id.
   *
   * @return string
   *   The entity id.
   */
  public function getEntityId() {
    return $this->entityId;
  }

  /**
   * Set connector.
   *
   * @param \EventBriteConnector\Connector $connector
   *   A Connector instance.
   */
  public function setConnector(Connector $connector) {
    $this->connector = $connector;
  }

  /**
   * Get connector.
   *
   * @return \EventBriteConnector\Connector
   *   The connector instance.
   */
  public function getConnector() {
    return $this->connector;
  }

  /**
   * Set data.
   *
   * @param string $key
   *   The data key.
   * @param mixed $data
   *   The data value.
   */
  protected function setData($key, $data) {
    $this->data[$key] = $data;
  }

  /**
   * Get data.
   *
   * @param string $key
   *   The data key.
   *
   * @return array
   *   The data value.
   */
  public function getData($key = '') {
    return (!empty($key)) ? $this->data[$key] : $this->data;
  }

  /**
   * Get entity endpoint.
   *
   * @return string
   *   The Entity API endpoint URL.
   */
  public function getEntityEndpoint() {
    $endpoint = array(
      $this->getConnector()->getEndpoint(),
      $this->getEntityApiType(),
      $this->getEntityId()
    );

    return implode('/', $endpoint);
  }

  /**
   * Load entity.
   *
   * @param string $property
   *   An Entity API property identified by an API URL path chunk.
   *   Example: orders, attendees, discounts, etc.
   * @param array $conditions
   *   An array of conditions.
   * @param bool $reset
   *   A boolean indicating whether or not to rest stored data.
   *
   * @return Entity
   *   The entity instance.
   */
  public function load($property = '', array $conditions = array(), $reset = FALSE) {
    $key = $this->buildDataKey($property, $conditions);

    if (!isset($this->data[$key]) || $reset) {
      $params = array(
        'url' => $this->getEntityEndpoint() . '/' . $property,
        'data' => $conditions
      );

      $response = $this->getConnector()->request($params);
      $this->setData($key, $response);
    }

    return $this;
  }

  /**
   * Build data key.
   *
   * @param string $property
   *   The property name.
   * @param array $conditions
   *   An array of conditions.
   *
   * @return string
   *   A string representing the data key.
   */
  public function buildDataKey($property, array $conditions) {
    $property = empty($property) ? $this->getEntityId() : $property;
    $conditions = http_build_query($conditions);

    return empty($conditions) ? $property : implode(':', array(
      $property,
      $conditions
    ));
  }

  /**
   * Get Entity Api type.
   *
   * @return string
   *   The API URL path part that identifies this Entity.
   *   Example: users, events, orders, etc.
   */
  abstract public function getEntityApiType();

}
