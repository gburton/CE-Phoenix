<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class pi_options_attributes extends abstract_module {

    const CONFIG_KEY_BASE = 'PI_OA_';

    public $group = 'pi_modules_c';
    public $content_width;

    function __construct() {
      parent::__construct();

      $this->group = basename(dirname(__FILE__));

      $this->description .= '<div class="alert alert-warning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';
      $this->description .= '<div class="alert alert-info">' . cm_pi_modular::display_layout() . '</div>';

      if ( $this->enabled ) {
        $this->group = 'pi_modules_' . strtolower(PI_OA_GROUP);
        $this->content_width = (int)PI_OA_CONTENT_WIDTH;
      }
    }

    function getOutput() {
      global $currencies, $product_info;

      $products_options_name_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT DISTINCT po.products_options_id, po.products_options_name
  FROM products_options po INNER JOIN products_attributes patrib ON patrib.options_id = po.products_options_id
  WHERE patrib.products_id = %d AND po.language_id = %d
  ORDER BY po.sort_order, po.products_options_name
EOSQL
        , (int)$_GET['products_id'], (int)$_SESSION['languages_id']));

      if (tep_db_num_rows($products_options_name_query)) {
        $content_width = (int)PI_OA_CONTENT_WIDTH;

        $fr_input = $fr_required = '';
        if (PI_OA_ENFORCE == 'True') {
          $fr_input    = FORM_REQUIRED_INPUT;
          $fr_required = 'required="required" aria-required="true" ';
        }

        $tax_rate = tep_get_tax_rate($product_info['products_tax_class_id']);

        $options = [];
        while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
          $choices = [];

          if (PI_OA_HELPER == 'True') {
            $choices[] = ['id' => '', 'text' => PI_OA_ENFORCE_SELECTION];
          }

          $products_options_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT pov.*, pa.*
 FROM products_attributes pa INNER JOIN products_options_values pov ON pa.options_values_id = pov.products_options_values_id
 WHERE pa.products_id = %d AND pa.options_id = %d AND pov.language_id = %d
 ORDER BY pov.sort_order, pov.products_options_values_name 
EOSQL
            , (int)$_GET['products_id'], (int)$products_options_name['products_options_id'], (int)$_SESSION['languages_id']));
          while ($products_options = tep_db_fetch_array($products_options_query)) {
            $text = $products_options['products_options_values_name'];
            if ($products_options['options_values_price'] != '0') {
              $text .= ' (' . $products_options['price_prefix']
                     . $currencies->display_price($products_options['options_values_price'], $tax_rate)
                     . ') ';
            }

            $choices[] = ['id' => $products_options['products_options_values_id'], 'text' => $text];
          }

          if (is_string($_GET['products_id'])) {
            $selected_attribute = $_SESSION['cart']->contents[$_GET['products_id']]['attributes'][$products_options_name['products_options_id']] ?? false;
          } else {
            $selected_attribute = false;
          }

          $options[] = [
            'id' => $products_options_name['products_options_id'],
            'name' => $products_options_name['products_options_name'],
            'menu' => tep_draw_pull_down_menu(
                        'id[' . $products_options_name['products_options_id'] . ']',
                        $choices,
                        $selected_attribute,
                        $fr_required . 'id="input_' . $products_options_name['products_options_id'] . '"'
                      ),
          ];
        }

        $tpl_data = ['group' => $this->group, 'file' => __FILE__];
        include 'includes/modules/block_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'PI_OA_STATUS' => [
          'title' => 'Enable Options & Attributes',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'PI_OA_GROUP' => [
          'title' => 'Module Display',
          'value' => 'C',
          'desc' => 'Where should this module display on the product info page?',
          'set_func' => "tep_cfg_select_option(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'], ",
        ],
        'PI_OA_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'PI_OA_HELPER' => [
          'title' => 'Add Helper Text',
          'value' => 'True',
          'desc' => 'Should first option in dropdown be Helper Text?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'PI_OA_ENFORCE' => [
          'title' => 'Enforce Selection',
          'value' => 'True',
          'desc' => 'Should customer be forced to select option(s)?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'PI_OA_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '310',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

