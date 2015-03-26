<?php

/**
 * @file
 * Contains \Drupal\entityform\Controller\EntityformController
 */

namespace Drupal\entityform\Controller;

use Drupal\entityform\Entity\EntityformTypeInterface;
use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Entityform routes
 */
class EntityformController extends ControllerBase {

  /**
   * Provides the entityform submission form.
   *
   * @param \Drupal\entityform\EntityformTypeInterface $entityform_type
   *   The entityform type to use for the entityform.
   *
   * @return array
   *   An entityform submission form.
   */
  public function add(EntityformTypeInterface $entityform_type) {
    $entityform = $this->entityManager()->getStorage('entityform')->create(array(
      'type' => $entityform_type->id(),
    ));

    $form = $this->entityFormBuilder()->getForm($entityform);
    return $form;
  }

}
