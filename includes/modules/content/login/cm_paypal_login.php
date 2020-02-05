<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  if ( !class_exists('OSCOM_PayPal') ) {
    require DIR_FS_CATALOG . 'includes/apps/paypal/OSCOM_PayPal.php';
  }

  class cm_paypal_login {

    const REQUIRES = [
      'firstname',
      'lastname',
      'street_address',
      'city',
      'postcode',
      'country',
      'telephone',
      'email_address',
    ];

    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;
    public $_app;

    function __construct() {
      $this->_app = new OSCOM_PayPal();
      $this->_app->loadLanguageFile('modules/LOGIN/LOGIN.php');

      $this->signature = 'paypal|paypal_login|4.0|2.3';

      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = $this->_app->getDef('module_login_title');
      $this->description = '<div align="center">' . $this->_app->drawButton($this->_app->getDef('module_login_legacy_admin_app_button'), tep_href_link('paypal.php', 'action=configure&module=LOGIN'), 'primary', null, true) . '</div>';

      if ( defined('OSCOM_APP_PAYPAL_LOGIN_STATUS') ) {
        $this->sort_order = (defined('OSCOM_APP_PAYPAL_LOGIN_SORT_ORDER') ? OSCOM_APP_PAYPAL_LOGIN_SORT_ORDER : 0);
        $this->enabled = in_array(OSCOM_APP_PAYPAL_LOGIN_STATUS, ['1', '0']);

        if ( OSCOM_APP_PAYPAL_LOGIN_STATUS == '0' ) {
          $this->title .= ' [Sandbox]';
        }

        if ( !function_exists('curl_init') ) {
          $this->description .= '<div class="secWarning">' . $this->_app->getDef('module_login_error_curl') . '</div>';

          $this->enabled = false;
        }

        if ( $this->enabled === true ) {
          if ( ((OSCOM_APP_PAYPAL_LOGIN_STATUS == '1') && (!tep_not_null(OSCOM_APP_PAYPAL_LOGIN_LIVE_CLIENT_ID) || !tep_not_null(OSCOM_APP_PAYPAL_LOGIN_LIVE_SECRET))) || ((OSCOM_APP_PAYPAL_LOGIN_STATUS == '0') && (!tep_not_null(OSCOM_APP_PAYPAL_LOGIN_SANDBOX_CLIENT_ID) || !tep_not_null(OSCOM_APP_PAYPAL_LOGIN_SANDBOX_SECRET))) ) {
            $this->description .= '<div class="secWarning">' . $this->_app->getDef('module_login_error_credentials') . '</div>';

            $this->enabled = false;
          }
        }
      }
    }

    function execute() {
      if ( isset($_GET['action']) ) {
        if ( $_GET['action'] == 'paypal_login' ) {
          $this->preLogin();
        } elseif ( $_GET['action'] == 'paypal_login_process' ) {
          $this->postLogin();
        }
      }

      $use_scopes = ['openid'];

      foreach ( explode(';', OSCOM_APP_PAYPAL_LOGIN_ATTRIBUTES) as $attribute ) {
        foreach ( $this->get_attributes() as $group => $scopes ) {
          if ( isset($scopes[$attribute]) && !in_array($scopes[$attribute], $use_scopes) ) {
            $use_scopes[] = $scopes[$attribute];
          }
        }
      }

      $cm_paypal_login = $this;

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    function guarantee_address($customer_id, $address) {
      $address['id'] = $customer_id;
      $check_query = tep_db_query($GLOBALS['customer_data']->build_read(['address_book_id'], 'address_book', $address) . "' LIMIT 1");
      if ($check = tep_db_fetch_array($check_query)) {
        $_SESSION['sendto'] = $check['address_book_id'];
      } else {
        $GLOBALS['customer_data']->create($address, 'address_book');
      }
    }

    function preLogin() {
      global $customer_data;

      $return_url = tep_href_link('login.php', '', 'SSL');

      if ( isset($_GET['code']) ) {
        $GLOBALS['paypal_login_customer_id'] = false;

        $params = [
          'code' => $_GET['code'],
          'redirect_uri' => str_replace('&amp;', '&', tep_href_link('login.php', 'action=paypal_login', 'SSL')),
        ];

        $response_token = $this->_app->getApiResult('LOGIN', 'GrantToken', $params);

        if ( !isset($response_token['access_token']) && isset($response_token['refresh_token']) ) {
          $params = ['refresh_token' => $response_token['refresh_token']];

          $response_token = $this->_app->getApiResult('LOGIN', 'RefreshToken', $params);
        }

        if ( isset($response_token['access_token']) ) {
          $params = ['access_token' => $response_token['access_token']];

          $response = $this->_app->getApiResult('LOGIN', 'UserInfo', $params);

          if ( isset($response['email']) ) {
            $_SESSION['paypal_login_access_token'] = $response_token['access_token'];
            $_SESSION['paypal_login_customer_id'] = false;
            $customer_details = [
              'firstname' => tep_db_prepare_input($response['given_name']),
              'lastname' => tep_db_prepare_input($response['family_name']),
              'address' => tep_db_prepare_input($response['address']['street_address']),
              'city' => tep_db_prepare_input($response['address']['locality']),
              'zone' => tep_db_prepare_input($response['address']['region']),
              'zone_id' => 0,
              'postcode' => tep_db_prepare_input($response['address']['postal_code']),
              'country' => tep_db_prepare_input($response['address']['country']),
              'country_id' => 0,
              'address_format_id' => 1,
            ];


            $country_query = tep_db_query("SELECT countries_id, address_format_id FROM countries WHERE countries_iso_code_2 = '" . tep_db_input($ship_country) . "' LIMIT 1");
            if ($country = tep_db_fetch_array($country_query)) {
              $customer_details['country_id'] = $country['countries_id'];
              $customer_details['address_format_id'] = $country['address_format_id'];
            }

            if ($customer_details['country_id'] > 0) {
              $zone_query = tep_db_query("SELECT zone_id FROM zones WHERE zone_country_id = '" . (int)$customer_details['country_id'] . "' AND (zone_name = '" . tep_db_input($customer_details['zone']) . "' or zone_code = '" . tep_db_input($customer_details['zone']) . "') LIMIT 1");
              if ($zone = tep_db_fetch_array($zone_query)) {
                $customer_details['zone_id'] = $zone['zone_id'];
              }
            }

            if ( isset($_SESSION['customer_id']) ) {
// check if paypal shipping address exists in the address book
              $this->guarantee_address($_SESSION['customer_id'], $customer_details);
            } else {
// check if e-mail address exists in database and log in or create customer account
              $email_address = tep_db_prepare_input($response['email']);

              $check_query = tep_db_query($customer_data->build_read(['id'], 'customers', ['email_address' => $email_address]) . ' LIMIT 1');
              if ($check = tep_db_fetch_array($check_query)) {
                $_SESSION['paypal_login_customer_id'] = (int)$customer_data->get('id', $check);
                $this->guarantee_address($_SESSION['paypal_login_customer_id'], $customer_details);
              } else {
                $customer_details += [
                  'email_address' => $email_address,
                  'telephone' => '',
                  'fax' => '',
                  'newsletter' => '0',
                  'password' => '',
                ];

                if ($this->hasAttribute('phone') && !empty($response['phone_number'])) {
                  $customer_details['telephone'] = tep_db_prepare_input($response['phone_number']);
                }

                if ($customer_details['zone_id'] > 0) {
                  $customer_details['state'] = '';
                } else {
                  $customer_details['zone_id'] = 0;
                  $customer_details['state'] = $customer_details['zone'];
                }

                $customer_data->create($customer_details);

                $_SESSION['paypal_login_customer_id'] = (int)$customer_data->get('id', $customer_details);
              }
            }

            $_SESSION['billto'] = $_SESSION['sendto'];

            $return_url = tep_href_link('login.php', 'action=paypal_login_process', 'SSL');
          }
        }
      }

      echo '<script>window.opener.location.href="' . str_replace('&amp;', '&', $return_url) . '";window.close();</script>';

      exit;
    }

    function postLogin() {
      if ( isset($_SESSION['paypal_login_customer_id']) ) {
        if ( false !== $_SESSION['paypal_login_customer_id'] ) {
          $GLOBALS['login_customer_id'] = $_SESSION['paypal_login_customer_id'];
        }

        unset($_SESSION['paypal_login_customer_id']);
      }

// Register PayPal Express Checkout as the default payment method
      if ( 'paypal_express' !== ($_SESSION['payment'] ?? null) ) {
        if (defined('MODULE_PAYMENT_INSTALLED') && tep_not_null(MODULE_PAYMENT_INSTALLED)) {
          if ( in_array('paypal_express.php', explode(';', MODULE_PAYMENT_INSTALLED)) ) {
            $ppe = new paypal_express();

            if ( $ppe->enabled ) {
              $_SESSION['payment'] = 'paypal_express';
              $GLOBALS['payment'] =& $_SESSION['payment'];
            }
          }
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('OSCOM_APP_PAYPAL_LOGIN_STATUS');
    }

    function install() {
      tep_redirect(tep_href_link('paypal.php', 'action=configure&subaction=install&module=LOGIN'));
    }

    function remove() {
      tep_redirect(tep_href_link('paypal.php', 'action=configure&subaction=uninstall&module=LOGIN'));
    }

    function keys() {
      return ['OSCOM_APP_PAYPAL_LOGIN_CONTENT_WIDTH', 'OSCOM_APP_PAYPAL_LOGIN_SORT_ORDER'];
    }

    function hasAttribute($attribute) {
      return in_array($attribute, explode(';', OSCOM_APP_PAYPAL_LOGIN_ATTRIBUTES));
    }

    function get_default_attributes() {
      $data = [];

      foreach ( $this->get_attributes() as $group => $attributes ) {
        foreach ( $attributes as $attribute => $scope ) {
          $data[] = $attribute;
        }
      }

      return $data;
    }

    public function get_attributes() {
      return [
        'personal' => [
          'full_name' => 'profile',
          'date_of_birth' => 'profile',
          'age_range' => 'https://uri.paypal.com/services/paypalattributes',
          'gender' => 'profile',
        ],
        'address' => [
          'email_address' => 'email',
          'street_address' => 'address',
          'city' => 'address',
          'state' => 'address',
          'country' => 'address',
          'zip_code' => 'address',
          'phone' => 'phone',
        ],
        'account' => [
          'account_status' => 'https://uri.paypal.com/services/paypalattributes',
          'account_type' => 'https://uri.paypal.com/services/paypalattributes',
          'account_creation_date' => 'https://uri.paypal.com/services/paypalattributes',
          'time_zone' => 'profile',
          'locale' => 'profile',
          'language' => 'profile',
        ],
        'checkout' => ['seamless_checkout' => 'https://uri.paypal.com/services/expresscheckout'],
      ];
    }

  }
