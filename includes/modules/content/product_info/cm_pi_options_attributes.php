<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_options_attributes extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PI_OA_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $currencies, $product_info;

      $content_width = (int)MODULE_CONTENT_PI_OA_CONTENT_WIDTH;

      $products_options_name_query = tep_db_query("select distinct po.products_options_id, po.products_options_name from products_options po, products_attributes patrib where patrib.products_id='" . (int)$_GET['products_id'] . "' and patrib.options_id = po.products_options_id and po.language_id = '" . (int)$_SESSION['languages_id'] . "' order by po.sort_order, po.products_options_name");

      if (tep_db_num_rows($products_options_name_query)) {
        $fr_input = $fr_required = '';
        if (MODULE_CONTENT_PI_OA_ENFORCE == 'True') {
          $fr_input    = FORM_REQUIRED_INPUT;
          $fr_required = 'required="required" aria-required="true" ';
        }

        $options = [];
        $options_output = null;
        while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
          $option_choices = [];

          if (MODULE_CONTENT_PI_OA_HELPER == 'True') {
            $option_choices[] = ['id' => '', 'text' => MODULE_CONTENT_PI_OA_ENFORCE_SELECTION];
          }

          $products_options_query = tep_db_query("select pov.*, pa.* from products_attributes pa, products_options_values pov where pa.products_id = " . (int)$_GET['products_id'] . " and pa.options_id = '" . (int)$products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int)$_SESSION['languages_id'] . "' order by pov.sort_order, pov.products_options_values_name");
          while ($products_options = tep_db_fetch_array($products_options_query)) {
            $text = $products_options['products_options_values_name'];
            if ($products_options['options_values_price'] != '0') {
              $text .= ' (' . $products_options['price_prefix']
                     . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id']))
                     .') ';
            }
            $option_choices[] = ['id' => $products_options['products_options_values_id'], 'text' => $text];
          }

          if (is_string($_GET['products_id'])) {
            $selected_attribute = $_SESSION['cart']->contents[$_GET['products_id']]['attributes'][$products_options_name['products_options_id']] ?? false;
          } else {
            $selected_attribute = false;
          }

          $options[] = [
            'id' => $products_options_name['products_options_id'],
            'name' => $products_options_name['products_options_name'],
            'choices' => $option_choices,
            'selection' => $selected_attribute,
          ];
        }

        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PI_OA_STATUS' => [
          'title' => 'Enable Options & Attributes',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_OA_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_OA_HELPER' => [
          'title' => 'Add Helper Text',
          'value' => 'True',
          'desc' => 'Should first option in dropdown be Helper Text?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_OA_ENFORCE' => [
          'title' => 'Enforce Selection',
          'value' => 'True',
          'desc' => 'Should customer be forced to select option(s)?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_OA_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '80',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

