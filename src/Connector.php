<?php

namespace EventBriteConnector;
use EventBriteConnector\Entity\Entity;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;

/**
 * Class Connector.
 *
 * @package EventBriteConnector
 */
class Connector {

  /**
   * The Api endpoint for access token generation.
   */
  const API_ENDPOINT = 'https://www.eventbriteapi.com/v3';

  /**
   * The OAuth endpoint for access token generation.
   */
  const OAUTH_ENDPOINT = 'https://www.eventbrite.com/oauth';

  /**
   * Api endpoint.
   *
   * @var string $endpoint
   */
  protected $endpoint;

  /**
   * Your App client secret.
   *
   * @var string $clientSecret
   */
  protected $clientSecret;

  /**
   * Your App client id.
   *
   * @var string $clientId
   */
  protected $clientId;

  /**
   * Your App client access token.
   *
   * @var string $accessToken
   */
  protected $accessToken;

  /**
   * Eventbrite loaded entities.
   *
   * @var array $entities
   */
  protected $entities;

  /**
   * @var Client $httpClient;
   */
  protected $httpClient;

  /**
   * EventBriteConnector constructor.
   *
   * @param string $client_secret
   *   Your App client secret.
   * @param string $client_id
   *   Your App client secret.
   * @param string $access_token
   *   Your App access token.
   */
  public function __construct($client_secret, $client_id, $access_token = NULL) {
    $this->setEndpoint(self::API_ENDPOINT);
    $this->setClientSecret($client_secret);
    $this->setClientId($client_id);
    $this->setAccessToken($access_token);
    $this->httpClient = new Client();
  }

  /**
   * Set endpoint.
   *
   * @param string $endpoint
   *   Api endpoint.
   */
  public function setEndpoint($endpoint) {
    $this->endpoint = $endpoint;
  }

  /**
   * Get endpoint.
   *
   * @return string
   *   Api endpoint.
   */
  public function getEndpoint() {
    return $this->endpoint;
  }

  /**
   * Set client secret.
   *
   * @param $client_secret
   *   Your App client secret.
   *
   * @return Connector
   *   The Eventbrite connector instance.
   */
  public function setClientSecret($client_secret) {
    $this->clientSecret = $client_secret;

    return $this;
  }

  /**
   * Get client secret.
   *
   * @return string
   *   Your App client secret.
   */
  public function getClientSecret() {
    return $this->clientSecret;
  }

  /**
   * Set client id.
   *
   * @param $client_id
   *   Your App client id.
   *
   * @return $this
   *   The Eventbrite connector instance.
   */
  public function setClientId($client_id) {
    $this->clientId = $client_id;

    return $this;
  }

  /**
   * Get client id.
   *
   * @return string
   *   Your App client id.
   */
  public function getClientId() {
    return $this->clientId;
  }

  /**
   * Set access token.
   *
   * @param string|NULL $access_token
   *   Your App access token.
   *
   * @return $this
   *   The Eventbrite connector instance.
   */
  public function setAccessToken($access_token = NULL) {
    if (empty($access_token) && !empty($_SESSION['eb_access_token'])) {
      $access_token = $_SESSION['eb_access_token'];
    }

    $this->accessToken = $access_token;
    $_SESSION['eb_access_token'] = $access_token;

    return $this;
  }

  /**
   * Get access token.
   *
   * @return string
   *   Your App access token.
   *
   * @throws \RuntimeException
   */
  public function getAccessToken() {
    if (!empty($_SESSION['eb_access_token'])) {
      $this->setAccessToken($_SESSION['eb_access_token']);
    }

    if (empty($this->accessToken)) {
      $_SESSION['eb_last_request'] = array(
        'server' => $_SERVER,
        'get' => $_GET,
        'post' => $_POST,
      );

      throw new \RuntimeException('Missing Access Token');
    }

    return $this->accessToken;
  }

  /**
   * Delete access token.
   *
   * @return $this
   *   The Eventbrite connector instance.
   */
  public function deleteAccessToken() {
    unset($this->accessToken);
    unset($_SESSION['eb_access_token']);
    return $this;
  }

  /**
   * Get entities.
   *
   * @param string $entity_api_type
   *   A string representing the type of the entity to be extracted.
   *
   * @return array
   *   An array of entities.
   */
  public function getEntities($entity_api_type = '') {
    return (!empty($entity_api_type)) ? $this->entities[$entity_api_type] : $this->entities;
  }

