<?php

/**
 * @file
 * Contains \Drupal\entityform\Form\EntityformTypeForm
 */

// @todo Revisit string management

namespace Drupal\entityform\Form;

use Drupal\entityform\Entity\EntityformType;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\String;

/**
 * Form handler for Entityform Type forms
 */
class EntityformTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    // Set the title depending on the operation.
    $type = $this->entity;
    if ($this->operation == 'add') {
      $form['#title'] = $this->t('Add Entityform Type');
    }
    else {
      $form['#title'] = $this->t('Edit Entityform Type %type', array('%type' => $type->label()));
    }

    $form['name'] = array(
      '#title' => t('Name'),
      '#type' => 'textfield',
      '#default_value' => $type->label(),
      '#description' => t('The name of this Entityform type.'),
      '#required' => TRUE,
      '#size' => 30,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#disabled' => !$type->isNew(),
      '#machine_name' => array(
        'exists' => 'Drupal\entityform\Entity\EntityformType::load',
        'source' => array('name'),
      ),
      '#description' => t('A unique machine-readable name for this Entityform type. It must only contain lowercase letters, numbers, and underscores.'),
    );

    $form['description'] = array(
      '#title' => t('Description'),
      '#type' => 'textarea',
      '#default_value' => $type->getDescription(),
      '#description' => t('Enter a brief description of the Entityform type.'),
    );

    $form['max_submissions_per_user'] = array(
      '#title' => t('Max. submissions per user.'),
      '#description' => t('The maximum number of submissions permitted per user ("0" for unlimited).'),
      '#type' => 'textfield',
      '#default_value' => $type->getMaxSubmissionsPerUser(),
      '#maxlength' => 4,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, FormStateInterface $form_state) {
    parent::validate($form, $form_state);

    // Make sure the machine name is not empty
    // @see NodeTypeForm->validate()
    // @todo Shouldn't this be done automatically by form api?
    $id = trim($form_state->getValue('id'));
    if ($id == '0') {
      $form_state->setErrorByName('id', $this->t("Invalid machine-readable name. Enter a name other than %invalid.", array('%invalid' => $id)));
    }

    // Ensure the max submissions per user is a number.
    $max_submissions_per_user = trim($form_state->getValue('max_submissions_per_user'));
    if (!ctype_digit($max_submissions_per_user)) {
      $form_state->setErrorByName('max_submissions_per_user', $this->t("Maximum submissions per user must be 0 or a positive integer."));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $type = $this->entity;
    // @see NodeTypeForm->save()
    // @todo Again, shouldn't this be done automatically by form api?
    $type->set('id', trim($type->id()));
    $type->set('name', trim($type->label()));

    $status = $type->save();
    if ($status == SAVED_UPDATED) {
      drupal_set_message($this->t('Entityform type %type was updated.', array('%type' => $type->label())));
      $this->logger('entityform')->notice('Entityform type @type was updated.', array('@type' => $type->label()));
    }
    elseif ($status == SAVED_NEW) {
      drupal_set_message($this->t('Entityform type %type was created.', array('%type' => $type->label())));
      $this->logger('entityform')->notice('Entityform type @type was created.', array('@type' => $type->label()));
    }

    $form_state->setRedirectUrl($type->urlInfo('collection'));
  }

}
