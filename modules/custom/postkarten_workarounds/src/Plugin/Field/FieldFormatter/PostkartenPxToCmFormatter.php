<?php

/**
 * @file
 * Contains \Drupal\Core\Field\Plugin\Field\FieldFormatter\ComputedPhpFormatterExample.
 */

namespace Drupal\postkarten_workarounds\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\NumericFormatterBase;

/**
 * Plugin implementation of the Postkarten px -> cm formatter for Postkarten project.
 *
 * @FieldFormatter(
 *   id = "postkarten_field_formatter_px_cm",
 *   label = @Translation("Pixel to Centimeter"),
 *   field_types = {
 *     "integer",
 *   }
 * )
 */
class PostkartenPxToCmFormatter extends NumericFormatterBase
{

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array
  {
    return [
        'thousand_separator' => '.',
        'decimal_seperator' => ',',
        'prefix_suffix' => TRUE,
      ] + parent::defaultSettings();
  }

  public function viewElements(FieldItemListInterface $items, $langcode): array
  {
    $elements = [];
    $settings = $this->getFieldSettings();

    foreach ($items as $delta => $item) {
      $output = $this->numberFormat($item->value);

      // Account for prefix and suffix.
      if ($this->getSetting('prefix_suffix')) {
        $prefixes = isset($settings['prefix']) ? array_map(['Drupal\Core\Field\FieldFilteredMarkup', 'create'], explode('|', $settings['prefix'])) : [''];
        $suffixes = isset($settings['suffix']) ? array_map(['Drupal\Core\Field\FieldFilteredMarkup', 'create'], explode('|', $settings['suffix'])) : [''];
        $prefix = (count($prefixes) > 1) ? $this->formatPlural($item->value, $prefixes[0], $prefixes[1]) : $prefixes[0];
        $suffix = (count($suffixes) > 1) ? $this->formatPlural($item->value, $suffixes[0], $suffixes[1]) : $suffixes[0];
        $value_cm = round(($item->value / 600.0) * 2.54, 1);
        $size_cm = number_format($value_cm, 1, $this->getSetting('decimal_seperator'), $this->getSetting('thousand_separator'));
        $size_px = number_format($item->value, 0, '', $this->getSetting('thousand_separator'));
        $output = $size_cm . ' cm (' . $prefix . $size_px . $suffix . ')';
      }
      // Output the raw value in a content attribute if the text of the HTML
      // element differs from the raw value (for example when a prefix is used).
      if (isset($item->_attributes) && $item->value != $output) {
        $item->_attributes += ['content' => $item->value];
      }

      $elements[$delta] = ['#markup' => $output];
    }

    return $elements;
  }

  protected function numberFormat($number): string
  {
    return number_format($number, 0, '', $this->getSetting('thousand_separator'));
  }
}
