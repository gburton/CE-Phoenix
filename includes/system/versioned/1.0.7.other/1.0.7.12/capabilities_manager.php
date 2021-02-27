<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
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
