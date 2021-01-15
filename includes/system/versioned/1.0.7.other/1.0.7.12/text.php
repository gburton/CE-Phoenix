<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class Text {

    public static function input(string $s) {
      return trim(static::sanitize(stripslashes($s)));
    }

    public static function is_empty(string $s = null) {
      return is_null($s) || ('' === trim($s));
    }

    public static function is_prefixed_by(string $s, string $prefix) {
      return (substr($s, 0, strlen($prefix)) === $prefix);
    }

    public static function is_suffixed_by(string $s, string $suffix) {
      return (substr($s, -strlen($suffix)) === $suffix);
    }

    public static function ltrim_once(string $s, string $prefix) {
      $length = strlen($prefix);
      if (substr($s, 0, $length) === $prefix) {
        return substr($s, $length);
      }

      return $s;
    }

    public static function output(string $s, $translate = false) {
      return strtr(trim($s), $translate ?: ['"' => '&quot;']);
    }

    public static function prepare(string $s) {
      return trim(stripslashes($s));
    }

    public static function rtrim_once(string $s, string $suffix) {
      $displacement = -strlen($suffix);
      if (substr($s, $displacement) === $suffix) {
        $s = substr($s, 0, $displacement);
      }

      return $s;
    }

    public static function sanitize(string $s) {
      return preg_replace(
        ['{ +}', '{[<>]}'],
        [' ', '_'],
        trim($s));
    }

  }
