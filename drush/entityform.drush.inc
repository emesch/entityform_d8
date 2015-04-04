<?php

use \Drupal\Component\Utility\Random;

/**
 * Implements hook_drush_command()
 */
function entityform_drush_command() {
  $items['entityform_bench_add_fields'] = array(
    'description' => dt('Add a number of fields to one or more bundles.'),
    'arguments' => array(
      'entity_type' => dt('The entity type.'),
      'name' => dt('The machine name of bundle(s) to which to add fields.  Specify "all" for all bundles, or a comma-delimited list.'),
      'number' => dt('Number of fields to add.'),
    ),
    'examples' => array(
      'ebaf node test 50' => dt('Add 50 fields to the "test" node bundle.'),
      'ebaf node all 5' => dt('Add 5 fields to every node bundle that exists.'),
      'ebaf node a,b,c 100' => dt('Add 100 fields to node bundles "a", "b", and "c".'),
    ),
    'aliases' => array('ebaf'),
  );

  $items['entityform_bench_create_type']  = array(
    'description' => dt('Create a number of bundles.'),
    'arguments' => array(
      'entity_type' => dt('The entity type.'),
      'number' => dt('Number of bundles to create.'),
    ),
    'examples' => array(
      'ebct node 50' => dt('Create 50 node bundles.'),
    ),
    'aliases' => array('ebct'),
  );

  $items['entityform_bench_delete_types'] = array(
    'description' => dt('Delete bundles."'),
    'arguments' => array(
      'entity_type' => dt('The entity type.'),
      'name' => dt('The bundles to delete.  Specify "all" for all bundles, or a comma-delimited list.'),
    ),
    'examples' => array(
      'ebdt node all' => dt('Delete all node bundles.'),
      'ebdt node a,b,c' => dt('Delete node bundles "a", "b", and "c".'),
    ),
    'aliases' => array('ebdt'),
  );

  return $items;
}

/**
 * Drush command callback for 'entityform_bench_add_fields'
 */
function drush_entityform_bench_add_fields($entity_type, $name, $number = 50) {
  $bundle_config = \Drupal::entityManager()->getDefinition($entity_type)->get('bundle_entity_type');
  $ids = bench_parse_bundle_names($name, $bundle_config);
  foreach ($ids as $id) {
    $start_time = microtime(TRUE);
    $bundle = entity_load($bundle_config, $id);
    if ($bundle) {
      // No easy way to do attach fields via the API right now.
      // @see https://www.drupal.org/node/2346347
      // @see \Drupal\field_ui\Form\FieldStorageAddForm submitForm()
      $entity_manager = \Drupal::service('entity.manager');
      $field_storage_values = [
        'entity_type' => $entity_type,
        'type' => 'integer',
        'translatable' => TRUE,
      ];
      $field_values = [
        'entity_type' => $entity_type,
        'bundle' => $id,
        'translatable' => TRUE,
      ];
      $form_settings = entity_get_form_display($entity_type, $id, 'default');
      $view_settings = entity_get_display($entity_type, $id, 'default');
      for ($counter = 0; $counter < $number; $counter++) {
        $field_name = (new Random())->word(12);
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
      $end_time = microtime(TRUE);
      drush_print(dt('Created @num integer fields on @entity bundle "@bundle" in @time seconds.', array('@num' => $number, '@entity' => $entity_type, '@bundle' => $id, '@time' => ($end_time - $start_time))));
    }
    else {
      drush_print(dt('Could not find bundle type "@type"', array('@type' => $id)));
    }
  }
}

/**
 * Drush command callback for 'entityform_bench_create_type'
 */
function drush_entityform_bench_create_type($entity_type, $number = 50) {
  // Find the bundle config entity id.
  $bundle_config = \Drupal::entityManager()->getDefinition($entity_type)->get('bundle_entity_type');
  // Find the property values of the config entity keys so that we can set them (they can have different values among bundle config entities).
  $entity_keys = \Drupal::entityManager()->getDefinition($bundle_config)->get('entity_keys');
  for ($counter = 0; $counter < $number; $counter++) {
    $bundle_name = (new Random())->word(12);
    // Only set the bare minimum ('id' and 'label') properties for the bundle.
    $config = array(
      $entity_keys['id'] => $bundle_name,
      $entity_keys['label'] => $bundle_name,
    );
    $entity = entity_create($bundle_config, $config)->save();
    drush_print(dt('Created bundle "@bundle".', array('@bundle' => $bundle_name)));
  }
}

/**
 * Drush command callback for 'entityform_delete_types'
 */
function drush_entityform_bench_delete_types($entity_type, $name) {
  $bundle_config = \Drupal::entityManager()->getDefinition($entity_type)->get('bundle_entity_type');
  $ids = bench_parse_bundle_names($name, $bundle_config);
  drush_print(dt('There are @count bundles to try to delete.', array('@count' => count($ids))));
  foreach ($ids as $id) {
    if (entity_load($bundle_config, $id)) {
      entity_delete_multiple($bundle_config, array($id));
      drush_print(dt('Bundle "@bundle" deleted.', array('@bundle' => $id)));
    }
    else {
      drush_print(dt('Bundle "@bundle" does not exist.', array('@bundle' => $id)));
    }
  }
}

/**
 * Returns a list of bundles based on Drush command argument.
 *
 * @param string $name
 *   The "name" Drush command argument value.
 *
 * @param string $bundle_config
 *   The bundle config machine name for the entity type
 *
 * @return array
 *   The bundles.
 */
function bench_parse_bundle_names($name, $bundle_config) {
  if ($name == 'all') {
    $ids = \Drupal::entityQuery($bundle_config)
      ->execute();
  }
  else {
    $ids = explode(',', $name);
  }

  return $ids;
}
