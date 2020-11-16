<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class bm_search extends abstract_block_module {

    const CONFIG_KEY_BASE = 'MODULE_BOXES_SEARCH_';

    function execute() {
      $form_output = tep_draw_form('quick_find', tep_href_link('advanced_search_result.php', '', null, false), 'get');
        $form_output .= '<div class="input-group">';
          $form_output .= tep_draw_input_field('keywords', '', 'required aria-required="true" autocomplete="off" placeholder="' . TEXT_SEARCH_PLACEHOLDER . '"', 'search');
          $form_output .= '<div class="input-group-append">';
            $form_output .= '<button type="submit" class="btn btn-info btn-search"><i class="fas fa-search"></i></button>';
          $form_output .= '</div>';
        $form_output .= '</div>';
        $form_output .= tep_draw_hidden_field('search_in_description', '0') . tep_hide_session_id();
      $form_output .= '</form>';

      $tpl_data = ['group' => $this->group, 'file' => __FILE__];
      include 'includes/modules/block_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_BOXES_SEARCH_STATUS' => [
          'title' => 'Enable Search Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_BOXES_SEARCH_CONTENT_PLACEMENT' => [
          'title' => 'Content Placement',
          'value' => 'Right Column',
          'desc' => 'Should the module be loaded in the left or right column?',
          'set_func' => "tep_cfg_select_option(['Left Column', 'Right Column'], ",
        ],
        'MODULE_BOXES_SEARCH_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '5025',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
