<?php

namespace Drupal\imagehash\Commands;

use Drupal\imagehash\Batch\GenerateBatch;
use Drush\Commands\DrushCommands;

/**
 * Drush 9 integration for File Hash module.
 */
class ImageHashCommands extends DrushCommands {

  /**
   * Generate hashes for existing files.
   *
   * @aliases igen,imagehash-generate
   * @command imagehash:generate
   * @usage drush imagehash:generate
   *   Generate hashes for existing image files.
   */
  public function generate() {
    batch_set(GenerateBatch::createBatch());
    $batch =& batch_get();
    $batch['progressive'] = FALSE;
    drush_backend_batch_process();
  }

}
