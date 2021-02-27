<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  if (version_compare(PHP_VERSION, 7.3, '<')) {
    // Prior to 7.3, the options signature did not exist
    // this fakes it
    class Cookie {

      public static function save_session_parameters($options = COOKIE_OPTIONS) {
        foreach (session_get_cookie_params() as $parameter => $value) {
          if (!isset($options[$parameter])) {
            $options[$parameter] = $value;
          }
        }

        $path = $options['path'] ?? '/;';
        if (isset($options['samesite'])) {
          $path .= '; samesite=' . $options['samesite'];
        }

        session_set_cookie_params(
          $options['lifetime'] ?? 0,
          $path,
          $options['domain'] ?? '',
          $options['secure'] ?? false,
          $options['httponly'] ?? false);
      }

      public static function save($name, $value, $options = COOKIE_OPTIONS) {
        unset($options['lifetime']);
        $path = $options['path'] ?? '/';
        if (isset($options['samesite'])) {
          $path .= '; samesite=' . $options['samesite'];
        }

        setcookie(
          $name,
          $value,
          $options['expires'] ?? strtotime('+1 month'),
          $path,
          $options['domain'] ?? '',
          $options['secure'] ?? false,
          $options['httponly'] ?? false);
      }

    }
  } else {
    class Cookie {

      public static function save_session_parameters($options = COOKIE_OPTIONS) {
        session_set_cookie_params($options);
      }

      public static function save($name, $value, $options = COOKIE_OPTIONS) {
        unset($options['lifetime']);
        if (!isset($options['expires'])) {
          $options['expires'] = strtotime('+1 month');
        }

        setcookie($name, $value, $options);
      }

    }
  }
