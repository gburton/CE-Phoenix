<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class Application {

    public function check_ssl_session_id() {
// verify the ssl_session_id if the feature is enabled
      if ( ($GLOBALS['request_type'] === 'SSL') && (SESSION_CHECK_SSL_SESSION_ID === 'True') && $GLOBALS['session_started'] ) {
        $ssl_session_id = getenv('SSL_SESSION_ID');
        if (!isset($_SESSION['SSL_SESSION_ID'])) {
          $_SESSION['SSL_SESSION_ID'] = $ssl_session_id;
        }

        if ($_SESSION['SSL_SESSION_ID'] !== $ssl_session_id) {
          tep_session_destroy();
          tep_redirect(tep_href_link('ssl_check.php'));
        }
      }
    }

    public function check_user_agent() {
// verify the browser user agent if the feature is enabled
      if (SESSION_CHECK_USER_AGENT == 'True') {
        $http_user_agent = getenv('HTTP_USER_AGENT');
        if (!isset($_SESSION['SESSION_USER_AGENT'])) {
          $_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
        }

        if ($_SESSION['SESSION_USER_AGENT'] != $http_user_agent) {
          tep_session_destroy();
          tep_redirect(tep_href_link('login.php'));
        }
      }
    }

    public function check_ip() {
// verify the IP address if the feature is enabled
      if (SESSION_CHECK_IP_ADDRESS == 'True') {
        $ip_address = tep_get_ip_address();
        if (!isset($_SESSION['SESSION_IP_ADDRESS'])) {
          $_SESSION['SESSION_IP_ADDRESS'] = $ip_address;
        }

        if ($_SESSION['SESSION_IP_ADDRESS'] != $ip_address) {
          tep_session_destroy();
          tep_redirect(tep_href_link('login.php'));
        }
      }
    }

    public function ensure_session_cart() {
      if (!isset($_SESSION['cart']) || !($_SESSION['cart'] instanceof shoppingCart)) {
        $_SESSION['cart'] = new shoppingCart();
      }
    }

    public function fix_numeric_locale() {
      static $_system_locale_numeric = 0;

// Prevent LC_ALL from setting LC_NUMERIC to a locale with 1,0 float/decimal values instead of 1.0 (see bug #634)
      $_system_locale_numeric = setlocale(LC_NUMERIC, $_system_locale_numeric);
    }

    public function set_session_language() {
      if (!isset($_SESSION['language']) || isset($_GET['language'])) {
        $GLOBALS['lng'] = language::build();

        $GLOBALS['languages_id'] =& $_SESSION['languages_id'];
        $GLOBALS['language'] =& $_SESSION['language'];
      }

      $this->fix_numeric_locale();
      return language::map_to_translation('.php');
    }

    public function set_template_title() {
      $GLOBALS['oscTemplate']->setTitle(TITLE);
    }

    public function ensure_navigation_history() {
      if (!isset($_SESSION['navigation']) || !($_SESSION['navigation'] instanceof navigationHistory)) {
        $_SESSION['navigation'] = new navigationHistory();
        $GLOBALS['navigation'] = &$_SESSION['navigation'];
      }

      $_SESSION['navigation']->add_current_page();
    }

    public function set_customer_if_identified() {
      if (isset($_SESSION['customer_id'])) {
        $GLOBALS['customer'] = new customer($_SESSION['customer_id']);
      }
    }

  }
