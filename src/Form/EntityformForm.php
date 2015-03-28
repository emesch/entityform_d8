<?php

/*
 * @file
 * Contains \Drupal\entityform\Form\EntityformForm
 */

namespace Drupal\entityform\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * For controller for Entityform submission form
 */
class EntityformForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $entityform = $this->entity;
    $insert = $entityform->isNew();
    $entityform->save();

    if ($entityform->id()) {
      // Tell the submitter what happened, and log the action.
      if ($insert) {
        drupal_set_message($this->t('Submission completed.'));
        $this->logger('entityform')->notice('Entityform submission @id created.', array('@id' => $entityform->id()));
      }
      else {
        drupal_set_message($this->t('Submission updated.'));
        $this->logger('entityform')->notice('Entityform submission @id updated.', array('@id' => $entityform->id()));
      }

      // Redirect the submitter.
      if ($entityform->access('view')) {
        $form_state->setRedirect('entity.entityform.canonical', array('entityform' => $entityform->id()));
      }
      else {
        $form_state->setRedirect('<front>');
      }
    }
    else {
      drupal_set_message($this->t('The form could not be saved.'));
    }

  }

}
