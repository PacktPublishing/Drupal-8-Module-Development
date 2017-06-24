<?php

namespace Drupal\license_plate\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'default_license_plate_widget' widget.
 *
 * @FieldWidget(
 *   id = "default_license_plate_widget",
 *   label = @Translation("Default license plate widget"),
 *   field_types = {
 *     "license_plate"
 *   }
 * )
 */
class DefaultLicensePlateWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'number_size' => 60,
      'code_size' => 5,
      'fieldset_state' => 'open',
      'placeholder' => [
        'number' => '',
        'code' => '',
      ],
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['number_size'] = [
      '#type' => 'number',
      '#title' => t('Size of plate number textfield'),
      '#default_value' => $this->getSetting('number_size'),
      '#required' => TRUE,
      '#min' => 1,
      '#max' => $this->getFieldSetting('number_max_length'),
    ];

    $elements['code_size'] = [
      '#type' => 'number',
      '#title' => t('Size of plate code textfield'),
      '#default_value' => $this->getSetting('code_size'),
      '#required' => TRUE,
      '#min' => 1,
      '#max' => $this->getFieldSetting('code_max_length'),
    ];

    $elements['fieldset_state'] = [
      '#type' => 'select',
      '#title' => t('Fieldset default state'),
      '#options' => [
        'open' => t('Open'),
        'closed' => t('Closed')
      ],
      '#default_value' => $this->getSetting('fieldset_state'),
      '#description' => t('The default state of the fieldset which contains the two plate fields: open or closed')
    ];

    $elements['placeholder'] = [
      '#type' => 'details',
      '#title' => t('Placeholder'),
      '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];

    $placeholder_settings = $this->getSetting('placeholder');
    $elements['placeholder']['number'] = [
      '#type' => 'textfield',
      '#title' => t('Number field'),
      '#default_value' => $placeholder_settings['number'],
    ];
    $elements['placeholder']['code'] = [
      '#type' => 'textfield',
      '#title' => t('Code field'),
      '#default_value' => $placeholder_settings['code'],
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = t('License plate size: @number (for number) and @code (for code)', ['@number' => $this->getSetting('number_size'), '@code' => $this->getSetting('code_size')]);
    $placeholder_settings = $this->getSetting('placeholder');
    if (!empty($placeholder_settings['number']) && !empty($placeholder_settings['code'])) {
      $placeholder = $placeholder_settings['number'] . ' ' . $placeholder_settings['code'];
      $summary[] = t('Placeholder: @placeholder', ['@placeholder' => $placeholder]);
    }
    $summary[] = t('Fieldset state: @state', ['@state' => $this->getSetting('fieldset_state')]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['details'] = [
      '#type' => 'details',
      '#title' => $element['#title'],
      '#open' => $this->getSetting('fieldset_state') == 'open' ? TRUE : FALSE,
      '#description' => $element['#description'],
    ] + $element;

    $placeholder_settings = $this->getSetting('placeholder');
    $this->addCodeField($element, $items, $delta, $placeholder_settings);

    $element['details']['number'] = [
      '#type' => 'textfield',
      '#title' => t('Plate number'),
      '#default_value' => isset($items[$delta]->number) ? $items[$delta]->number : NULL,
      '#size' => $this->getSetting('number_size'),
      '#placeholder' => $placeholder_settings['number'],
      '#maxlength' => $this->getFieldSetting('number_max_length'),
      '#description' => '',
      '#required' => $element['#required'],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$value) {
      $value['number'] = $value['details']['number'];
      $value['code'] = $value['details']['code'];
      unset($value['details']);
    }

    return $values;
  }

  /**
   * Adds the license plate code field to the form element
   *
   * @param $element
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   * @param $delta
   * @param $placeholder_settings
   */
  protected function addCodeField(&$element, FieldItemListInterface $items, $delta, $placeholder_settings) {
    $element['details']['code'] = [
      '#title' => t('Plate code'),
      '#default_value' => isset($items[$delta]->code) ? $items[$delta]->code : NULL,
      '#description' => '',
      '#required' => $element['#required'],
    ];

    $codes = $this->getFieldSetting('codes');
    if (!$codes) {
      $element['details']['code'] += [
        '#type' => 'textfield',
        '#placeholder' => $placeholder_settings['code'],
        '#maxlength' => $this->getFieldSetting('code_max_length'),
        '#size' => $this->getSetting('code_size'),
      ];
      return;
    }

    $codes = explode("\r\n", $codes);
    $element['details']['code'] += [
      '#type' => 'select',
      '#options' => array_combine($codes, $codes),
    ];
  }
}
