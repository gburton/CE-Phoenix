<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  abstract class capabilities_manager {

    protected static $capabilities;

    public function __construct() {
      if (!isset(static::$capabilities)) {
        static::$capabilities = static::CAPABILITIES;

        $parameters = [
          'capabilities' => &static::$capabilities,
        ];

        $GLOBALS['OSCOM_Hooks']->call('system', static::LISTENER_NAME, $parameters);
      }
    }

    public function can($key) {
      return isset(static::$capabilities[$key]) && is_callable(static::$capabilities[$key]);
    }

  }
