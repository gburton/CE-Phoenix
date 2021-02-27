<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_account_set_password extends abstract_executable_module {

    const REQUIRES = [ 'password' ];

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_ACCOUNT_SET_PASSWORD_';

    function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      if ( isset($_SESSION['customer_id']) && ( MODULE_CONTENT_ACCOUNT_SET_PASSWORD_ALLOW_PASSWORD == 'True' ) ) {
        if ( empty($GLOBALS['customer']->get('password')) ) {
          $links =& $GLOBALS['oscTemplate']->_data['account']['account']['links'];

          $counter = 0;
          foreach ( array_keys($links) as $key ) {
            if ( 'password' === $key ) {
              break;
            }

            $counter++;
          }

          $after = array_slice($links, $counter + 1, null, true);

          $links = array_slice($links, 0, $counter, true);

          $links += [
            'set_password' => [
              'title' => MODULE_CONTENT_ACCOUNT_SET_PASSWORD_SET_PASSWORD_LINK_TITLE,
              'link' => tep_href_link('ext/modules/content/account/set_password.php', '', 'SSL'),
              'icon' => 'fas fa-lock fa-5x',
            ],
          ];

          $links += $after;
        }
      }
    }

    public function get_parameters() {
      return [
        'MODULE_CONTENT_ACCOUNT_SET_PASSWORD_STATUS' => [
          'title' => 'Enable Set Account Password',
          'value' => 'True',
          'desc' => 'Do you want to enable the Set Account Password module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_ACCOUNT_SET_PASSWORD_ALLOW_PASSWORD' => [
          'title' => 'Allow Local Passwords',
          'value' => 'True',
          'desc' => 'Allow local account passwords to be set.',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_ACCOUNT_SET_PASSWORD_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
