<?php

/**
 * @file
 * Contains \Drupal\entityform\Entity\EntityformInterface
 */

namespace Drupal\entityform\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for an Entityform
 */
interface EntityformInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Returns a list of eligible status codes
   *
   * @return array
   *   A list of integer status codes
   */
  public static function getStatii();

  /**
   * Returns the type label of an entityform.
   *
   * @return string|FALSE
   *   The type label if the type exists, or FALSE.
   */
  public function getTypeLabel();

  /**
   * Returns the number of Entityforms of a given type.
   *
   * @param string $id
   *   The machine name of the Entityform type.
   *
   * @return integer
   *   The number of Entityforms.
   */
  public static function countByType($id);

}
