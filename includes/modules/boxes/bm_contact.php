<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class bm_contact {
    var $code = 'bm_contact';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function bm_contact() {
      $this->title = MODULE_BOXES_CONTACT_TITLE;
      $this->description = MODULE_BOXES_CONTACT_DESCRIPTION;

      if ( defined('MODULE_BOXES_CONTACT_STATUS') ) {
        $this->sort_order = MODULE_BOXES_CONTACT_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_CONTACT_STATUS == 'True');

        switch (MODULE_BOXES_CONTACT_CONTENT_PLACEMENT) {
          case 'Left Column':
          $this->group = 'boxes_column_left';
          break;
          case 'Footer':
          $this->group = 'boxes_footer';
          break;
          default:
          $this->group = 'boxes_column_right';
        }
      }
    }

    function execute() {
      global $oscTemplate;

      if ($this->group == 'boxes_footer') {
        $data = '<div class="col-sm-3 col-lg-3">' .
                '  <div class="footerbox contact">' .
                '    <h2>' . MODULE_BOXES_CONTACT_BOX_TITLE . '</h2>';
      }
      else {
        $data = '<div class="panel panel-default">' .
                '  <div class="panel-heading">' . MODULE_BOXES_CONTACT_BOX_TITLE . '</div>' .
                '  <div class="panel-body">';
      }
      
      $data .= '    <address>' .
               '      <strong>' . STORE_NAME . '</strong><br>' . nl2br(STORE_ADDRESS) . '<br>' .
               '      <abbr title="Phone">P:</abbr> ' . STORE_PHONE . '<br>' .
               '      <abbr title="Email">E:</abbr> ' . STORE_OWNER_EMAIL_ADDRESS .
               '    </address>' .
               '    <ul class="list-unstyled">' .
               '      <li><a class="btn btn-success btn-sm" role="button" href="' . tep_href_link(FILENAME_CONTACT_US) . '"><i class="glyphicon glyphicon-send"></i> ' . MODULE_BOXES_CONTACT_BOX_CONTACT . '</a></li>' .
               '    </ul>';
               
      $data .= '  </div>' .
               '</div>';

      $oscTemplate->addBlock($data, $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_CONTACT_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Contact Us Module', 'MODULE_BOXES_CONTACT_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Placement', 'MODULE_BOXES_CONTACT_CONTENT_PLACEMENT', 'Footer', 'Should the module be loaded in the left or right column or directly in the footer?', '6', '1', 'tep_cfg_select_option(array(\'Left Column\', \'Right Column\', \'Footer\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_CONTACT_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_CONTACT_STATUS', 'MODULE_BOXES_CONTACT_CONTENT_PLACEMENT', 'MODULE_BOXES_CONTACT_SORT_ORDER');
    }
  }

