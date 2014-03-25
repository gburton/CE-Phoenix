<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class bm_account {
    var $code = 'bm_account';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function bm_account() {
      $this->title = MODULE_BOXES_ACCOUNT_TITLE;
      $this->description = MODULE_BOXES_ACCOUNT_DESCRIPTION;

      if ( defined('MODULE_BOXES_ACCOUNT_STATUS') ) {
        $this->sort_order = MODULE_BOXES_ACCOUNT_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_ACCOUNT_STATUS == 'True');

        switch (MODULE_BOXES_ACCOUNT_CONTENT_PLACEMENT) {
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
      global $customer_id, $oscTemplate;

      if ($this->group == 'boxes_footer') {
        $data = '<div class="col-sm-3 col-lg-2">' .
                '  <div class="footerbox account">' .
                '    <h2>' . MODULE_BOXES_ACCOUNT_BOX_TITLE . '</h2>' .
                '    <ul class="list-unstyled">';
      }
      else {
        $data = '<div class="panel panel-default">' .
                '  <div class="panel-heading">' . MODULE_BOXES_ACCOUNT_BOX_TITLE . '</div>' .
                '  <div class="panel-body">' .
                '    <ul class="list-unstyled">';
        }
              
      if (tep_session_is_registered('customer_id')) {
        $data .= '      <li><a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . MODULE_BOXES_ACCOUNT_BOX_ACCOUNT . '</a></li>' .
                 '      <li><a href="' . tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . MODULE_BOXES_ACCOUNT_BOX_ADDRESS_BOOK . '</a></li>' .
                 '      <li><a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . MODULE_BOXES_ACCOUNT_BOX_ORDER_HISTORY . '</a></li>' .
                 '      <li><br><a class="btn btn-danger btn-sm" role="button" href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . '"><i class="glyphicon glyphicon-log-out"></i> ' . MODULE_BOXES_ACCOUNT_BOX_LOGOFF . '</a></li>';
      }
      else {
        $data .= '      <li><a href="' . tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL') . '">' . MODULE_BOXES_ACCOUNT_BOX_CREATE_ACCOUNT . '</a></li>' .
                 '      <li><br><a class="btn btn-success btn-sm" role="button" href="' . tep_href_link(FILENAME_LOGIN, '', 'SSL') . '"><i class="glyphicon glyphicon-log-in"></i> ' . MODULE_BOXES_ACCOUNT_BOX_LOGIN . '</a></li>';
      }

      $data .= '    </ul>' .
               '  </div>' .
               '</div>';

      $oscTemplate->addBlock($data, $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_ACCOUNT_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Account Module', 'MODULE_BOXES_ACCOUNT_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Placement', 'MODULE_BOXES_ACCOUNT_CONTENT_PLACEMENT', 'Footer', 'Should the module be loaded in the left or right column or directly in the footer?', '6', '1', 'tep_cfg_select_option(array(\'Left Column\', \'Right Column\', \'Footer\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_ACCOUNT_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_ACCOUNT_STATUS', 'MODULE_BOXES_ACCOUNT_CONTENT_PLACEMENT', 'MODULE_BOXES_ACCOUNT_SORT_ORDER');
    }
  }
  
