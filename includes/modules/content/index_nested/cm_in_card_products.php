<?php
/*
  Copyright (c) 2018, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

  class cm_in_card_products {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_IN_CARD_PRODUCTS_TITLE;
      $this->description = MODULE_CONTENT_IN_CARD_PRODUCTS_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_IN_CARD_PRODUCTS_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_IN_CARD_PRODUCTS_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_IN_CARD_PRODUCTS_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $current_category_id, $languages_id, $currencies, $PHP_SELF, $currency;

      $content_width = MODULE_CONTENT_IN_CARD_PRODUCTS_CONTENT_WIDTH;
      
      $card_products_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_tax_class_id, pd.products_name, if(s.status, s.specials_new_products_price, p.products_price) as products_price, p.products_quantity as in_stock, p.manufacturers_id, if(s.status, 1, 0) as is_special from products p left join specials s on p.products_id = s.products_id, products_description pd, products_to_categories p2c, categories c where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and c.parent_id = '" . (int)$current_category_id . "' and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by p.products_id desc limit " . (int)MODULE_CONTENT_IN_CARD_PRODUCTS_MAX_DISPLAY);
      $num_card_products = tep_db_num_rows($card_products_query);

      if ($num_card_products > 0) {
        ob_start();
        include('includes/modules/content/' . $this->group . '/templates/tpl_' . basename(__FILE__));
        $template = ob_get_clean(); 
        
        $oscTemplate->addContent($template, $this->group);
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_IN_CARD_PRODUCTS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable New Products Module', 'MODULE_CONTENT_IN_CARD_PRODUCTS_STATUS', 'True', 'Do you want to enable this module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_IN_CARD_PRODUCTS_CONTENT_WIDTH', '12', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum Display', 'MODULE_CONTENT_IN_CARD_PRODUCTS_MAX_DISPLAY', '6', 'Maximum Number of products that should show in this module?', '6', '3', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Products In Each Row (SM)', 'MODULE_CONTENT_IN_CARD_PRODUCTS_DISPLAY_ROW_SM', '2', 'How many products should display per Row in SM (Small) viewport?', '6', '4', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Products In Each Row (MD)', 'MODULE_CONTENT_IN_CARD_PRODUCTS_DISPLAY_ROW_MD', '3', 'How many products should display per Row in MD (Medium) viewport?', '6', '4', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Products In Each Row (LG)', 'MODULE_CONTENT_IN_CARD_PRODUCTS_DISPLAY_ROW_LG', '4', 'How many products should display per Row in LG (Large) viewport?', '6', '4', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Products In Each Row (XL)', 'MODULE_CONTENT_IN_CARD_PRODUCTS_DISPLAY_ROW_XL', '4', 'How many products should display per Row in XL (Extra Large) viewport?', '6', '4', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Button', 'MODULE_CONTENT_IN_CARD_PRODUCTS_BUTTON', 'View', 'Which button should display (View, Buy).', '6', '1', 'tep_cfg_select_option(array(\'View\', \'Buy\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_IN_CARD_PRODUCTS_SORT_ORDER', '300', 'Sort order of display. Lowest is displayed first.', '6', '5', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_IN_CARD_PRODUCTS_STATUS', 'MODULE_CONTENT_IN_CARD_PRODUCTS_CONTENT_WIDTH', 'MODULE_CONTENT_IN_CARD_PRODUCTS_MAX_DISPLAY', 'MODULE_CONTENT_IN_CARD_PRODUCTS_DISPLAY_ROW_SM', 'MODULE_CONTENT_IN_CARD_PRODUCTS_DISPLAY_ROW_MD', 'MODULE_CONTENT_IN_CARD_PRODUCTS_DISPLAY_ROW_LG', 'MODULE_CONTENT_IN_CARD_PRODUCTS_DISPLAY_ROW_XL', 'MODULE_CONTENT_IN_CARD_PRODUCTS_BUTTON', 'MODULE_CONTENT_IN_CARD_PRODUCTS_SORT_ORDER');
    }
  }
