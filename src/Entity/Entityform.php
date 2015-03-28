<?php

/**
 * @file
 * Contains \Drupal\entityform\Entity\Entityform
 */

namespace Drupal\entityform\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Session\AccountInterface;

// @todo Fix Entity type definition
// @todo Fill in gaps of Entity definition vis-a-vis node
/**
 * Defines the Entityform class.
 *
 * @ContentEntityType(
 *   id = "entityform",
 *   label = @Translation("Entityform"),
 *   bundle_label = @Translation("Entityform type"),
 *   handlers = {
 *     "storage" = "Drupal\entityform\Storage\EntityformStorage",
 *     "storage_schema" = "Drupal\entityform\Storage\EntityformStorageSchema",
 *     "view_builder" = "Drupal\entityform\EntityformViewBuilder",
 *     "list_builder" = "Drupal\entityform\EntityformListBuilder",
 *     "access" = "Drupal\entityform\Access\EntityformAccess",
 *     "views_data" = "Drupal\entityform\EntityformViewsData",
 *     "form" = {
 *       "default" = "Drupal\entityform\Form\EntityformForm",
 *       "delete" = "Drupal\entityform\Form\EntityformDeleteForm",
 *       "edit" = "Drupal\entityform\Form\EntityformForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\entityform\Entity\EntityformRouteProvider",
 *     },
 *   },
 *   base_table = "entityform",
 *   data_table = "entityform_field_data",
 *   revision_table = "entityform_revision",
 *   revision_data_table = "entityform_field_revision",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "entityform_id",
 *     "revision" = "entityform_vid",
 *     "bundle" = "type",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid"
 *   },
 *   bundle_entity_type = "entityform_type",
 *   field_ui_base_route = "entity.entityform_type.edit_form",
 *   permission_granularity = "bundle",
 *   links = {
 *     "canonical" = "/eform/{entityform}",
 *     "delete-form" = "/eform/{entityform}/delete",
 *     "edit-form" = "/eform/{entityform}/edit",
 *     "version-history" = "/eform/{entityform}/revisions",
 *   }
 * )
 */
class Entityform extends ContentEntityBase implements EntityformInterface {

  // @todo Move constants to interface?

  /*
   * Indicates the Entityform is a draft.
   */
  const ENTITYFORM_DRAFT = 0;

  /*
   * Indicates the Entityform is complete.
   */
  const ENTITYFORM_COMPLETE = 1;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['entityform_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The Entityform ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The Entityform UUID.'))
      ->setReadOnly(TRUE);

    $fields['entityform_vid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('VID'))
      ->setDescription(t('The Entityform revision ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Type'))
      ->setDescription(t('The Entityform type.'))
      ->setSetting('target_type', 'entityform_type')
      ->setReadOnly(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language'))
      ->setDescription(t('The Entityform language code.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', array(
        'type' => 'hidden',
      ))
      ->setDisplayOptions('form', array(
        'type' => 'language_select',
        'weight' => 2,
      ));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the Entityform was created.'))
      ->setReadOnly(TRUE)
      ->setTranslatable(TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the Entityform was last edited.'))
      ->setReadOnly(TRUE)
      ->setTranslatable(TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Completed by'))
      ->setDescription(t('The user id of the user that completed the form.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      // @todo change default value callback
      //->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE);

    // @todo - use options array and add dependency to module info file
    $fields['status'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Status'))
      ->setDescription(t('Status of Entityform'))
      ->addPropertyConstraints('value', array(
        'AllowedValues' => array('callback' => __CLASS__ . '::getStatii'),
      ));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public static function getStatii() {
    // @todo Add an event here to populate the statii, or perhaps tagging mechanism?
    return array(
      static::ENTITYFORM_DRAFT,
      static::ENTITYFORM_COMPLETE,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public static function countByType($id) {
    // @todo Shouldn't call \Drupal::entityQuery directly; inject dependency here, or move method to another class?  Storage class?
    // @see https://www.drupal.org/node/2133171
    return \Drupal::entityQuery('entityform')
      ->condition('type', $id)
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getTypeLabel() {
    $type = EntityformType::load($this->bundle());
    return $type ? $type->label() : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function access($operation = 'view', AccountInterface $account = NULL, $return_as_object = FALSE) {
    // @todo Complete this function.
    return TRUE;
  }

}
