<?php

/**
 * @file
 * Contains \Drupal\entityform\Entity\EntityformTypeInterface.
 */

namespace Drupal\entityform\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Config\Entity\ThirdPartySettingsInterface;

/**
 * Provides an interface for an Entityform Type
 */
interface EntityformTypeInterface extends ConfigEntityInterface, ThirdPartySettingsInterface {

  /**
   * Returns the description of the Entityform type
   *
   * @return string
   *   The description of the Entityform type.
   */
  public function getDescription();

}
