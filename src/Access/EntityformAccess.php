<?php

/**
 * @file
 * Contains \Drupal\entityform\Access\EntityformAccess
 */

namespace Drupal\entityform\Access;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityHandlerInterface;

/**
 * An access control handler for Entityforms.
 */
class EntityformAccess extends EntityAccessControlHandler implements EntityHandlerInterface {

  /**
   * Constructs an EntityformAccess object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   */
  public function __construct(EntityTypeInterface $entity_type) {
    parent::__construct($entity_type);
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type
    );
  }

}
