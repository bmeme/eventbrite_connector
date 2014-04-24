<?php

/**
 * EventBriteConnector.
 * Version: 1.0
 * Author: bmeme
 */

/**
 * Class EventBriteConnector
 */
class EventBriteConnector {

  /**
   * The Eventbrite OAuth endpoint for access token generation.
   */
  const EVENTBRITE_OAUTH_ENDPOINT = 'https://www.eventbrite.com/oauth';

  private $endpoint;
  private $client_secret;
  private $client_id;
  private $access_token;
  private $entities;

  public function __construct($client_secret, $client_id, $access_token = NULL) {
    $this->setEndpoint('https://www.eventbriteapi.com/v3');
    $this->setClientSecret($client_secret);
    $this->setClientId($client_id);
    $this->setAccessToken($access_token);
  }

  /**
   * @param string $endpoint
   */
  public function setEndpoint($endpoint) {
    $this->endpoint = $endpoint;
  }

  /**
   * @return string
   */
  public function getEndpoint() {
    return $this->endpoint;
  }

  /**
   * @param $client_secret
   * @return $this
   */
  public function setClientSecret($client_secret) {
    $this->client_secret = $client_secret;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getClientSecret() {
    return $this->client_secret;
  }

  /**
   * @param $client_id
   * @return $this
   */
  public function setClientId($client_id) {
    $this->client_id = $client_id;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getClientId() {
    return $this->client_id;
  }

  /**
   * @param $access_token
   * @return $this
   */
  public function setAccessToken($access_token = NULL) {
    if (empty($access_token) && !empty($_SESSION['eb_access_token'])) {
      $access_token = $_SESSION['eb_access_token'];
    }

    $this->access_token = $access_token;
    $_SESSION['eb_access_token'] = $access_token;

    return $this;
  }

  /**
   * @return mixed
   * @throws RuntimeException
   */
  public function getAccessToken() {
    if (!empty($_SESSION['eb_access_token'])) {
      $this->setAccessToken($_SESSION['eb_access_token']);
    }

    if (empty($this->access_token)) {
      $_SESSION['eb_last_request'] = array(
        'server' => $_SERVER,
        'get' => $_GET,
        'post' => $_POST,
      );

      throw new RuntimeException('Missing Access Token');
    }

    return $this->access_token;
  }

  /**
   * @return $this
   */
  public function deleteAccessToken() {
    unset($this->access_token);
    unset($_SESSION['eb_access_token']);

    return $this;
  }

  /**
   * @param string $entity_api_type
   * @return mixed
   */
  public function getEntities($entity_api_type = '') {
    return (!empty($entity_api_type)) ? $this->entities[$entity_api_type] : $this->entities;
  }

  /**
   * @param $entity_api_type
   * @param $entity_id
   * @return EventBriteEntity
   */
  public function getEntity($entity_api_type, $entity_id) {
    return $this->entities[$entity_api_type][$entity_id];
  }

  /**
   * @param mixed $entities
   */
  private function setEntities($entities) {
    $this->entities = $entities;
  }

  /**
   * @param EventBriteEntity $entity
   * @return EventBriteEntity
   */
  public function addEntity(EventBriteEntity $entity) {
    $entities = $this->getEntities();

    if (empty($entities[$entity->getEntityAPIType()][$entity->getEntityId()])) {
      $entity->setConnector($this);
      $entities[$entity->getEntityAPIType()][$entity->getEntityId()] = $entity;

      $this->setEntities($entities);
    }

    return $entity;
  }

  /**
   * @param EventBriteEntity $entity
   * @return $this
   */
  public function removeEntity(EventBriteEntity $entity) {
    $entities = $this->getEntities();

    if (!empty($entities[$entity->getEntityAPIType()][$entity->getEntityId()])) {
      unset($entities[$entity->getEntityAPIType()][$entity->getEntityId()]);
    }

    $this->setEntities($entities);

    return $this;
  }

  /**
   * Generate an URL for redirection to Eventbrite Authorization page.
   *
   * @return string
   *  The redirect URL.
   */
  public function getAuthorizationURL() {
    $params = array(
      'response_type' => 'code',
      'client_id' => $this->getClientId()
    );

    return self::EVENTBRITE_OAUTH_ENDPOINT . '/authorize?' . http_build_query($params);
  }

  /**
   * Invokes the Eventbrite API to get and set a valid Access Token.
   *
   * @param $auth_code
   * @throws RuntimeException
   */
  public function getOAuthToken($auth_code) {
    $response = $this->request($this->buildOAuthConnectionParams($auth_code));

    if (empty($response)) {
      throw new RuntimeException('Empty response');
    }

    if (!empty($response->error)) {
      throw new RuntimeException($response->error);
    }

    $this->setAccessToken($response->access_token);
  }

  /**
   * @param $auth_code
   *  The code sent via GET from the Eventbrite Authorization page
   *  to your redirect URI.
   *
   * @return array
   *  Options to be used for the OAuth connection request.
   */
  private function buildOAuthConnectionParams($auth_code) {
    return array(
      'url' => self::EVENTBRITE_OAUTH_ENDPOINT . '/token',
      'method' => 'POST',
      'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
      'data' => array(
        'code' => $auth_code,
        'client_secret' => $this->getClientSecret(),
        'client_id' => $this->getClientId(),
        'grant_type' => 'authorization_code'
      ),
      'access_token_required' => FALSE,
    );
  }

  /**
   * @param $params
   *  An array of request parameters.
   *  Example:
   *  $params = array(
   *    'url' => self::EVENTBRITE_OAUTH_ENDPOINT . '/token',
   *    'method' => 'POST',
   *    'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
   *    'data' =>  array(
   *      'code' => $auth_code,
   *      'client_secret' => $this->getClientSecret(),
   *      'client_id' => $this->getClientId(),
   *      'grant_type' => 'authorization_code'
   *    ),
   *    'access_token_required' => FALSE,
   *  );
   * @return mixed
   */
  public function request($params) {

    $default = array(
      'url' => '',
      'method' => 'GET',
      'headers' => array(),
      'data' => array(),
      'access_token_required' => TRUE,
    );

    $params = array_merge($default, $params);

    if ($params['access_token_required']) {
      $access_token = $this->getAccessToken();
      $params['headers']['Authorization'] = 'Bearer ' . $access_token;
    }

    $options = array(
      'http' => array(
        'method' => $params['method'],
        'header' => '',
        'content' => '',
      )
    );

    $data = is_array($params['data']) ? http_build_query($params['data']) : $params['data'];

    if ($params['method'] == 'GET') {
      $params['url'] .= !empty($data) ? '?' . $data : '';
    }
    else {
      $options['http']['content'] = $data;
    }

    foreach ($params['headers'] as $type => $value) {
      $options['http']['header'] .= "$type: $value\r\n";
    }

    $context = stream_context_create($options);
    $response = file_get_contents($params['url'], FALSE, $context);

    return json_decode($response);
  }
}