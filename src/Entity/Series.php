<?php

namespace EventBriteConnector\Entity;

use EventBriteConnector\Entity\Crud\EntityCreateTrait;
use EventBriteConnector\Entity\Crud\EntityDeleteTrait;
use EventBriteConnector\Entity\Crud\EntityUpdateTrait;

/**
 * Class Series.
 *
 * @package EventBriteConnector\Entity
 */
class Series extends Entity {

  use EntityCreateTrait;
  use EntityUpdateTrait;
  use EntityDeleteTrait;

  /**
   * {@inheritdoc}
   */
  public function getEntityApiType() {
    return 'series';
  }

}
