<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class Product extends product_builder {

    protected $_data = [];

    public function __construct($data = ['status' => 0]) {
      parent::__construct();

      foreach ($data as $key => $value) {
        $trimmed_key = Text::ltrim_once($key, 'products_');

        $this->_data[isset($data[$trimmed_key]) ? $key : $trimmed_key] = $value;
      }

      if (!isset($this->_data['final_price']) && isset($this->_data['base_price'])) {
        $this->_data['final_price'] = $this->_data['base_price'];
      }

      if (isset($this->_data['id']) && !isset($this->_data['link'])) {
        $this->_data['link'] = static::build_link((int)$this->_data['id']);
      }
    }

    public function can($key) {
      return $this->has($key) || parent::can($key);
    }

    public function has($key) {
      return isset($this->_data[$key]) || array_key_exists($this->_data, $key);
    }

    public function get($key) {
      if (!isset($this->_data[$key])) {
        if (parent::can($key)) {
          call_user_func(static::$capabilities[$key], $this);
        } else {
          return null;
        }
      }

      return $this->_data[$key];
    }

    public function set($key, $value) {
      $this->_data[$key] = $value;
    }

    public function get_data() {
      return $this->_data;
    }

    public function hype_price($show_special_price = true) {
      if ($show_special_price && ($this->get('is_special') == 1)) {
        return sprintf(
          IS_PRODUCT_SHOW_PRICE_SPECIAL,
          $this->format('price'),
          $this->format());
      }

      return sprintf(IS_PRODUCT_SHOW_PRICE, $this->format());
    }

    public function format($price = 'final_price', $quantity = 1) {
      return $GLOBALS['currencies']->display_price($this->get($price), $this->get('tax_rate'), $quantity);
    }

    public function format_raw($price = 'final_price', $quantity = 1) {
      return $GLOBALS['currencies']->display_raw($this->get($price), $this->get('tax_rate'), $quantity);
    }

    public function increment_view_count() {
      tep_db_query("UPDATE products_description SET products_viewed = products_viewed+1 WHERE products_id = " . (int)$this->get('id') . " AND language_id = " . (int)$_SESSION['languages_id']);
    }

    public function find_path() {
      return (($categories = $this->get('categories')) && isset($categories[0]))
           ? Guarantor::ensure_global('category_tree')->find_path($categories[0])
           : '';
    }

    public function lacks_stock($quantity = null) {
      return $this->get('in_stock') < ($quantity ?? $this->get('quantity'));
    }

  }
