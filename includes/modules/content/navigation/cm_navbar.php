<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
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

      $navbar_style   = (MODULE_CONTENT_NAVBAR_STYLE == 'Inverse') ? ' navbar-inverse' : ' navbar-default';
      $navbar_corners = (MODULE_CONTENT_NAVBAR_CORNERS == 'Yes') ? '' : ' navbar-no-corners';
      $navbar_margin  = (MODULE_CONTENT_NAVBAR_MARGIN == 'Yes') ? '' : ' navbar-no-margin';  

      switch(MODULE_CONTENT_NAVBAR_FIXED) {
        case 'Top':
          $navbar_fixed = ' navbar-fixed-top';
          $navbar_css   = '<style scoped>body { padding-top: 50px; }</style>';
        break;
        case 'Bottom':
          $navbar_fixed = ' navbar-fixed-bottom';
          $navbar_css   = '<style scoped>body { padding-bottom: 50px; }</style>';
        break;
        default:
          $navbar_fixed = $navbar_css = '';
      }
      
      
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
          include('includes/modules/content/' . $this->group . '/templates/navbar.php');
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
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Navbar Style', 'MODULE_CONTENT_NAVBAR_STYLE', 'Inverse', 'What style should the Navbar have?  See http://getbootstrap.com/components/#navbar-inverted', '6', '0', 'tep_cfg_select_option(array(\'Default\', \'Inverse\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Navbar Corners', 'MODULE_CONTENT_NAVBAR_CORNERS', 'No', 'Should the Navbar have Corners?', '6', '0', 'tep_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Navbar Margin', 'MODULE_CONTENT_NAVBAR_MARGIN', 'No', 'Should the Navbar have a bottom Margin?', '6', '0', 'tep_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Navbar Fixed Position', 'MODULE_CONTENT_NAVBAR_FIXED', 'Floating', 'Should the Navbar stay fixed on Top/Bottom of the page or Floating?', '6', '0', 'tep_cfg_select_option(array(\'Floating\', \'Top\', \'Bottom\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_NAVBAR_SORT_ORDER', '10', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_NAVBAR_STATUS', 'MODULE_CONTENT_NAVBAR_STYLE', 'MODULE_CONTENT_NAVBAR_CORNERS', 'MODULE_CONTENT_NAVBAR_MARGIN', 'MODULE_CONTENT_NAVBAR_FIXED', 'MODULE_CONTENT_NAVBAR_SORT_ORDER');
    }
  }
