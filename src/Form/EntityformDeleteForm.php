<?php

/**
 * @file
 * Contains \Drupal\entityform\Form\EntityformDeleteForm
 */

namespace Drupal\entityform\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;

/**
 * Provides a form for deleting an Entityform.
 */
class EntityformDeleteForm extends ContentEntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $this->entity->delete();
    $this->logger('entityform')->notice('Entityform submission @id deleted.', array('@id' => $this->entity->id()));
    drupal_set_message($this->t('Submission deleted.'));
    $form_state->setRedirect('<front>');
  }
}
