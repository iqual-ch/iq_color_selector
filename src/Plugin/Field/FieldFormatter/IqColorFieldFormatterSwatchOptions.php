<?php

namespace Drupal\iq_color_selector\Plugin\Field\FieldFormatter;

use Drupal\color_field\ColorHex;
use Drupal\color_field\Plugin\Field\FieldFormatter\ColorFieldFormatterSwatch;
use Drupal\color_field\Plugin\Field\FieldType\ColorFieldType;
use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Template\Attribute;

/**
 * Plugin implementation of the color_field swatch formatter.
 *
 * @FieldFormatter(
 *   id = "iq_color_field_formatter_swatch_options",
 *   module = "color_field",
 *   label = @Translation("iQ Color swatch options"),
 *   field_types = {
 *     "color_field_type"
 *   }
 * )
 */
class IqColorFieldFormatterSwatchOptions extends ColorFieldFormatterSwatch {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $item = NULL;
    $settings = $this->getSettings();

    $elements = [];

    $hexes = [];

    $name = Html::getUniqueId("color-field");
    foreach ($items as $item) {
      $hexes[] = $hex = $this->viewRawValue($item);
      $id = Html::getUniqueId("color-field-$hex");
    }

    $elements[] = [
      '#theme' => 'color_field_formatter_swatch_option_gradient',
      '#id' => $id,
      '#name' => $name,
      '#label' => $items->getEntity()->name->value ?? '',
      '#input_type' => $this->fieldDefinition->getFieldStorageDefinition()->isMultiple() ? 'checkbox' : 'radio',
      '#value' => $hex,
      '#shape' => $settings['shape'],
      '#height' => is_numeric($settings['height']) ? "{$settings['height']}px" : $settings['height'],
      '#width' => is_numeric($settings['width']) ? "{$settings['width']}px" : $settings['width'],
      '#color' => $this->viewValue($item),
      '#hexes' => $hexes,
      '#attributes' => new Attribute([
        'class' => [
          "color_field__swatch--{$settings['shape']}",
        ],
      ]),
    ];
    if ($settings['data_attribute']) {
      $elements[0]['#attributes']['data-color'] = $hex;
    }

    return $elements;
  }

  /**
   * Return the raw field value.
   *
   * @param \Drupal\color_field\Plugin\Field\FieldType\ColorFieldType $item
   *   The color field item.
   *
   * @return string
   *   The color hex value.
   */
  protected function viewRawValue(ColorFieldType $item) {
    return (new ColorHex($item->color, $item->opacity))->toString(FALSE);
  }

}
