<?php

/**
 * @file
 * Contains \Drupal\entityform\EntityformListBuilder
 */

namespace Drupal\entityform;

use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Component\Utility\Xss;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class handler to create a list of entityform entities.
 */
class EntityformListBuilder extends EntityListBuilder {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * Constructs a new EntityformListBuilder object.
   *
   * Need to override the default constructor in order to inject the date formatter service.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatter $date_formatter) {
    parent::__construct($entity_type, $storage);
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['type']['data'] = $this->t('Entityform Type');
    $header['uid']['data'] = $this->t('Submitter');
    $header['changed'] = $this->t('Updated');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $uri = $entity->urlInfo();
    $row['type']['data'] = array(
      '#type' => 'link',
      '#title' => $entity->getTypeLabel(),
      '#url' => $uri,
    );
    $row['uid']['data'] = array(
      '#theme' => 'username',
      '#account' => $entity->getOwner(),
    );
    $row['changed'] = $this->dateFormatter->format($entity->getChangedTime(), 'short');
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['#empty'] = $this->t('No Entityform submissions have yet been made.');
    return $build;
  }

}
