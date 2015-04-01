<?php

/**
 * @file
 * Contains \Drupal\entityform\Form\EntityformTypeDeleteForm
 */

namespace Drupal\entityform\Form;

use Drupal\entityform\Entity\Entityform;
use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form handler to delete Entityform Types
 */
class EntityformTypeDeleteForm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Prevent deletion of type if submissions of the type exist.
    $count = Entityform::countByType($this->entity->id());
    if ($count) {
      $form['#title'] = $this->getQuestion();
      $form['stop'] = array('#markup' => $this->formatPlural($count, '%type cannot be deleted because one submission of that type exists.', '%type cannot be deleted because @count submissions of that type exist.', array('%type' => $this->entity->label(), '@count' => $count)));
      return $form;
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->delete();
    drupal_set_message($this->t('Entityform type %type deleted.', array('%type' => $this->entity->bundle())));
    $this->logger('entityform')->notice('Entityform type @type deleted.', array('@type' => $this->entity->bundle()));
  }

}
