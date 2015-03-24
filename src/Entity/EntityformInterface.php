<?php

/**
 * @file
 * Contains \Drupal\entityform\Entity\EntityformInterface
 */

namespace Drupal\entityform\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for an Entityform
 */
interface EntityformInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Returns a list of eligible status codes
   *
   * @return array
   *   A list of integer status codes
   */
  public static function getStatii();

}
