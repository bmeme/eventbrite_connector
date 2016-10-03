<?php

namespace EventBriteConnector\Entity;

/**
 * Class Media.
 *
 * @package EventBriteConnector\Entity
 */
class Media extends Entity {

  /**
   * Image event logo type.
   */
  const IMAGE_EVENT_LOGO = 'image-event-logo';

  /**
   * Image event view from seat type.
   */
  const IMAGE_EVENT_VIEW_FROM_SEAT = 'image-event-view-from-seat';

  /**
   * Image organizer logo type.
   */
  const IMAGE_ORGANIZER_LOGO = 'image-organizer-logo';

  /**
   * Image user photo type.
   */
  const IMAGE_USER_PHOTO = 'image-user-photo';

  /**
   * {@inheritdoc}
   */
  public function getEntityApiType() {
    return 'media';
  }

  /**
   * Upload a file.
   *
   * @param string $filename
   *   The path to the file to be uploaded.
   * @param string $type
   *   One of the Media constants used to define the file type.
   * @param array $crop_mask
   *   (optional) An associative array containing crop mask settings:
   *   - crop_mask.top_left.x
   *   - crop_mask.top_left.y
   *   - crop_mask.width
   *   - crop_mask.height.
   *
   * @return array
   *   The file upload response.
   */
  public function upload($filename, $type, array $crop_mask = array()) {
    $data = $this->getUploadInstructions($type);
    return $this->uploadFile($data, $filename)
      ->notifyApi($data['upload_token'], $crop_mask);
  }

  /**
   * Step 1: Retrieve the upload instructions.
   *
   * @param string $type
   *   One of the Media constants used to define the file type.
   *
   * @return array
   *   An associative array containing upload instructions.
   */
  protected function getUploadInstructions($type) {
    $params = [
      'url' => $this->getEntityEndpoint() . 'upload',
      'data' => [
        'type' => $type,
      ],
    ];
    $data = $this->getConnector()->request($params);

    if (empty($data['upload_data'])) {
      throw new \RuntimeException('Cannot proceed uploading the specified file');
    }

    return $data;
  }

  /**
   * Step 2: Upload the file to the specified URL.
   *
   * @param array $data
   *   An associative array containing upload instructions.
   * @param string $filename
   *   The path to the file to be uploaded.
   *
   * @return $this
   */
  protected function uploadFile($data, $filename) {
    if (!file_exists($filename)) {
      $message = sprintf('Cannot find file %s', $filename);
      throw new \InvalidArgumentException($message);
    }

    $file = file_get_contents($filename);

    $post_args = $data['upload_data'];
    $post_args[$data['file_parameter_name']] = $file;
    $post_args['upload_token'] = $data['upload_token'];

    $multipart = array();
    foreach ($post_args as $key => $value) {
      $multipart[] = array(
        'name' => $key,
        'contents' => $value,
      );
    }

    $client = $this->getConnector()->getHttpClient();
    $client->request($data['upload_method'], $data['upload_url'], [
      'multipart' => $multipart,
    ]);

    return $this;
  }

  /**
   * Step 3: Notify the API that the upload has completed.
   *
   * @param string $upload_token
   *   The upload access token.
   * @param array $crop_mask
   *   (optional) An associative array containing crop mask settings.
   *
   * @return array
   *   An array representing the image you have uploaded.
   */
  protected function notifyApi($upload_token, array $crop_mask = array()) {
    $post_args = array(
      'upload_token' => $upload_token,
    );

    if (!empty($crop_mask)) {
      $post_args = array_merge($post_args, $this->getCropMaskParams($crop_mask));
    }

    $params = [
      'url' => $this->getEntityEndpoint() . 'upload/',
      'data' => $post_args,
      'method' => 'POST',
    ];

    return $this->getConnector()->request($params);
  }

  /**
   * Get crop mask parameters.
   *
   * @param array $crop_mask
   *   An associative array containing crop mask settings.
   *
   * @return array
   *   A validated array containing crop mask settings.
   */
  protected function getCropMaskParams($crop_mask) {
    $mask_keys = array(
      'crop_mask.top_left.x' => NULL,
      'crop_mask.top_left.y' => NULL,
      'crop_mask.width' => NULL,
      'crop_mask.height' => NULL,
    );
    $crop_mask = array_intersect_key($crop_mask, $mask_keys);

    if (count($crop_mask) != 4) {
      $message = 'You must specify all crop mask params.';
      throw new \InvalidArgumentException($message);
    }

    return $crop_mask;
  }

}
