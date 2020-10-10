<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class pi_img_disclaimer extends abstract_module {

    const CONFIG_KEY_BASE = 'PI_IMG_DISCLAIMER_';

    public $group = 'pi_modules_b';
    public $content_width;

    function __construct() {
      parent::__construct();

      $this->group = basename(dirname(__FILE__));

      $this->description .= '<div class="alert alert-warning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';
      $this->description .= '<div class="alert alert-info">' . cm_pi_modular::display_layout() . '</div>';

      if ( $this->enabled ) {
        $this->group = 'pi_modules_' . strtolower(PI_IMG_DISCLAIMER_GROUP);
        $this->content_width = (int)PI_IMG_DISCLAIMER_CONTENT_WIDTH;
      }
    }
    
    function getOutput() {
      global $product_info;
      
      $content_width = $this->content_width;

      $tpl_data = ['group' => $this->group, 'file' => __FILE__];
      include 'includes/modules/block_template.php';
    }

    protected function get_parameters() {
      return [
        'PI_IMG_DISCLAIMER_STATUS' => [
          'title' => 'Enable Image Disclaimer Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'PI_IMG_DISCLAIMER_GROUP' => [
          'title' => 'Module Display',
          'value' => 'B',
          'desc' => 'Where should this module display on the product info page?',
          'set_func' => "tep_cfg_select_option(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'], ",
        ],
        'PI_IMG_DISCLAIMER_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'PI_IMG_DISCLAIMER_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '230',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
  