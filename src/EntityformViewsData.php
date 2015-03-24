<?php

/*
 * Contains \Drupal\entityform\EntityformViewsData
 */

namespace Drupal\entityform;

use Drupal\views\EntityViewsData;

/**
 * Provides a views integration class for the Entityform entity type.
 */
class EntityformViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    return $data;
  }
}
