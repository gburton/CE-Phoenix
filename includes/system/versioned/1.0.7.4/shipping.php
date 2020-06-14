<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class shipping {

    public $modules;

// class constructor
    public function __construct($module = '') {
      if (defined('MODULE_SHIPPING_INSTALLED') && tep_not_null(MODULE_SHIPPING_INSTALLED)) {
        $this->modules = explode(';', MODULE_SHIPPING_INSTALLED);

        $include_modules = [];

        if ( (tep_not_null($module)) && (in_array(substr($module['id'], 0, strpos($module['id'], '_')) . '.php', $this->modules)) ) {
          $class = substr($module['id'], 0, strpos($module['id'], '_'));
          $include_modules[] = [
            'class' => $class,
            'file' => "$class.php",
          ];
        } else {
          foreach ($this->modules as $value) {
            $include_modules[] = [
              'class' => pathinfo($value, PATHINFO_FILENAME),
              'file' => $value,
            ];
          }
        }

        foreach ($include_modules as $m) {
          $GLOBALS[$m['class']] = new $m['class']();
        }
      }
    }

    public function quote($method = '', $module = '') {
      global $total_weight, $shipping_weight, $shipping_quoted, $shipping_num_boxes;

      $quotes_array = [];

      if (is_array($this->modules)) {
        $shipping_quoted = '';
        $shipping_num_boxes = 1;
        $shipping_weight = $total_weight;

        $padded_weight = $shipping_weight * SHIPPING_BOX_PADDING / 100;
        if (SHIPPING_BOX_WEIGHT >= $padded_weight) {
          $shipping_weight += SHIPPING_BOX_WEIGHT;
        } else {
          $shipping_weight += $padded_weight;
        }

        if ($shipping_weight > SHIPPING_MAX_WEIGHT) { // Split into many boxes
          $shipping_num_boxes = ceil($shipping_weight/SHIPPING_MAX_WEIGHT);
          $shipping_weight = $shipping_weight/$shipping_num_boxes;
        }

        $include_quotes = [];

        foreach ($this->modules as $value) {
          $class = pathinfo($value, PATHINFO_FILENAME);
          if (tep_not_null($module)) {
            if ( ($module == $class) && ($GLOBALS[$class]->enabled) ) {
              $include_quotes[] = $class;
            }
          } elseif ($GLOBALS[$class]->enabled) {
            $include_quotes[] = $class;
          }
        }

        foreach ($include_quotes as $q) {
          $quotes = $GLOBALS[$q]->quote($method);
          if (is_array($quotes)) {
            $quotes_array[] = $quotes;
          }
        }
      }

      return $quotes_array;
    }

    public function cheapest() {
      if (is_array($this->modules)) {
        $rates = [];

        foreach ($this->modules as $value) {
          $class = pathinfo($value, PATHINFO_FILENAME);
          if ($GLOBALS[$class]->enabled) {
            $quotes = $GLOBALS[$class]->quotes;
            foreach ($quotes['methods'] as $method) {
              if (isset($method['cost']) && tep_not_null($method['cost'])) {
                $rates[] = [
                  'id' => $quotes['id'] . '_' . $method['id'],
                  'title' => $quotes['module'] . ' (' . $method['title'] . ')',
                  'cost' => $method['cost'],
                ];
              }
            }
          }
        }

        $cheapest = $rates[0] ?? false;
        foreach ($rates as $rate) {
          if ($rate['cost'] < $cheapest['cost']) {
            $cheapest = $rate;
          }
        }

        return $cheapest;
      }
    }

    public static function is_enabled($id) {
      if (!is_string($id)) {
        return false;
      }

      return ($GLOBALS[substr($id, 0, strpos($id, '_'))]->enabled ?? false);
    }

    public static function ensure_enabled() {
      if (static::is_enabled($_SESSION['shipping']['id'] ?? $_SESSION['shipping'] ?? null)) {
        return;
      }

      unset($_SESSION['shipping']);
    }

    public function count() {
      return count(array_filter($this->modules, function ($m) {
        return $GLOBALS[pathinfo($m, PATHINFO_FILENAME)]->enabled ?? false;
      }));
    }

    public function process_selection() {
      if (tep_not_null($_POST['comments'])) {
        $_SESSION['comments'] = tep_db_prepare_input($_POST['comments']);
      }

      if ( ($GLOBALS['module_count'] <= 0) && !$GLOBALS['free_shipping'] ) {
        if ( defined('SHIPPING_ALLOW_UNDEFINED_ZONES') && (SHIPPING_ALLOW_UNDEFINED_ZONES == 'False') ) {
          unset($_SESSION['shipping']);
          return;
        }

        $_SESSION['shipping'] = false;
        tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
      }

      if ( (isset($_POST['shipping'])) && (strpos($_POST['shipping'], '_')) ) {
        $_SESSION['shipping'] = $_POST['shipping'];

        list($module, $shipping_method) = explode('_', $_SESSION['shipping']);
        if ('free_free' === $_SESSION['shipping']) {
          $quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
          $quote[0]['methods'][0]['cost'] = '0';
        } elseif (is_object($GLOBALS[$module] ?? null)) {
          $quote = $GLOBALS['shipping_modules']->quote($shipping_method, $module);
        } else {
          unset($_SESSION['shipping']);
          return;
        }

        if (isset($quote['error'])) {
          unset($_SESSION['shipping']);
          return;
        }

        if ( isset($quote[0]['methods'][0]['title'], $quote[0]['methods'][0]['cost']) ) {
          if ($GLOBALS['free_shipping']) {
            $title = $quote[0]['methods'][0]['title'];
          } else {
            $title = $quote[0]['module'];
            if ($quote[0]['methods'][0]['title']) {
              $title .= ' (' . $quote[0]['methods'][0]['title'] . ')';
            }
          }

          $_SESSION['shipping'] = [
            'id' => $_SESSION['shipping'],
            'title' => $title,
            'cost' => $quote[0]['methods'][0]['cost'],
          ];

          tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
        }
      }
    }

  }
