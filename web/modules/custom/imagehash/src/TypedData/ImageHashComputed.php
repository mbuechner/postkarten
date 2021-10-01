<?php

namespace Drupal\imagehash\TypedData;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Class GeolocationComputed.
 *
 * @package Drupal\geolocation
 */
class ImageHashComputed extends FieldItemList implements FieldItemListInterface {

  use ComputedItemListTrait;

  /**
   * Compute the values.
   */
  protected function computeValue() {
    $some_calculated_values = [1,2,3];
    foreach($some_calculated_values as $delta => $value) {
      $this->list[$delta] = $this->createItem($delta, $value);
    }
  }

}
