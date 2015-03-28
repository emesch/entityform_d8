<?php

/**
 * @file
 * Contains \Drupal\entityform\Form\EntityformTypeDeleteForm
 */

namespace Drupal\entityform\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form handler to delete Entityform Types
 */
class EntityformTypeDeleteForm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->delete();
    drupal_set_message($this->t('Entityform type %type deleted.', array('%type' => $this->entity->bundle())));
    $this->logger('entityform')->notice('Entityform type @type deleted.', array('@type' => $this->entity->bundle()));
  }
  
}
