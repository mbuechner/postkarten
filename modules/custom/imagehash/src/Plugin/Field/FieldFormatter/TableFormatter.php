<?php

namespace Drupal\imagehash\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Plugin\Field\FieldFormatter\DescriptionAwareFileFormatterBase;

/**
 * Plugin implementation of the 'imagehash_table' formatter.
 *
 * @FieldFormatter(
 *   id = "imagehash_table",
 *   label = @Translation("Table of files with hashes"),
 *   field_types = {
 *     "file",
 *     "image",
 *   }
 * )
 */
class TableFormatter extends DescriptionAwareFileFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    if ($files = $this->getEntitiesToView($items, $langcode)) {
      $names = imagehash_names();
      $header = [
        $this->t('Attachment'),
        $this->t('Size'),
        $this->t('@algo hash', ['@algo' => $names[$this->getSetting('algo')]]),
      ];
      $rows = [];
      foreach ($files as $file) {
        if (property_exists($file, '_referringItem')) {
          $item = $file->_referringItem;
          $rows[] = [
            [
              'data' => [
                '#theme' => 'file_link',
                '#file' => $file,
                '#description' => $this->getSetting('use_description_as_link_text') ? $item->description : NULL,
                '#cache' => ['tags' => $file->getCacheTags()],
              ],
            ],
            ['data' => format_size(method_exists($file, 'getSize') ? $file->getSize() : 0)],
            [
              'data' => [
                '#markup' => substr(chunk_split(property_exists($file, 'filehash') ? $file->imagehash[$this->getSetting('algo')] : '', 1, '<wbr />'), 0, -7),
              ],
            ],
          ];
        }
      }

      $elements[0] = [];
      if (!empty($rows)) {
        $elements[0] = [
          '#theme' => 'table__imagehash_formatter_table',
          '#header' => $header,
          '#rows' => $rows,
        ];
      }
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = parent::defaultSettings();
    $algos = imagehash_algos();
    $settings['algo'] = array_pop($algos);
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $names = imagehash_names();
    $options = [];
    foreach (imagehash_algos() as $algo) {
      $options[$algo] = $names[$algo];
    }
    $form['algo'] = [
      '#title' => $this->t('Hash algorithm'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('algo'),
      '#options' => $options,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $algos = imagehash_names();
    if (isset($algos[$this->getSetting('algo')])) {
      $summary[] = $this->t('@algo hash', ['@algo' => $algos[$this->getSetting('algo')]]);
    }
    return $summary;
  }

}
