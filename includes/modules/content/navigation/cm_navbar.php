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

  class cm_navbar {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_NAVBAR_TITLE;
      $this->description = MODULE_CONTENT_NAVBAR_DESCRIPTION;

      if ( defined('MODULE_CONTENT_NAVBAR_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_NAVBAR_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_NAVBAR_STATUS == 'True');
      }
    }

    function execute() {
      global $language, $oscTemplate;
      
      $style_array = array();
      $style_array[] = MODULE_CONTENT_NAVBAR_STYLE_BG;
      $style_array[] = MODULE_CONTENT_NAVBAR_STYLE_FG;
      $style_array[] = MODULE_CONTENT_NAVBAR_FIXED;
      $style_array[] = MODULE_CONTENT_NAVBAR_COLLAPSE;
      
      $navbar_style = implode(' ', $style_array);      
      
      if ( defined('MODULE_CONTENT_NAVBAR_INSTALLED') && tep_not_null(MODULE_CONTENT_NAVBAR_INSTALLED) ) {
        $nav_array = explode(';', MODULE_CONTENT_NAVBAR_INSTALLED);

        $navbar_modules = array();

        foreach ( $nav_array as $nbm ) {
          $class = substr($nbm, 0, strrpos($nbm, '.'));

          if ( !class_exists($class) ) {
            include('includes/languages/' . $language . '/modules/navbar_modules/' . $nbm);
            require('includes/modules/navbar_modules/' . $class . '.php');
          }

          $nav = new $class();

          if ( $nav->isEnabled() ) {
            $navbar_modules[] = $nav->getOutput();
          }
        }

        if ( !empty($navbar_modules) ) {          
          ob_start();
          include('includes/modules/content/' . $this->group . '/templates/tpl_' . basename(__FILE__));
          $template = ob_get_clean();

          $oscTemplate->addContent($template, $this->group);
        }
      }      
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_NAVBAR_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Navbar Module', 'MODULE_CONTENT_NAVBAR_STATUS', 'True', 'Should the Navbar be shown? ', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Background Colour Scheme', 'MODULE_CONTENT_NAVBAR_STYLE_BG', 'bg-light', 'What background colour should the Navbar have?  See https://getbootstrap.com/docs/4.0/utilities/colors/#background-color', '6', '0', 'tep_cfg_select_option(array(\'bg-primary\', \'bg-secondary\', \'bg-success\', \'bg-danger\', \'bg-warning\', \'bg-info\', \'bg-light\', \'bg-dark\', \'bg-white\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Link Colour Scheme', 'MODULE_CONTENT_NAVBAR_STYLE_FG', 'navbar-light', 'What foreground colour should the Navbar have?  See https://getbootstrap.com/docs/4.0/utilities/colors/#background-color', '6', '0', 'tep_cfg_select_option(array(\'navbar-dark\', \'navbar-light\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Placement', 'MODULE_CONTENT_NAVBAR_FIXED', 'floating', 'Should the Navbar be Placed or Floating? See https://getbootstrap.com/docs/4.0/components/navbar/#placement', '6', '0', 'tep_cfg_select_option(array(\'fixed-top\', \'fixed-bottom\', \'sticky-top\', \'floating\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Collapse', 'MODULE_CONTENT_NAVBAR_COLLAPSE', 'navbar-expand-sm', 'When should the Navbar Show? See https://getbootstrap.com/docs/4.0/components/navbar/#how-it-works', '6', '0', 'tep_cfg_select_option(array(\'navbar-expand-sm\', \'navbar-expand-md\', \'navbar-expand-lg\', \'navbar-expand-xl\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_NAVBAR_SORT_ORDER', '10', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_NAVBAR_STATUS', 'MODULE_CONTENT_NAVBAR_STYLE_BG', 'MODULE_CONTENT_NAVBAR_STYLE_FG', 'MODULE_CONTENT_NAVBAR_FIXED', 'MODULE_CONTENT_NAVBAR_COLLAPSE', 'MODULE_CONTENT_NAVBAR_SORT_ORDER');
    }
  }
  