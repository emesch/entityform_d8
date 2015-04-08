<?php

/**
 * @file
 * Contains \Drupal\entityform\Entity\EntityformType
 */

namespace Drupal\entityform\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines an Entityform type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "entityform_type",
 *   label = @Translation("Entityform type"),
 *   handlers = {
 *     "list_builder" = "Drupal\entityform\EntityformTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\entityform\Form\EntityformTypeForm",
 *       "edit" = "Drupal\entityform\Form\EntityformTypeForm",
 *       "delete" = "Drupal\entityform\Form\EntityformTypeDeleteForm"
 *     },
 *   },
 *   admin_permission = "administer entityform types",
 *   config_prefix = "type",
 *   bundle_of = "entityform",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name"
 *   },
 *   links = {
 *     "edit-form" = "/admin/structure/eform/{entityform_type}",
 *     "delete-form" = "/admin/structure/eform/{entityform_type}/delete",
 *     "collection" = "/admin/structure/eform/types",
 *   }
 * )
 */
class EntityformType extends ConfigEntityBundleBase implements EntityformTypeInterface {

  /**
   * The machine name of the Entityform type.
   *
   * @var string
   */
  protected $id;

  /**
   * The name of the Entityform type.
   *
   * @var string
   */
  protected $name;

  /**
   * A description of the Entityform type.
   *
   * @var string
   */
  protected $description;

  /**
   * The weight of the Entityform type relative to other types.
   */
  //protected $weight = 0;

  /**
   * Whether a new revision should be created by default.
   *
   * @var bool
   */
  // protected $new_revision = FALSE;


  /**
   * The maximum number of times a single user can submit the form ("0" for unlimited)
   *
   * @var integer
   */
  protected $max_submissions_per_user = 0;

  /**
   * Whether to allow submissions by anonymous users
   *
   * @var bool
   */
  // protected $anonymous_submissions = TRUE;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function getMaxSubmissionsPerUser() {
    return $this->max_submissions_per_user;
  }

}
