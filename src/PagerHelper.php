<?php

namespace Drupal\field_pager;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Helper functions for field_pager.
 */
class PagerHelper {

  /**
   * Add default settings.
   */
  public static function mergeDefaultSettings($settings = []) {

    return [
      'index_name' => 'page',
      'pager_np' => 1,
      'pager_fl' => 1,
      'pager_nb' => 1,
      'items_per_page' => 5,
      'summary' => 0,
    ] + $settings;

  }

  /**
   * Add settings to the form.
   */
  public static function mergeSettingsForm(array $form, FormStateInterface $form_state, $instance, $elements = []) {
    $elements['summary'] = [
      '#type' => 'checkbox',
      '#title' => t('Show summary'),
      '#description' => t('Show text "Page X of N"'),
      '#default_value' => $instance->getSetting('summary'),
    ];
    $elements['index_name'] = [
      '#type' => 'textfield',
      '#title' => t('Index field name'),
      '#default_value' => $instance->getSetting('index_name'),
      '#required' => TRUE,
    ];
    $elements['pager_np'] = [
      '#type' => 'checkbox',
      '#title' => t('Show Next & Previus'),
      '#default_value' => $instance->getSetting('pager_np'),
    ];
    $elements['pager_fl'] = [
      '#type' => 'checkbox',
      '#title' => t('Show First & Last'),
      '#default_value' => $instance->getSetting('pager_fl'),
    ];
    $elements['pager_nb'] = [
      '#type' => 'checkbox',
      '#title' => t('Show All pages'),
      '#default_value' => $instance->getSetting('pager_nb'),
    ];
    $elements['items_per_page'] = [
      '#type' => 'number',
      '#title' => t('The number of items per page.'),
      '#default_value' => $instance->getSetting('items_per_page'),
      '#states' => [
        'visible' => [
          ':input[name$="[settings_edit_form][settings][pager_nb]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return $elements;
  }

  /**
   * Add settings summary.
   */
  public static function mergeSettingsSummary($summary, $instance) {
    $items_per_page = $instance->getSetting('items_per_page');
    $summary[] = "Pager ($items_per_page)";
    return $summary;
  }

  /**
   * Add View.
   */
  public static function mergeView($nb_items, $instance, $fields, $settings = []) {
    $items_per_page = $instance->getSetting('items_per_page');
    $summary = $instance->getSetting('summary');

    $pageCount = \ceil($nb_items / $items_per_page);
    if ($pageCount === 1) {
      // No need for a pager if there's only a single page.
      return $fields;
    }

    $currentPage = pager_find_page();
    pager_default_initialize($nb_items, $items_per_page);

    $elements = [];
    if ($summary) {
      $elements['summary'] = [
        '#markup' => new TranslatableMarkup('Page @index of @total', [
          '@index' => $currentPage + 1,
          '@total' => $pageCount,
        ]),
        '#prefix' => '<div>',
        '#suffix' => '</div>',
      ];
    }
    $elements['fields'] = $fields;

    $elements['pager'] = ['#type' => 'pager'];

    return $elements;
  }

}
