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
   * @param string|NULL $entity_id
   *   The entity id.
   */
  public function __construct($entity_id = NULL) {
    $this->isNew = TRUE;
    $this->data = array();
    $this->values = array();
    $this->setEntityId($entity_id);
  }

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
  public static function getInstance($type, $entity_id = NULL) {
    static $instances;

    if (!isset($instances[$type][$entity_id])) {
      $class = self::getEntityClass($type);
      $instances[$type][$entity_id] = new $class($entity_id);
    }

    return $instances[$type][$entity_id];
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
        'url' => $this->getEntityEndpoint() . $property,
        'data' => $conditions
      );

      $response = $this->getConnector()->request($params);
      $this->setData($key, $response);
    }

    return $this;
  }

  /**
   * Get Entity type name.
   *
   * @return string
   *   The name of the entity.
   */
  public function getEntityTypeName() {
    $path = explode('\\', get_called_class());
    return strtolower(array_pop($path));
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

  /**
   * Set entity id.
   *
   * @param string $entity_id
   *   The entity id.
   */
  public function setEntityId($entity_id) {
    if (!empty($entity_id)) {
      $this->isNew = FALSE;
    }
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
    $this->setValues($this->getNormalizedData());
  }

  /**
   * Set values.
   *
   * @param array $values
   *   The values array.
   */
  protected function setValues(array $values = array()) {
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
   * Get all loaded data.
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
   * Get data from active data set (last loaded data).
   *
   * @return array
   *   The data value.
   */
  public function getActiveData() {
    return $this->getData($this->activeDataSet);
  }

  /**
   * Get entity endpoint.
   *
   * @return string
   *   The Entity API endpoint URL.
   */
  public function getEntityEndpoint() {
    $endpoint = array_filter(array(
      $this->getConnector()->getEndpoint(),
      $this->getEntityApiType(),
      $this->getEntityId(),
    ));
    return implode('/', $endpoint) . '/';
  }

  /**
   * Get data to be saved.
   *
   * @return array
   *   An array of data to be saved.
   */
  protected function getDataToBeSaved() {
    if ($this->isNew()) {
      return $this->getValues();
    }

    if (empty($this->getEntityId())) {
      $type = $this->getEntityApiType();
      $message = sprintf('Cannot update entity of type %s without an entity id', $type);
      throw new \RuntimeException($message);
    }

    return $this->getNormalizedData();
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
      $conditions,
    ));
  }

  /**
   * Parse data key.
   *
   * @param string $key
   *   A string representing the data key.
   *
   * @return array
   *   An associative array containing property and conditions.
   */
  public function parseDataKey($key) {
    $conditions = array();
    list($property, $query) = explode(':', $key);

    if ($property == $this->entityId) {
      $property = '';
    }

    if (!empty($query)) {
      parse_str($query, $conditions);
    }

    return array(
      'property' => $property,
      'conditions' => $conditions,
    );
  }

  /**
   * Get normalized data.
   *
   * @return array
   *   An array of normalized data.
   */
  protected function getNormalizedData() {
    $data = $this->normalizeData($this->getEntityTypeName(), $this->getActiveData());
    return $data;
  }

  /**
   * Normalize data.
   *
   * @param string $prefix
   *   The entity property prefix.
   * @param array $data
   *   An array of data to be normalized.
   * @param array $normalized
   *   The normalized data array.
   *
   * @return array
   *   An array of normalized data.
   */
  private function normalizeData($prefix, array $data, &$normalized = array()) {
    foreach ($data as $key => $value) {
      $element = "$prefix.$key";
      if (is_array($value)) {
        $this->normalizeData($element, $value, $normalized);
      }
      else {
        $normalized[$element] = $value;
      }
    }

    return $normalized;
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
