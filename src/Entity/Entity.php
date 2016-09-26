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
   * The Eventbrite Connector instance.
   *
   * @var Connector $connector
   */
  protected $connector;

  /**
   * The entity data array of existing entities.
   *
   * @var array $data
   */
  protected $data;

  /**
   * The key of the last loaded data set.
   *
   * @var string $activeDataSet
   */
  protected $activeDataSet;

  /**
   * The entity values array of new entities.
   *
   * @var array $values
   */
  protected $values;

  /**
   * Whether or not this entity is new.
   *
   * @var bool $isNew
   */
  protected $isNew;

  /**
   * Entity constructor.
   *
   * @param $entity_id
   *   The entity id.
   */
  public function __construct($entity_id) {
    $this->setEntityId($entity_id);
    $this->data = array();
    $this->values = array();
    $this->isNew = TRUE;
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
   * Is new.
   *
   * @return bool
   *   A boolean indicating whether or not this entity is new.
   */
  public function isNew() {
    return $this->isNew;
  }

  /**
   * Set connector.
   *
   * @param \EventBriteConnector\Connector $connector
   *   A Connector instance.
   *
   * @return Entity
   *   The entity instance.
   */
  public function setConnector(Connector $connector) {
    $this->connector = $connector;
    return $this;
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
    $this->activeDataSet = $key;
    $this->data[$key] = $data;
    $this->isNew = FALSE;
  }

  /**
   * Set values.
   *
   * @param array $values
   *   The values array.
   */
  public function setValues(array $values = array()) {
    $this->values = $values;
  }

  /**
   * Get values.
   *
   * @return array
   *   The values array.
   */
  public function getValues() {
    return $this->values;
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
   *   A boolean indicating whether or not to reset stored data.
   *
   * @return $this
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
   * Save entity.
   *
   * @return mixed
   *   The response data.
   */
  public function save() {
    $url = $this->getEntityEndpoint() . '/';

    if (!$this->isNew()) {
      $url .= $this->getEntityId();
    }

    $params = array(
      'method' => 'POST',
      'url' => $url,
      'data' => $this->getDataToBeSaved(),
    );
    return $this->getConnector()->request($params);
  }

  /**
   * Get data to be saved.
   *
   * @return array
   *   An array of data to be saved.
   */
  protected function getDataToBeSaved() {
    $data = $this->getValues();
    if ($this->isNew()) {
      return $data;
    }

    if (empty($this->getEntityId())) {
      $type = $this->getEntityApiType();
      $message = sprintf('Cannot update entity of type %s without an entity id', $type);
      throw new \RuntimeException($message);
    }

    return $this->getData($this->activeDataSet);
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
