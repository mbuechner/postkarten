<?php

namespace Drupal\Tests\imagehash\Functional;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\Entity\File;
use Drupal\Tests\file\Functional\FileFieldTestBase;

/**
 * Image file Hash tests.
 *
 * @group File Hash
 */
class ImageHashTest extends FileFieldTestBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  protected mixed $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'imagehash',
    'node',
    'file',
    'file_module_test',
    'field_ui',
  ];

  /**
   * Overrides WebTestBase::setUp().
   */
  protected function setUp() {
    parent::setUp();
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('admin/config/media/filehash');
    $fields = ['algos[sha1]' => TRUE];
    $this->submitForm($fields, $this->t('Save configuration'));
  }

  /**
   * Tests that a file hash is set on the file object.
   */
  public function testImageHash() {
    $file = File::create([
      'uid' => 1,
      'filename' => 'druplicon.txt',
      'uri' => 'public://druplicon.txt',
      'filemime' => 'text/plain',
      'created' => 1,
      'changed' => 1,
      'status' => FILE_STATUS_PERMANENT,
    ]);
    file_put_contents($file->getFileUri(), 'hello world');
    $file->save();
    $this->assertEquals($file->imagehash['sha1'], '2aae6c35c94fcfb415dbe95f408b9ce91ee846ed', 'Image file hash was set correctly.');
  }

  /**
   * Tests the table with file hashes field formatter.
   */
  public function testImageHashField() {
    $field_name = strtolower($this->randomMachineName());
    $type_name = 'article';
    $field_storage_settings = [
      'display_field' => '1',
      'display_default' => '1',
      'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
    ];
    $field_settings = ['description_field' => '1'];
    $widget_settings = [];
    $this->createFileField($field_name, 'node', $type_name, $field_storage_settings, $field_settings, $widget_settings);
    $this->drupalGet("admin/structure/types/manage/$type_name/display");
    $fields = ["fields[$field_name][type]" => 'imagehash_table'];
    $this->submitForm($fields, $this->t('Save'));
  }

  /**
   * Tests file hash bulk generation.
   */
  public function testImageHashGenerate() {
    $this->drupalGet('admin/config/media/filehash');
    $fields = ['algos[sha1]' => FALSE];
    $this->submitForm($fields, $this->t('Save configuration'));

    do {
      $file = $this->getTestFile('text');
      $file->save();
    } while ($file->id() < 5);

    $this->drupalGet('admin/config/media/imagehash');
    $fields = ['algos[sha1]' => TRUE];
    $this->submitForm($fields, $this->t('Save configuration'));

    $this->drupalGet('admin/config/media/imagehash/generate');
    $this->submitForm([], $this->t('Generate'));
    $this->assertSession()->pageTextContains('Processed 5 files.');
  }

}
