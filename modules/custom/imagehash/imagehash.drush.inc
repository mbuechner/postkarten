<?php

/**
 * @file
 * Drush integration for Image file Hash module.
 */

use Drupal\imagehash\Batch\GenerateBatch;

/**
 * Implements hook_drush_command().
 */
function imagehash_drush_command() {
  $commands['imagehash-generate'] = [
    'description' => 'Generate hashes for existing image files.',
    'examples' => [
      'drush imagehash-generate',
    ],
    'aliases' => ['igen'],
  ];

  return $commands;
}

/**
 * Drush callback for imagehash-generate command.
 */
function drush_imagehash_generate() {
  batch_set(GenerateBatch::createBatch());
  $batch =& batch_get();
  $batch['progressive'] = FALSE;
  drush_backend_batch_process();
}
