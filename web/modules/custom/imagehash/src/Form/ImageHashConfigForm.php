<?php

namespace Drupal\imagehash\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements the image file hash config form.
 */
class ImageHashConfigForm extends ConfigFormBase {

  /**
   * Stores a module manager.
   *
   * @var ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'imagehash_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['imagehash.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler) {
    parent::__construct($config_factory);
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['algos'] = [
      '#default_value' => $this->config('imagehash.settings')->get('algos'),
      '#description' => $this->t('The checked hash algorithm(s) will be calculated when a file is saved. For optimum performance, only enable the hash algorithm(s) you need.'),
      '#options' => imagehash_names(),
      '#title' => $this->t('Enabled hash algorithms'),
      '#type' => 'checkboxes',
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $original_algos = $this->config('imagehash.settings')->get('algos');
    $this->config('imagehash.settings')
      ->set('algos', $form_state->getValue('algos'))
      ->save();
    // Invalidate the views cache if configured algorithms were modified.
    if ($this->moduleHandler->moduleExists('views') && $form_state->getValue('algos') != $original_algos) {
      views_invalidate_cache();
    }
    parent::submitForm($form, $form_state);
  }

}
