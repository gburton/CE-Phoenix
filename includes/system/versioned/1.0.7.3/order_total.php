<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class order_total {

    public $modules;

// class constructor
    function __construct() {
      if (defined('MODULE_ORDER_TOTAL_INSTALLED') && tep_not_null(MODULE_ORDER_TOTAL_INSTALLED)) {
        $this->modules = explode(';', MODULE_ORDER_TOTAL_INSTALLED);

        foreach ($this->modules as $value) {
          $class = pathinfo($value, PATHINFO_FILENAME);
          $GLOBALS[$class] = new $class();
        }
      }
    }

    function process() {
      $order_total_array = [];
      if (is_array($this->modules)) {
        foreach ($this->modules as $value) {
          $class = pathinfo($value, PATHINFO_FILENAME);
          if ($GLOBALS[$class]->enabled) {
            $GLOBALS[$class]->output = [];
            $GLOBALS[$class]->process();

            foreach ($GLOBALS[$class]->output as $output) {
              if (tep_not_null($output['title']) && tep_not_null($output['text'])) {
                $order_total_array[] = [
                  'code' => $GLOBALS[$class]->code,
                  'title' => $output['title'],
                  'text' => $output['text'],
                  'value' => $output['value'],
                  'sort_order' => $GLOBALS[$class]->sort_order,
                ];
              }
            }
          }
        }
      }

      return $order_total_array;
    }

    function output() {
      $output_string = '';
      if (is_array($this->modules)) {
        foreach ($this->modules as $value) {
          $class = pathinfo($value, PATHINFO_FILENAME);
          if ($GLOBALS[$class]->enabled) {
            foreach ($GLOBALS[$class]->output as $output) {
              $output_string .= '<tr>';
              $output_string .= '<td>' . $output['title'] . '</td>';
              $output_string .= '<td class="text-right">' . $output['text'] . '</td>';
              $output_string .= '</tr>';
            }
          }
        }
      }

      return $output_string;
    }
  }
