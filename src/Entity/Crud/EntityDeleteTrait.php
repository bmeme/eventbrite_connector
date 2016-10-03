<?php

namespace EventBriteConnector\Entity\Crud;

use EventBriteConnector\Entity\Entity;

/**
 * Class EntityDeleteTrait.
 *
 * @package EventBriteConnector\Entity
 */
trait EntityDeleteTrait {

  /**
   * Delete entity.
   *
   * @return mixed
   *   The response data.
   *
   * @throws \RuntimeException
   */
  public function delete() {
    /** @var Entity $this */
    if ($this->isNew()) {
      throw new \RuntimeException('Cannot delete a new entity');
    }

    $params = array(
      'method' => 'DELETE',
      'url' => $this->getEntityEndpoint(),
    );

    return $this->getConnector()->request($params);
  }

}
