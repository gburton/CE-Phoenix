<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class shoppingCart {

    const COLUMN_KEYS = [
      'name' => 'products_name',
      'model' => 'products_model',
      'image' => 'products_image',
      'weight' => 'products_weight',
      'tax_class_id' => 'products_tax_class_id',
    ];

    public $contents, $total, $weight, $cartID, $content_type;

    function __construct() {
      $this->reset();
    }

    function restore_contents() {
      if (!isset($_SESSION['customer_id'])) {
        return false;
      }

// insert current cart contents in database
      if (is_array($this->contents)) {
        foreach (array_keys($this->contents) as $products_id) {
          $qty = $this->contents[$products_id]['qty'];
          $product_query = tep_db_query("SELECT products_id FROM customers_basket WHERE customers_id = " . (int)$_SESSION['customer_id'] . " AND products_id = '" . tep_db_input($products_id) . "'");
          if (tep_db_num_rows($product_query)) {
            tep_db_query("UPDATE customers_basket SET customers_basket_quantity = '" . tep_db_input($qty) . "' WHERE customers_id = " . (int)$_SESSION['customer_id'] . " AND products_id = '" . tep_db_input($products_id) . "'");
          } else {
            tep_db_query("INSERT INTO customers_basket (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) VALUES (" . (int)$_SESSION['customer_id'] . ", '" . tep_db_input($products_id) . "', '" . tep_db_input($qty) . "', '" . date('Ymd') . "')");
            if (isset($this->contents[$products_id]['attributes'])) {
              foreach ($this->contents[$products_id]['attributes'] as $option => $value) {
                tep_db_query("INSERT INTO customers_basket_attributes (customers_id, products_id, products_options_id, products_options_value_id) VALUES (" . (int)$_SESSION['customer_id'] . ", '" . tep_db_input($products_id) . "', " . (int)$option . ", " . (int)$value . ")");
              }
            }
          }
        }
      }

// reset per-session cart contents, but not the database contents
      $this->reset(false);

      $products_query = tep_db_query("SELECT products_id, customers_basket_quantity FROM customers_basket WHERE customers_id = " . (int)$_SESSION['customer_id']);
      while ($products = tep_db_fetch_array($products_query)) {
        $this->contents[$products['products_id']] = ['qty' => $products['customers_basket_quantity']];
// attributes
        $attributes_query = tep_db_query("SELECT products_options_id, products_options_value_id FROM customers_basket_attributes WHERE customers_id = " . (int)$_SESSION['customer_id'] . " AND products_id = '" . tep_db_input($products['products_id']) . "'");
        while ($attributes = tep_db_fetch_array($attributes_query)) {
          $this->contents[$products['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
        }
      }

      $this->cleanup();

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

    function reset($reset_database = false) {
      $this->contents = [];
      $this->total = 0;
      $this->weight = 0;
      $this->content_type = false;

      if (isset($_SESSION['customer_id']) && $reset_database) {
        tep_db_query("DELETE FROM customers_basket WHERE customers_id = " . (int)$_SESSION['customer_id']);
        tep_db_query("DELETE FROM customers_basket_attributes WHERE customers_id = " . (int)$_SESSION['customer_id']);
      }

      unset($this->cartID);
      unset($_SESSION['cartID']);
    }

    function add_cart($products_id, $qty = 1, $attributes = null, $notify = true) {
      $products_id_string = tep_get_uprid($products_id, $attributes);
      $products_id = tep_get_prid($products_id_string);

      if (defined('MAX_QTY_IN_CART') && (MAX_QTY_IN_CART > 0) && ((int)$qty > MAX_QTY_IN_CART)) {
        $qty = MAX_QTY_IN_CART;
      }

      if (!empty($attributes) && is_array($attributes)) {
        foreach ($attributes as $option => $value) {
          if (!is_numeric($option) || !is_numeric($value)) {
            return;
          }

          $check_query = tep_db_query("SELECT products_attributes_id FROM products_attributes WHERE products_id = " . (int)$products_id . " AND options_id = " . (int)$option . " AND options_values_id = " . (int)$value . " LIMIT 1");
          if (tep_db_num_rows($check_query) < 1) {
            return;
          }
        }
      } elseif (tep_has_product_attributes($products_id)) {
        return;
      }

      if (is_numeric($products_id) && is_numeric($qty)) {
        $check_product_query = tep_db_query("SELECT products_status FROM products WHERE products_id = " . (int)$products_id);
        $check_product = tep_db_fetch_array($check_product_query);

        if (($check_product !== false) && ($check_product['products_status'] == '1')) {
          if ($notify) {
            $_SESSION['new_products_id_in_cart'] = $products_id;
          }

          if ($this->in_cart($products_id_string)) {
            $this->update_quantity($products_id_string, $qty, $attributes);
          } else {
            $this->contents[$products_id_string] = ['qty' => (int)$qty];

            if (isset($_SESSION['customer_id'])) {
              tep_db_query("INSERT INTO customers_basket (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) VALUES (" . (int)$_SESSION['customer_id'] . ", '" . tep_db_input($products_id_string) . "', " . (int)$qty . ", '" . date('Ymd') . "')");
            }

            if (is_array($attributes)) {
              foreach ($attributes as $option => $value) {
                $this->contents[$products_id_string]['attributes'][$option] = $value;

                if (isset($_SESSION['customer_id'])) {
                  tep_db_query("INSERT INTO customers_basket_attributes (customers_id, products_id, products_options_id, products_options_value_id) VALUES (" . (int)$_SESSION['customer_id'] . ", '" . tep_db_input($products_id_string) . "', " . (int)$option . ", " . (int)$value . ")");
                }
              }
            }
          }

          $this->cleanup();

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
          $this->cartID = $this->generate_cart_id();
        }
      }
    }

    function update_quantity($products_id, $quantity, $attributes = null) {
      $products_id_string = tep_get_uprid($products_id, $attributes);
      $products_id = tep_get_prid($products_id_string);

      if (defined('MAX_QTY_IN_CART') && (MAX_QTY_IN_CART > 0) && ((int)$quantity > MAX_QTY_IN_CART)) {
        $quantity = MAX_QTY_IN_CART;
      }

      foreach (($attributes ?? []) as $option => $value) {
        if (!is_numeric($option) || !is_numeric($value)) {
          return;
        }
      }

      if (is_numeric($products_id) && isset($this->contents[$products_id_string]) && is_numeric($quantity)) {
        $this->contents[$products_id_string] = ['qty' => (int)$quantity];

        if (isset($_SESSION['customer_id'])) {
          tep_db_query(sprintf(<<<'EOSQL'
UPDATE customers_basket
 SET customers_basket_quantity = %d
 WHERE customers_id = %d AND products_id = '%s'
EOSQL
            , (int)$quantity, (int)$_SESSION['customer_id'], tep_db_input($products_id_string)));
        }

        foreach (($attributes ?? []) as $option => $value) {
          $this->contents[$products_id_string]['attributes'][$option] = $value;

          if (isset($_SESSION['customer_id'])) {
            tep_db_query(sprintf(<<<'EOSQL'
UPDATE customers_basket_attributes
 SET products_options_value_id = %d
 WHERE customers_id = %d AND products_id = '%s' AND products_options_id = %d
EOSQL
              , (int)$value, (int)$_SESSION['customer_id'], tep_db_input($products_id_string), (int)$option));
          }
        }

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
        $this->cartID = $this->generate_cart_id();
      }
    }

    function cleanup() {
      foreach (array_keys($this->contents) as $product_id) {
        if ($this->contents[$product_id]['qty'] < 1) {
          unset($this->contents[$product_id]);

          if (isset($_SESSION['customer_id'])) {
            tep_db_query("DELETE FROM customers_basket WHERE products_id = '" . tep_db_input($product_id) . "' AND customers_id = " . (int)$_SESSION['customer_id']);
            tep_db_query("DELETE FROM customers_basket_attributes WHERE products_id = '" . tep_db_input($product_id) . "' AND customers_id = " . (int)$_SESSION['customer_id']);
          }
        }
      }
    }

// get total number of items in cart
    function count_contents() {
      return array_sum(array_column($this->contents, 'qty'));
    }

    function get_quantity($products_id) {
      return $this->contents[$products_id]['qty'] ?? 0;
    }

    function in_cart($products_id) {
      return isset($this->contents[$products_id]);
    }

    function remove($products_id) {
      unset($this->contents[$products_id]);
// remove from database
      if (isset($_SESSION['customer_id'])) {
        tep_db_query("DELETE FROM customers_basket WHERE customers_id = " . (int)$_SESSION['customer_id'] . " AND products_id = '" . tep_db_input($products_id) . "'");
        tep_db_query("DELETE FROM customers_basket_attributes WHERE customers_id = " . (int)$_SESSION['customer_id'] . " AND products_id = '" . tep_db_input($products_id) . "'");
      }

      $this->calculate();

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

    function remove_all() {
      $this->reset();
    }

    function get_product_id_list() {
      return implode(', ', array_keys($this->contents));
    }

    function calculate() {
      global $currencies;

      $this->total = 0;
      $this->weight = 0;

      foreach (array_keys($this->contents) as $products_id) {
        $qty = $this->contents[$products_id]['qty'];

// products price
        $product_query = tep_db_query("SELECT products_id, products_price, products_tax_class_id, products_weight FROM products WHERE products_id = " . (int)$products_id);
        if ($product = tep_db_fetch_array($product_query)) {
          $products_tax = tep_get_tax_rate($product['products_tax_class_id']);

          $specials_query = tep_db_query("SELECT specials_new_products_price FROM specials WHERE status = 1 AND products_id = " . (int)$products_id);
          $specials = tep_db_fetch_array($specials_query);
          $products_price = $specials['specials_new_products_price'] ?? $product['products_price'];

          $this->total += $currencies->calculate_price($products_price, $products_tax, $qty);
          $this->weight += ($qty * $product['products_weight']);
        }

// attributes price
        if (!empty($this->contents[$products_id]['attributes'])) {
          $this->total += $currencies->calculate_price($this->attributes_price($products_id), $products_tax, $qty);
        }
      }
    }

    function attributes_price($products_id) {
      $attributes_price = 0;

      foreach (($this->contents[$products_id]['attributes'] ?? []) as $option => $value) {
        $attribute_price_query = tep_db_query("SELECT options_values_price, price_prefix FROM products_attributes WHERE products_id = " . (int)$products_id . " AND options_id = " . (int)$option . " AND options_values_id = " . (int)$value);
        $attribute_price = tep_db_fetch_array($attribute_price_query);
        if ($attribute_price['price_prefix'] == '+') {
          $attributes_price += $attribute_price['options_values_price'];
        } else {
          $attributes_price -= $attribute_price['options_values_price'];
        }
      }

      return $attributes_price;
    }

    private function map_columns($data) {
      static $column_keys = null;
      if (is_null($column_keys)) {
        $column_keys = static::COLUMN_KEYS;
        $parameters = ['column_keys' => &$column_keys];
        $GLOBALS['OSCOM_Hooks']->call('siteWide', 'cartProductColumns', $parameters);
      }

      $product = [];
      foreach ($column_keys as $key => $column_name) {
        $product[$key] = $data[$column_name];
      }

      return $product;
    }

    function get_products() {
      $products = [];
      foreach (array_keys($this->contents) as $products_id) {
        $products_query = tep_db_query("SELECT p.*, pd.* FROM products p INNER JOIN products_description pd ON pd.products_id = p.products_id WHERE p.products_id = " . (int)$products_id . " AND pd.language_id = " . (int)$_SESSION['languages_id']);
        if ($product = tep_db_fetch_array($products_query)) {
          $prid = $product['products_id'];
          $product_price = $product['products_price'];

          $specials_query = tep_db_query("SELECT specials_new_products_price FROM specials WHERE status = 1 AND products_id = " . (int)$prid);
          if ($specials = tep_db_fetch_array($specials_query)) {
            $product_price = $specials['specials_new_products_price'];
          }

          $product = $this->map_columns($product);

          $product['id'] = $products_id;
          $product['price'] = $product_price;
          $product['quantity'] = $this->contents[$products_id]['qty'];
          $product['final_price'] = ($product_price + $this->attributes_price($products_id));
          $product['attributes'] = ($this->contents[$products_id]['attributes'] ?? null);

          $products[] = $product;
        }
      }

      return $products;
    }

    function show_total() {
      $this->calculate();

      return $this->total;
    }

    function show_weight() {
      $this->calculate();

      return $this->weight;
    }

    function generate_cart_id($length = 5) {
      return tep_create_random_value($length, 'digits');
    }

    function get_content_type() {
      if ( (DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0) ) {
        $this->content_type = false;

        foreach (array_keys($this->contents) as $products_id) {
          if (isset($this->contents[$products_id]['attributes'])) {
            foreach (($this->contents[$products_id]['attributes'] ?? []) as $option => $value) {
              $virtual_check_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT COUNT(*) AS total
 FROM products_attributes pa INNER JOIN products_attributes_download pad ON pa.products_attributes_id = pad.products_attributes_id
 WHERE pa.products_id = %d AND pa.options_values_id = %d
EOSQL
                , (int)$products_id, (int)$value));
              $virtual_check = tep_db_fetch_array($virtual_check_query);

              if ($virtual_check['total'] > 0) {
                switch ($this->content_type) {
                  case 'physical':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                  default:
                    $this->content_type = 'virtual';
                }
              } else {
                switch ($this->content_type) {
                  case 'virtual':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                  default:
                    $this->content_type = 'physical';
                }
              }
            }
          } else {
            switch ($this->content_type) {
              case 'virtual':
                $this->content_type = 'mixed';

                return $this->content_type;
              default:
                $this->content_type = 'physical';
            }
          }
        }
      } else {
        $this->content_type = 'physical';
      }

      return $this->content_type;
    }

  }
