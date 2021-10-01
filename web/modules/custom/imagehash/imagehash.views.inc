<?php

/**
 * @file
 * Provide views data and handlers for imagehash table.
 */

/**
 * Implements hook_views_data().
 */
function imagehash_views_data() {
  $data['imagehash']['table']['group'] = t('Image');
  $data['imagehash']['table']['join'] = [
    'file_managed' => [
      'left_field' => 'fid',
      'field' => 'fid',
    ],
  ];
  // Use a nice human-readable name for each hash algorithm.
  $names = imagehash_names();
  foreach (imagehash_algos() as $algo) {
    $data['imagehash'][$algo] = [
      'title' => t('@algo', ['@algo' => $names[$algo]]),
      'help' => t('The @algo of the image.', ['@algo' => $names[$algo]]),
      'field' => [
        'id' => 'standard',
        'click sortable' => TRUE,
      ],
      'argument' => [
        'id' => 'string',
      ],
      'filter' => [
        'id' => 'string',
      ],
      'sort' => [
        'id' => 'standard',
      ],
    ];

    $data['imagehash']['my_computed_field'] = [
      'title' => t('Computed field'),
      'help' => t('Help for the computed field'),
      'field' => [
        'id' => 'standard',
        'click sortable' => TRUE,
      ],
      'argument' => [
        'id' => 'string',
      ],
      'filter' => [
        'id' => 'string',
      ],
      'sort' => [
        'id' => 'standard',
      ],
    ];

  }
  return $data;
}
