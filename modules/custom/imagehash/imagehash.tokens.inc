<?php

/**
 * @file
 * Tokens for image hash module.
 */

use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info().
 */
function imagehash_token_info() {
  $info = [];
  $names = imagehash_names();
  foreach (imagehash_algos() as $algo) {
    $info['tokens']['file']["imagehash-$algo"] = [
      'name' => t('@algo hash', ['@algo' => $names[$algo]]),
      'description' => t('The @algo hash of the image.', ['@algo' => $names[$algo]]),
    ];
    $info['tokens']['file']["imagehash-$algo-pair-1"] = [
      'name' => t('@algo hash: Initial pair', ['@algo' => $names[$algo]]),
      'description' => t('The @algo hash of the image: first and second characters.', ['@algo' => $names[$algo]]),
    ];
    $info['tokens']['file']["imagehash-$algo-pair-2"] = [
      'name' => t('@algo hash: Second pair', ['@algo' => $names[$algo]]),
      'description' => t('The @algo hash of the image: third and fourth characters.', ['@algo' => $names[$algo]]),
    ];
  }
  return $info;
}

/**
 * Implements hook_tokens().
 */
function imagehash_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];
  if ($type == 'file' && !empty($data['file'])) {
    foreach (imagehash_algos() as $algo) {
      // Generate the image hash if it is needed but does not yet exist.
      // TODO
      if (empty($data['file']->imagehash[$algo])) {
        foreach ($tokens as $name => $original) {
          if (strpos($name, "imagehash-$algo") === 0) {
            $data['file']->imagehash[$algo] = hash_file($algo, $data['file']->getFileUri());
            break;
          }
        }
      }
      if (isset($tokens["imagehash-$algo"])) {
        $replacements[$tokens["imagehash-$algo"]] = $data['file']->imagehash[$algo];
      }
      if (isset($tokens["imagehash-$algo-pair-1"])) {
        $replacements[$tokens["imagehash-$algo-pair-1"]] = substr($data['file']->imagehash[$algo], 0, 2);
      }
      if (isset($tokens["imagehash-$algo-pair-2"])) {
        $replacements[$tokens["imagehash-$algo-pair-2"]] = substr($data['file']->imagehash[$algo], 2, 2);
      }
    }
  }
  return $replacements;
}
