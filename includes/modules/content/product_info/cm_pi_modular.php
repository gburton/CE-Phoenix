<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_modular extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PI_MODULAR_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $oscTemplate, $product_info;

      $content_width = (int)MODULE_CONTENT_PI_MODULAR_CONTENT_WIDTH;
      $slot_array = ['a' => (int)MODULE_CONTENT_PI_MODULAR_A_WIDTH, 
                     'b' => (int)MODULE_CONTENT_PI_MODULAR_B_WIDTH, 
                     'c' => (int)MODULE_CONTENT_PI_MODULAR_C_WIDTH, 
                     'd' => (int)MODULE_CONTENT_PI_MODULAR_D_WIDTH, 
                     'e' => (int)MODULE_CONTENT_PI_MODULAR_E_WIDTH, 
                     'f' => (int)MODULE_CONTENT_PI_MODULAR_F_WIDTH, 
                     'g' => (int)MODULE_CONTENT_PI_MODULAR_G_WIDTH, 
                     'h' => (int)MODULE_CONTENT_PI_MODULAR_H_WIDTH, 
                     'i' => (int)MODULE_CONTENT_PI_MODULAR_I_WIDTH];
      
      if ( defined('MODULE_CONTENT_PI_INSTALLED') && tep_not_null(MODULE_CONTENT_PI_INSTALLED) ) {
        $pi_array = explode(';', MODULE_CONTENT_PI_INSTALLED);

        $pi_modules = [];

        foreach ( $pi_array as $pim ) {
          $class = pathinfo($pim, PATHINFO_FILENAME);

          $p_i = new $class();
          if ( $p_i->isEnabled() ) {
            $pi_modules[] = $p_i->getOutput();
          }
        }

        if ( [] !== $pi_modules ) {
          $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
          include 'includes/modules/content/cm_template.php';
        }
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PI_MODULAR_STATUS' => [
          'title' => 'Enable &pi; Modular product_info',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_MODULAR_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_MODULAR_A_WIDTH' => [
          'title' => 'Slot Width: A',
          'value' => '12',
          'desc' => 'What width should Slot A be?  Note that Slots in a Row should totalise 12.',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_MODULAR_B_WIDTH' => [
          'title' => 'Slot Width: B',
          'value' => '6',
          'desc' => 'What width should Slot B be?  Note that Slots in a Row should totalise 12.',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_MODULAR_C_WIDTH' => [
          'title' => 'Slot Width: C',
          'value' => '6',
          'desc' => 'What width should Slot C be?  Note that Slots in a Row should totalise 12.',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_MODULAR_D_WIDTH' => [
          'title' => 'Slot Width: D',
          'value' => '4',
          'desc' => 'What width should Slot D be?  Note that Slots in a Row should totalise 12.',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_MODULAR_E_WIDTH' => [
          'title' => 'Slot Width: E',
          'value' => '4',
          'desc' => 'What width should Slot E be?  Note that Slots in a Row should totalise 12.',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_MODULAR_F_WIDTH' => [
          'title' => 'Slot Width: F',
          'value' => '4',
          'desc' => 'What width should Slot F be?  Note that Slots in a Row should totalise 12.',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_MODULAR_G_WIDTH' => [
          'title' => 'Slot Width: G',
          'value' => '6',
          'desc' => 'What width should Slot G be?  Note that Slots in a Row should totalise 12.',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_MODULAR_H_WIDTH' => [
          'title' => 'Slot Width: H',
          'value' => '6',
          'desc' => 'What width should Slot H be?  Note that Slots in a Row should totalise 12.',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_MODULAR_I_WIDTH' => [
          'title' => 'Slot Width: I',
          'value' => '12',
          'desc' => 'What width should Slot I be?  Note that Slots in a Row should totalise 12.',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_MODULAR_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '59',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }
    
    public static function display_layout() {
      if ( defined('MODULE_CONTENT_PI_MODULAR_STATUS') ) {
        $arr = ['A' => ['s' => MODULE_CONTENT_PI_MODULAR_A_WIDTH, 'c' => '96858f'],  
                'B' => ['s' => MODULE_CONTENT_PI_MODULAR_B_WIDTH, 'c' => '6d7993'],  
                'C' => ['s' => MODULE_CONTENT_PI_MODULAR_C_WIDTH, 'c' => '9099a2'],  
                'D' => ['s' => MODULE_CONTENT_PI_MODULAR_D_WIDTH, 'c' => 'd5d5d5'],   
                'E' => ['s' => MODULE_CONTENT_PI_MODULAR_E_WIDTH, 'c' => '96858f'],  
                'F' => ['s' => MODULE_CONTENT_PI_MODULAR_F_WIDTH, 'c' => '6d7993'],   
                'G' => ['s' => MODULE_CONTENT_PI_MODULAR_G_WIDTH, 'c' => '9099a2'],   
                'H' => ['s' => MODULE_CONTENT_PI_MODULAR_H_WIDTH, 'c' => 'd5d5d5'],   
                'I' => ['s' => MODULE_CONTENT_PI_MODULAR_I_WIDTH, 'c' => '96858f']];
                   
        $c = 0; $img = null;
        foreach ($arr as $x => $y) {
          $img .= '<span style="color: white; font-weight: bold; font-size: 20px; background: #' . $y['c'] . '; font-family: courier;">' . $x . str_repeat('&nbsp;', $y['s']-1) . '</span>';
          $c += $y['s'];
          if ($c > 11) {
            $c = 0;
            $img .= '<br>';
          }
        }

        return $img;
      }
      
      return null;
    }

  }
  