<?php

use \Drupal\entityform\Entity\EntityformType;
use \Drupal\entityform\Entity\Entityform;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Implements hook_drush_command()
 */
function entityform_drush_command() {
  $items['entityform_bench_add_fields'] = array(
    'description' => dt('Add the specified number of fields to an entityform bundle.'),
    'arguments' => array(
      'number' => dt('Number of fields to add.'),
      'name' => dt('The bundle to which to add fields.  Specify "all" for all bundles, or a comma-delimited list.'),
    ),
    'examples' => array(
      'ebaf 50 test' => dt('Add 50 fields to the "test" entityform bundle.'),
      'ebaf 5 all' => dt('Add 5 fields to every bundle that exists.'),
      'ebaf 100 a,b,c' => dt('Add 100 fields to bundles "a", "b", and "c".'),
    ),
    'aliases' => array('ebaf'),
  );

  $items['entityform_bench_create_type']  = array(
    'description' => dt('Create the specified number of entityform bundles.'),
    'arguments' => array(
      'number' => dt('Number of bundles to create.'),
    ),
    'examples' => array(
      'ebct 50' => dt('Create 50 entityform bundles.'),
    ),
    'aliases' => array('ebct'),
  );

  $items['entityform_bench_delete_types'] = array(
    'description' => dt('Delete all entitform types prefixed with "bench_"'),
    'aliases' => array('ebdt'),
  );

  return $items;
}

/**
 * Drush command callback for 'entityform_bench_add_fields'
 */
function drush_entityform_bench_add_fields($number = 50, $name = NULL) {
  $bundle = EntityformType::load($name);
  if ($bundle) {
    // No easy way to do attach fields via the API right now.
    // @see https://www.drupal.org/node/2346347
    // @see \Drupal\field_ui\Form\FieldStorageAddForm submitForm()
    $entity_manager = \Drupal::service('entity.manager');
    $field_storage_values = [
      'entity_type' => 'entityform',
      'type' => 'integer',
      'translatable' => TRUE,
    ];
    $field_values = [
      'entity_type' => 'entityform',
      'bundle' => $name,
      'translatable' => TRUE,
    ];
    $form_settings = entity_get_form_display('entityform', $name, 'default');
    $view_settings = entity_get_display('entityform', $name, 'default');
    for ($counter = 0; $counter < $number; $counter++) {
      $field_name = 'ebench_' . rand(0, 5000000);
      $field_storage_values['field_name'] = $field_name;
      $field_values['field_name'] = $field_name;
      $field_values['label'] = $field_name;
      $field_values['default_value'] = 52;
      $entity_manager->getStorage('field_storage_config')->create($field_storage_values)->save();
      $entity_manager->getStorage('field_config')->create($field_values)->save();
      $form_settings->setComponent($field_name, array('type' => 'number'));
      $view_settings->setComponent($field_name, array('type' => 'number_integer', 'label' => 'above'));
    }

    // Set configuration for bundle edit form display
    $form_settings->save();
    // Set configuration for bundle display
    $view_settings->save();
    drush_print(dt('Created @num integer fields on @bundle.', array('@num' => $number, '@bundle' => $name)));
  }
  else {
    drush_print(dt('Could not find bundle type @type', array('@type' => $name)));
  }
}

/**
 * Drush command callback for 'entityform_bench_create_type'
 */
function drush_entityform_bench_create_type($number = 50) {
  $count = 0;
  $count_pass = 0;
  $per_pass = 50;
  for ($counter = 0; $counter < $number; $counter++) {
    $bundle_name = 'bench_' . rand(0, 5000000);
    $entity = EntityformType::create(array(
      'id' => $bundle_name,
      'name' => $bundle_name,
      'description' => dt('Bundle created by Entityform drush command "ebct"'),
    ));
    $entity->save();
    $count++;
    $count_pass++;
    if ($count_pass == $per_pass) {
      drush_print(dt('@num bundles created.', array('@num' => $count)));
      $count_pass = 0;
    }
  }

  drush_print(dt('Done.  Created @num entityform bundles.', array('@num' => $count)));
}

/**
 * Drush command callback for 'entityform_delete_types'
 */
function drush_entityform_bench_delete_types() {
  $ids = \Drupal::entityQuery('entityform_type')
    ->execute();

  if ($ids) {
    $total = count($ids);
    drush_print(dt('Found @total of entityform types.', array('@total' => $total)));
  }
  else {
    drush_print(dt('No bundles found.'));
    return;
  }

  $count = 0;
  $count_pass = 0;
  $per_pass = 50;
  foreach ($ids as $id => $name) {
    if (strpos($name, 'bench_') === 0) {
      $to_delete[] = $id;
      $count++;
      $count_pass++;
    }

    if ($count_pass == $per_pass) {
      entity_delete_multiple('entityform_type', $to_delete);
      drush_print(dt('@num bundles have been deleted.', array('@num' => $count)));
      $to_delete = array();
      $count_pass = 0;
    }
  }

  if (!empty($to_delete)) {
    entity_delete_multiple('entityform_type', $to_delete);
  }

  drush_print(dt('Done.  Deleted @count entityform bundles.', array('@count' => $count)));
}