  /**
   * Get entity.
   *
   * @param string $entity_api_type
   *   The entity type name.
   * @param string $entity_id
   *   The entity id.
   *
   * @return Entity
   *   An Eventbrite entity instance.
   *
   * @throws \InvalidArgumentException
   */
  public function getEntity($entity_api_type, $entity_id) {
    if (!isset($this->entities[$entity_api_type][$entity_id])) {
      $message = sprintf('Undefined entity %s with id %s', $entity_api_type, $entity_id);
      throw new \InvalidArgumentException($message);
    }
    return $this->entities[$entity_api_type][$entity_id];
  }

  /**
   * Set entities.
   *
   * @param array $entities
   *   An array of entities.
   */
  protected function setEntities(array $entities) {
    $this->entities = $entities;
  }

  /**
   * Add entity.
   *
   * @param string $entity_api_type
   *   The entity type name.
   * @param string|NULL $entity_id
   *   The entity id.
   *
   * @return \EventBriteConnector\Entity\Entity
   *   The added Eventbrite entity instance.
   */
  public function addEntity($entity_api_type, $entity_id = NULL) {
    $entity = EntityFactory::get($entity_api_type, $entity_id);
    $entities = $this->getEntities();

    if (empty($entities[$entity->getEntityApiType()][$entity->getEntityId()])) {
      $entity->setConnector($this);
      $entities[$entity->getEntityApiType()][$entity->getEntityId()] = $entity;

      $this->setEntities($entities);
    }

    return $entity;
  }

  /**
   * Remove entity.
   *
   * @param \EventBriteConnector\Entity\Entity $entity
   *   An Eventbrite entity instance.
   *
   * @return $this
   *   The Eventbrite connector instance.
   */
  public function removeEntity(Entity $entity) {
    $entities = $this->getEntities();

    if (!empty($entities[$entity->getEntityApiType()][$entity->getEntityId()])) {
      unset($entities[$entity->getEntityApiType()][$entity->getEntityId()]);
    }

    $this->setEntities($entities);

    return $this;
  }

  /**
   * Generate an URL for redirection to Eventbrite Authorization page.
   *
   * @return string
   *   The redirect URL.
   */
  public function getAuthorizationURL() {
    $params = array(
      'response_type' => 'code',
      'client_id' => $this->getClientId()
    );

    return self::OAUTH_ENDPOINT . '/authorize?' . http_build_query($params);
  }

  /**
   * Invokes the Eventbrite API to get and set a valid Access Token.
   *
   * @param $auth_code
   *   The auth code.
   *
   * @throws \RuntimeException
   */
  public function getOAuthToken($auth_code) {
    $response = $this->request($this->buildOauthConnectionParams($auth_code));

    if (empty($response)) {
      throw new \RuntimeException('Empty response');
    }

    if (!empty($response->error)) {
      throw new \RuntimeException($response->error);
    }

    $this->setAccessToken($response->access_token);
  }

  /**
   * Build OAuth connection params.
   *
   * @param string $auth_code
   *   The code sent via GET from the Eventbrite Authorization page to your
   *   redirect URI.
   *
   * @return array
   *   Options to be used for the OAuth connection request.
   */
  protected function buildOauthConnectionParams($auth_code) {
    return array(
      'url' => self::OAUTH_ENDPOINT . '/token',
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
   * Request.
   *
   * @param array $params
   *  An array of request parameters.
   *
   * @code
   *  $params = array(
   *    'url' => self::OAUTH_ENDPOINT . '/token',
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
   * @endcode
   *
   * @return mixed
   *   The request response.
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

    $data = is_array($params['data']) ? http_build_query($params['data']) : $params['data'];
    if ($params['method'] == 'GET') {
      $params['url'] .= !empty($data) ? '?' . $data : '';
      $data = '';
    }
    elseif (empty($params['headers']['Content-type'])) {
      $params['headers']['Content-type'] = 'application/x-www-form-urlencoded';
    }

    $request = new Request($params['method'], $params['url'], $params['headers'], $data);
    $response = $this->httpClient->send($request);

    return json_decode($response->getBody()->getContents());
  }

}
