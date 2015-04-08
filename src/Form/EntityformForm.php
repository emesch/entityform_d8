<?php

/*
 * @file
 * Contains \Drupal\entityform\Form\EntityformForm
 */

namespace Drupal\entityform\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityManagerInterface;

/**
 * For controller for Entityform submission form
 */
class EntityformForm extends ContentEntityForm {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $current_user;

  /**
   * Constructs an EntityformForm object
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The current user.
   */
  public function __construct(EntityManagerInterface $entity_manager, AccountInterface $user) {
    $this->entityManager = $entity_manager;
    $this->current_user = $user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('current_user')
    );
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    // If the maximum number of form submissions is exceeded, do not build the form.
    $uid = $this->current_user->id();
    $bundle = $this->entityManager->getStorage('entityform_type')->load($this->entity->bundle());
    $max = $bundle->getMaxSubmissionsPerUser();
    if ($this->operation != 'edit' && $uid && $max != 0) {
      $submissions = $this->entity->countByTypeUser($this->entity->bundle(), $uid);
      if ($submissions >= $max) {
        $form['do_not_proceed'] = array(
          '#type' => 'markup',
          '#markup' => $this->t('You have submitted this form a maximum number of times.'),
        );
        return $form;
      }
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, FormStateInterface $form_state) {
    parent::validate($form, $form_state);

    // Ensure the maximum number of form submissions per user is not exceeded.
    $uid = $this->current_user->id();
    $bundle = $this->entityManager->getStorage('entityform_type')->load($this->entity->bundle());
    $max = $bundle->getMaxSubmissionsPerUser();
    // Only enforce if this is a new form submission, the user is authenticated, and submissions are not unlimited.
    if ($this->operation != 'edit' && $uid && $max != 0) {
      $submissions = $this->entity->countByTypeUser($this->entity->bundle(), $uid);
      if ($submissions >= $max) {
        $form_state->setErrorByName('', $this->formatPlural($max, $this->t('You may not submit this form more than one time.'), $this->t('You may not submit this form more than %max times.', array('%max' => $max))));
      }
    }
  }

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
