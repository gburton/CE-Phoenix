<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class bm_languages extends abstract_block_module {

    const CONFIG_KEY_BASE = 'MODULE_BOXES_LANGUAGES_';

    public function execute() {
      global $PHP_SELF, $lng, $request_type;

      if (substr(basename($PHP_SELF), 0, 8) !== 'checkout') {
        if (!isset($lng) || !($lng instanceof language)) {
          $lng = new language();
        }

        if (count($lng->catalog_languages) > 1) {
          $languages_string = '';
          $parameters = tep_get_all_get_params(['language', 'currency']) . 'language=';
          foreach($lng->catalog_languages as $key => $value) {
            $languages_string .= ' <a href="' . tep_href_link($PHP_SELF, "$parameters$key", $request_type) . '">'
                               . tep_image('includes/languages/' .  $value['directory'] . '/images/' . $value['image'], htmlspecialchars($value['name']), null, null, null, false)
                               . '</a> ';
          }

          $tpl_data = ['group' => $this->group, 'file' => __FILE__];
          include 'includes/modules/block_template.php';
        }
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_BOXES_LANGUAGES_STATUS' => [
          'title' => 'Enable Languages Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_BOXES_LANGUAGES_CONTENT_PLACEMENT' => [
          'title' => 'Content Placement',
          'value' => 'Right Column',
          'desc' => 'Should the module be loaded in the left or right column?',
          'set_func' => "tep_cfg_select_option(['Left Column', 'Right Column'], ",
        ],
        'MODULE_BOXES_LANGUAGES_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
