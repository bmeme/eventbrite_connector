<?php

namespace EventBriteConnector\Entity\Crud;

use EventBriteConnector\Entity\Entity;

/**
 * Class EntityUpdateTrait.
 *
 * @package EventBriteConnector\Entity
 */
trait EntityUpdateTrait {

  /**
   * Update entity.
   *
   * @param array $values
   *   An associative array of data to be saved. Keys must not be normalized but
   *   like $values['name']['html'] = "My event name", without the entity type
   *   name prefix.
   *
   * @return Entity
   *   The entity instance.
   *
   * @throws \RuntimeException
   */
  public function update(array $values = array()) {
    /** @var Entity $this */
    if ($this->isNew()) {
      throw new \RuntimeException('Cannot update values for new entities');
    }
    $this->setData($this->activeDataSet, $values);
    $this->save();

    $args = $this->parseDataKey($this->activeDataSet);
    $args['reset'] = TRUE;
    call_user_func_array([$this, 'load'], array_values($args));

    return $this;
  }

}
