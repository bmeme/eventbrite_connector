<?php
/**
 * EventBriteConnector.
 * Version: 1.0
 * Author: bmeme
 */

/**
 * Class EventBriteEntity
 */
abstract class EventBriteEntity {

  private $entity_id;
  private $connector;
  private $data;

  /**
   * @param $entity_id
   */
  public function __construct($entity_id) {
    $this->setEntityId($entity_id);
    $this->data = array();
  }

  /**
   * @param $entity_id
   */
  public function setEntityId($entity_id) {
    $this->entity_id = $entity_id;
  }

  /**
   * @return mixed
   */
  public function getEntityId() {
    return $this->entity_id;
  }

  /**
   * @param EventBriteConnector $connector
   */
  public function setConnector(EventBriteConnector $connector) {
    $this->connector = $connector;
  }

  /**
   * @return EventBriteConnector
   */
  public function getConnector() {
    return $this->connector;
  }

  /**
   * @param $key
   * @param $data
   */
  private function setData($key, $data) {
    $this->data[$key] = $data;
  }

  /**
   * @param string $key
   * @return array
   */
  public function getData($key = '') {
    return (!empty($key)) ? $this->data[$key] : $this->data;
  }

  /**
   * @return string
   *  The Entity API endpoint URL.
   */
  public function getEntityEndpoint() {
    $endpoint = array(
      $this->getConnector()->getEndpoint(),
      $this->getEntityAPIType(),
      $this->getEntityId()
    );

    return implode('/', $endpoint);
  }

  /**
   * @param string $property
   *  An Entity API property identified by an API URL path chunk.
   *  Example: orders, attendees, discounts, etc.
   * @param array $conditions
   * @param bool $reset
   * @return EventBriteEntity $this
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
   * @param $property
   * @param array $conditions
   * @return string
   */
  public function buildDataKey($property, array $conditions) {
    $property = empty($property) ? $this->getEntityId() : $property;
    $conditions = http_build_query($conditions);

    return empty($conditions) ? $property : implode(':', array($property, $conditions));
  }

  /**
   * @return string
   *  The API URL path part that identifies this Entity.
   *  Don't use slashes.
   *  Example: users, events, orders, etc.
   */
  abstract public function getEntityAPIType();
}