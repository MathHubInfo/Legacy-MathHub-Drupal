<?php

/*************************************************************************
* This file is part of the MathHub.info System  (http://mathhub.info).   *
* It is hosted at https://github.com/KWARC/MathHub                       *
* Copyright (c) 2015 by the KWARC group (http://kwarc.info)              *
* Licensed under GPL3, see http://www.gnu.org/licenses/gpl.html          *
**************************************************************************/

/**
 * @file
 * mathhub_worker_configurations.features.field_instance.inc
 */

/**
 * Implements hook_field_default_field_instances().
 */
function mathhub_worker_configurations_field_default_field_instances() {
  $field_instances = array();

  // Exported field_instance: 'user-user-field_worker_password'
  $field_instances['user-user-field_worker_password'] = array(
    'bundle' => 'user',
    'default_value' => array(
      0 => array(
        'value' => 'webclient',
      ),
    ),
    'deleted' => 0,
    'description' => '',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'text',
        'settings' => array(),
        'type' => 'text_default',
        'weight' => 2,
      ),
    ),
    'entity_type' => 'user',
    'field_name' => 'field_worker_password',
    'label' => 'worker_password',
    'required' => 0,
    'settings' => array(
      'text_processing' => 0,
      'user_register_form' => 0,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'text',
      'settings' => array(
        'size' => 60,
      ),
      'type' => 'text_textfield',
      'weight' => 9,
    ),
  );

  // Exported field_instance: 'user-user-field_worker_url'
  $field_instances['user-user-field_worker_url'] = array(
    'bundle' => 'user',
    'default_value' => array(
      0 => array(
        'value' => 'http://alpha.mathhub.info:61614',
      ),
    ),
    'deleted' => 0,
    'description' => '',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'text',
        'settings' => array(),
        'type' => 'text_default',
        'weight' => 0,
      ),
    ),
    'entity_type' => 'user',
    'field_name' => 'field_worker_url',
    'label' => 'worker_url',
    'required' => 0,
    'settings' => array(
      'text_processing' => 0,
      'user_register_form' => 0,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'text',
      'settings' => array(
        'size' => 60,
      ),
      'type' => 'text_textfield',
      'weight' => 7,
    ),
  );

  // Exported field_instance: 'user-user-field_worker_user'
  $field_instances['user-user-field_worker_user'] = array(
    'bundle' => 'user',
    'default_value' => array(
      0 => array(
        'value' => 'webclient',
      ),
    ),
    'deleted' => 0,
    'description' => '',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'text',
        'settings' => array(),
        'type' => 'text_default',
        'weight' => 1,
      ),
    ),
    'entity_type' => 'user',
    'field_name' => 'field_worker_user',
    'label' => 'worker_user',
    'required' => 0,
    'settings' => array(
      'text_processing' => 0,
      'user_register_form' => 0,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'text',
      'settings' => array(
        'size' => 60,
      ),
      'type' => 'text_textfield',
      'weight' => 8,
    ),
  );

  // Translatables
  // Included for use with string extractors like potx.
  t('worker_password');
  t('worker_url');
  t('worker_user');

  return $field_instances;
}
