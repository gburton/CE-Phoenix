<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

////
// Get the installed version number
  function tep_get_version() {
    static $v;

    if (!isset($v)) {
      $v = trim(implode('', file(DIR_FS_CATALOG . 'includes/version.php')));
    }

    return $v;
  }

////
// Redirect to another page or site
  function tep_redirect($url) {
    if ( (strstr($url, "\n") != false) || (strstr($url, "\r") != false) ) {
      tep_redirect(tep_href_link('index.php', '', 'NONSSL', false));
    }

    if ( ENABLE_SSL && ('on' === getenv('HTTPS')) ) {
      // if this is an SSL page, we can't redirect to a non-SSL page
      // so substitute the SSL URL instead
      $http_base = HTTP_SERVER . DIR_WS_HTTP_CATALOG;
      $http_length = strlen($http_base);
      if (substr($url, 0, $http_length) === $http_base) {
        $url = HTTPS_SERVER . DIR_WS_HTTPS_CATALOG . substr($url, $http_length);
      }
    }

    if ( strpos($url, '&amp;') !== false ) {
      $url = str_replace('&amp;', '&', $url);
    }

    header('Location: ' . $url);


    exit;
  }

////
// Parse the data used in the html tags to ensure the tags will not break
  function tep_parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
  }

  function tep_output_string($string, $translate = false, $protected = false) {
    if ($protected) {
      return htmlspecialchars($string);
    }

    if (!$translate) {
      $translate = ['"' => '&quot;'];
    }

    return tep_parse_input_field_data($string, $translate);
  }

  function tep_output_string_protected($string) {
    return htmlspecialchars($string);
  }

  function tep_sanitize_string($string) {
    $patterns = ['/ +/', '/[<>]/'];
    $replace = [' ', '_'];
    return preg_replace($patterns, $replace, trim($string));
  }

////
// Return a random row from a database query
  function tep_random_select($query) {
    $random_product = '';
    $random_query = tep_db_query($query);
    $num_rows = tep_db_num_rows($random_query);
    if ($num_rows > 0) {
      $random_row = tep_rand(0, ($num_rows - 1));
      tep_db_data_seek($random_query, $random_row);
      $random_product = tep_db_fetch_array($random_query);
    }

    return $random_product;
  }

////
// Return a product's name
// TABLES: products
  function tep_get_products_name($product_id, $language_id = null) {
    if (empty($language_id)) $language_id = $_SESSION['languages_id'];

    $product_query = tep_db_query("SELECT products_name FROM products_description WHERE products_id = " . (int)$product_id . " AND language_id = " . (int)$language_id);
    $product = tep_db_fetch_array($product_query);

    return $product['products_name'];
  }

////
// Return a product's special price (returns nothing if there is no offer)
// TABLES: products
  function tep_get_products_special_price($product_id) {
    $product_query = tep_db_query("SELECT specials_new_products_price FROM specials WHERE products_id = " . (int)$product_id . " AND status = 1");
    $product = tep_db_fetch_array($product_query);

    return $product['specials_new_products_price'] ?? null;
  }

////
// Return a product's stock
// TABLES: products
  function tep_get_products_stock($products_id) {
    $products_id = tep_get_prid($products_id);
    $stock_query = tep_db_query("SELECT products_quantity FROM products WHERE products_id = " . (int)$products_id);
    $stock_values = tep_db_fetch_array($stock_query);

    return $stock_values['products_quantity'];
  }

////
// Check if the required stock is available
// If insufficent stock is available return an out of stock message
  function tep_check_stock($products_id, $products_quantity) {
    return tep_get_products_stock($products_id) < $products_quantity;
  }

////
// Return all HTTP GET variables, except those passed as a parameter
  function tep_get_all_get_params($excludes = []) {
    $excludes += [ session_name(), 'error', 'x', 'y' ];

    $get_url = '';
    foreach ($_GET ?? [] as $key => $value) {
      if ( is_string($value) && (strlen($value) > 0) && !in_array($key, $excludes) ) {
        $get_url .= $key . '=' . rawurlencode(stripslashes($value)) . '&';
      }
    }

    return $get_url;
  }

////
// Returns an array with countries
// TABLES: countries
  function tep_get_countries($countries_id = '', $with_iso_codes = false) {
    $countries_array = [];
    if (tep_not_null($countries_id)) {
      if ($with_iso_codes == true) {
        $countries = tep_db_query("SELECT countries_name, countries_iso_code_2, countries_iso_code_3 FROM countries WHERE countries_id = " . (int)$countries_id . " ORDER BY countries_name");
        $countries_values = tep_db_fetch_array($countries);
        $countries_array = [
          'countries_name' => $countries_values['countries_name'],
          'countries_iso_code_2' => $countries_values['countries_iso_code_2'],
          'countries_iso_code_3' => $countries_values['countries_iso_code_3'],
        ];
      } else {
        $countries = tep_db_query("SELECT countries_name FROM countries WHERE countries_id = " . (int)$countries_id);
        $countries_values = tep_db_fetch_array($countries);
        $countries_array = ['countries_name' => $countries_values['countries_name']];
      }
    } else {
      $countries = tep_db_query("SELECT countries_id, countries_name FROM countries ORDER BY countries_name");
      while ($countries_values = tep_db_fetch_array($countries)) {
        $countries_array[] = [
          'countries_id' => $countries_values['countries_id'],
          'countries_name' => $countries_values['countries_name'],
        ];
      }
    }

    return $countries_array;
  }

////
// Generate a path to categories
  function tep_get_path($current_category_id = '') {
    global $cPath_array;

    if (tep_not_null($current_category_id)) {
      $cp_size = count($cPath_array);
      if ($cp_size == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
        $last_category_query = tep_db_query("SELECT parent_id FROM categories WHERE categories_id = " . (int)$cPath_array[($cp_size-1)]);
        $last_category = tep_db_fetch_array($last_category_query);

        $current_category_query = tep_db_query("SELECT parent_id FROM categories WHERE categories_id = " . (int)$current_category_id);
        $current_category = tep_db_fetch_array($current_category_query);

        if ($last_category['parent_id'] == $current_category['parent_id']) {
          for ($i=0; $i<($cp_size-1); $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        } else {
          for ($i=0; $i<$cp_size; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        }
        $cPath_new .= '_' . $current_category_id;

        if (substr($cPath_new, 0, 1) == '_') {
          $cPath_new = substr($cPath_new, 1);
        }
      }
    } else {
      $cPath_new = implode('_', (array)$cPath_array);
    }

    return 'cPath=' . $cPath_new;
  }

////
// Alias function to tep_get_countries()
  function tep_get_country_name($country_id) {
    $country_array = tep_get_countries($country_id);

    return $country_array['countries_name'];
  }

////
// Returns the zone (State/Province) name
// TABLES: zones
  function tep_get_zone_name($country_id, $zone_id, $default_zone) {
    $zone_query = tep_db_query("SELECT zone_name FROM zones WHERE zone_country_id = " . (int)$country_id . " AND zone_id = " . (int)$zone_id);
    if (tep_db_num_rows($zone_query)) {
      $zone = tep_db_fetch_array($zone_query);
      return $zone['zone_name'];
    } else {
      return $default_zone;
    }
  }

////
// Returns the zone (State/Province) code
// TABLES: zones
  function tep_get_zone_code($country_id, $zone_id, $default_zone) {
    $zone_query = tep_db_query("SELECT zone_code FROM zones WHERE zone_country_id = " . (int)$country_id . " AND zone_id = " . (int)$zone_id);
    $zone = tep_db_fetch_array($zone_query);

    return $zone ? $zone['zone_code'] : $default_zone;
  }

////
// Wrapper function for round()
  function tep_round($number, $precision) {
    if (strpos($number, '.') && (strlen(substr($number, strpos($number, '.')+1)) > $precision)) {
      $number = substr($number, 0, strpos($number, '.') + 1 + $precision + 1);

      if (substr($number, -1) >= 5) {
        if ($precision > 1) {
          $number = substr($number, 0, -1) + ('0.' . str_repeat(0, $precision-1) . '1');
        } elseif ($precision == 1) {
          $number = substr($number, 0, -1) + 0.1;
        } else {
          $number = substr($number, 0, -1) + 1;
        }
      } else {
        $number = substr($number, 0, -1);
      }
    }

    return $number;
  }

////
// Returns the tax rate for a zone / class
// TABLES: tax_rates, zones_to_geo_zones
  function tep_get_tax_rate($class_id, $country_id = -1, $zone_id = -1) {
    static $tax_rates = [];

    if ( ($country_id == -1) && ($zone_id == -1) ) {
      global $customer;

      if (isset($customer) && is_object($customer) && is_a($customer, 'customer')) {
        $country_id = $customer->get_country_id();
        $zone_id = $customer->get_zone_id();
      } else {
        $country_id = STORE_COUNTRY;
        $zone_id = STORE_ZONE;
      }
    }

    if (!isset($tax_rates[$class_id][$country_id][$zone_id]['rate'])) {
      $tax_query = tep_db_query("SELECT sum(tax_rate) AS tax_rate FROM tax_rates tr LEFT JOIN zones_to_geo_zones za ON (tr.tax_zone_id = za.geo_zone_id) LEFT JOIN geo_zones tz ON (tz.geo_zone_id = tr.tax_zone_id) WHERE (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = " . (int)$country_id . ") AND (za.zone_id is null or za.zone_id = '0' or za.zone_id = " . (int)$zone_id . ") AND tr.tax_class_id = " . (int)$class_id . " group by tr.tax_priority");
      if (tep_db_num_rows($tax_query)) {
        $tax_multiplier = 1.0;
        while ($tax = tep_db_fetch_array($tax_query)) {
          $tax_multiplier *= 1.0 + ($tax['tax_rate'] / 100);
        }

        $tax_rates[$class_id][$country_id][$zone_id]['rate'] = ($tax_multiplier - 1.0) * 100;
      } else {
        $tax_rates[$class_id][$country_id][$zone_id]['rate'] = 0;
      }
    }

    return $tax_rates[$class_id][$country_id][$zone_id]['rate'];
  }

////
// Return the tax description for a zone / class
// TABLES: tax_rates;
  function tep_get_tax_description($class_id, $country_id, $zone_id) {
    static $tax_rates = [];

    if (!isset($tax_rates[$class_id][$country_id][$zone_id]['description'])) {
      $tax_query = tep_db_query("SELECT tax_description FROM tax_rates tr LEFT JOIN zones_to_geo_zones za ON (tr.tax_zone_id = za.geo_zone_id) LEFT JOIN geo_zones tz ON (tz.geo_zone_id = tr.tax_zone_id) WHERE (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = " . (int)$country_id . ") AND (za.zone_id is null or za.zone_id = '0' or za.zone_id = " . (int)$zone_id . ") AND tr.tax_class_id = " . (int)$class_id . " ORDER BY tr.tax_priority");
      if (tep_db_num_rows($tax_query)) {
        $tax_description = '';
        while ($tax = tep_db_fetch_array($tax_query)) {
          $tax_description .= $tax['tax_description'] . ' + ';
        }
        $tax_description = substr($tax_description, 0, -3);

        $tax_rates[$class_id][$country_id][$zone_id]['description'] = $tax_description;
      } else {
        $tax_rates[$class_id][$country_id][$zone_id]['description'] = TEXT_UNKNOWN_TAX_RATE;
      }
    }

    return $tax_rates[$class_id][$country_id][$zone_id]['description'];
  }

////
// Add tax to a products price
  function tep_add_tax($price, $tax) {
    if ( (DISPLAY_PRICE_WITH_TAX == 'true') && ($tax > 0) ) {
      return $price + tep_calculate_tax($price, $tax);
    } else {
      return $price;
    }
  }

// Calculates Tax rounding the result
  function tep_calculate_tax($price, $tax) {
    return $price * $tax / 100;
  }

  function tep_get_categories($categories_array = '', $parent_id = '0', $indent = '') {
    if (!is_array($categories_array)) $categories_array = [];

    $categories_query = tep_db_query("SELECT c.categories_id, cd.categories_name FROM categories c, categories_description cd WHERE parent_id = " . (int)$parent_id . " AND c.categories_id = cd.categories_id AND cd.language_id = " . (int)$_SESSION['languages_id'] . " ORDER BY sort_order, cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      $categories_array[] = [
        'id' => $categories['categories_id'],
        'text' => $indent . $categories['categories_name'],
      ];

      if ($categories['categories_id'] != $parent_id) {
        $categories_array = tep_get_categories($categories_array, $categories['categories_id'], $indent . '&nbsp;&nbsp;');
      }
    }

    return $categories_array;
  }

  function tep_get_manufacturers($manufacturers = []) {
    $manufacturers_query = tep_db_query("SELECT manufacturers_id, manufacturers_name FROM manufacturers ORDER BY manufacturers_name");
    while ($manufacturer = tep_db_fetch_array($manufacturers_query)) {
      $manufacturers[] = ['id' => $manufacturer['manufacturers_id'], 'text' => $manufacturer['manufacturers_name']];
    }

    return $manufacturers;
  }

////
// Return all subcategory IDs
// TABLES: categories
  function tep_get_subcategories(&$subcategories_array, $parent_id = 0) {
    $subcategories_query = tep_db_query("SELECT categories_id FROM categories WHERE parent_id = " . (int)$parent_id);
    while ($subcategories = tep_db_fetch_array($subcategories_query)) {
      $subcategories_array[] = $subcategories['categories_id'];
      if ($subcategories['categories_id'] != $parent_id) {
        tep_get_subcategories($subcategories_array, $subcategories['categories_id']);
      }
    }
  }

// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
  function tep_date_long($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = (int)substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    return strftime(DATE_FORMAT_LONG, mktime($hour,$minute,$second,$month,$day,$year));
  }

////
// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
// NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
  function tep_date_short($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || empty($raw_date) ) return false;

    $year = substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
      return date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
    } else {
      return preg_replace('/2037$/', $year, date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, 2037)));
    }
  }

////
// Parse search string into indivual objects
  function tep_parse_search_string($search_str = '', &$objects) {
    $search_str = trim(strtolower($search_str));

// Break up $search_str on whitespace; quoted string will be reconstructed later
    $pieces = preg_split('/[[:space:]]+/', $search_str);
    $objects = [];
    $tmpstring = '';
    $flag = '';

    for ($k=0; $k<count($pieces); $k++) {
      while (substr($pieces[$k], 0, 1) == '(') {
        $objects[] = '(';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 1);
        } else {
          $pieces[$k] = '';
        }
      }

      $post_objects = [];

      while (substr($pieces[$k], -1) == ')')  {
        $post_objects[] = ')';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 0, -1);
        } else {
          $pieces[$k] = '';
        }
      }

// Check individual words

      if ( (substr($pieces[$k], -1) != '"') && (substr($pieces[$k], 0, 1) != '"') ) {
        $objects[] = trim($pieces[$k]);

        for ($j=0; $j<count($post_objects); $j++) {
          $objects[] = $post_objects[$j];
        }
      } else {
/* This means that the $piece is either the beginning or the end of a string.
   So, we'll slurp up the $pieces and stick them together until we get to the
   end of the string or run out of pieces.
*/

// Add this word to the $tmpstring, starting the $tmpstring
        $tmpstring = trim(preg_replace('/"/', ' ', $pieces[$k]));

// Check for one possible exception to the rule. That there is a single quoted word.
        if (substr($pieces[$k], -1 ) == '"') {
// Turn the flag off for future iterations
          $flag = 'off';

          $objects[] = trim(preg_replace('/"/', ' ', $pieces[$k]));

          for ($j=0; $j<count($post_objects); $j++) {
            $objects[] = $post_objects[$j];
          }

          unset($tmpstring);

// Stop looking for the end of the string and move onto the next word.
          continue;
        }

// Otherwise, turn on the flag to indicate no quotes have been found attached to this word in the string.
        $flag = 'on';

// Move on to the next word
        $k++;

// Keep reading until the end of the string as long as the $flag is on

        while ( ($flag == 'on') && ($k < count($pieces)) ) {
          while (substr($pieces[$k], -1) == ')') {
            $post_objects[] = ')';
            if (strlen($pieces[$k]) > 1) {
              $pieces[$k] = substr($pieces[$k], 0, -1);
            } else {
              $pieces[$k] = '';
            }
          }

// If the word doesn't end in double quotes, append it to the $tmpstring.
          if (substr($pieces[$k], -1) != '"') {
// Tack this word onto the current string entity
            $tmpstring .= ' ' . $pieces[$k];

// Move on to the next word
            $k++;
            continue;
          } else {
/* If the $piece ends in double quotes, strip the double quotes, tack the
   $piece onto the tail of the string, push the $tmpstring onto the $haves,
   kill the $tmpstring, turn the $flag "off", and return.
*/
            $tmpstring .= ' ' . trim(str_replace('"', ' ', $pieces[$k]));

// Push the $tmpstring onto the array of stuff to search for
            $objects[] = trim($tmpstring);

            for ($j=0; $j<count($post_objects); $j++) {
              $objects[] = $post_objects[$j];
            }

            unset($tmpstring);

// Turn off the flag to exit the loop
            $flag = 'off';
          }
        }
      }
    }

// add default logical operators if needed
    $temp = [];
    for($i=0; $i<(count($objects)-1); $i++) {
      $temp[] = $objects[$i];
      if ( ($objects[$i] != 'and')
        && ($objects[$i] != 'or')
        && ($objects[$i] != '(')
        && ($objects[$i+1] != 'and')
        && ($objects[$i+1] != 'or')
        && ($objects[$i+1] != ')') )
      {
        $temp[] = ADVANCED_SEARCH_DEFAULT_OPERATOR;
      }
    }
    $temp[] = $objects[$i];
    $objects = $temp;

    $keyword_count = 0;
    $operator_count = 0;
    $balance = 0;
    foreach ($objects as $object) {
      if ('(' === $object) {
        $balance--;
      } else if (')' === $object) {
        $balance++;
      } else if ( ('and' === $object) || ('or' === $object) ) {
        $operator_count++;
      } elseif ($object) {
        $keyword_count++;
      }
    }

    return ( ($operator_count < $keyword_count) && ($balance == 0) );
  }

////
// Return table heading with sorting capabilities
  function tep_create_sort_heading($sortby, $colnum, $heading) {
    global $PHP_SELF;

    $sort_prefix = '';
    $sort_suffix = '';

    if ($sortby) {
	  $sort_prefix = '<a href="' . tep_href_link($PHP_SELF, tep_get_all_get_params(['info', 'sort', 'page']) . 'sort=' . $colnum . ($sortby == $colnum . 'a' ? 'd' : 'a')) . '" title="' . tep_output_string(TEXT_SORT_PRODUCTS . ($sortby == $colnum . 'd' || substr($sortby, 0, 1) != $colnum ? TEXT_ASCENDINGLY : TEXT_DESCENDINGLY) . TEXT_BY . $heading) . '" class="dropdown-item">' ;
      $sort_suffix = (substr($sortby, 0, 1) == $colnum ? (substr($sortby, 1, 1) == 'a' ? LISTING_SORT_DOWN : LISTING_SORT_UP) : LISTING_SORT_UNSELECTED) . '</a>';
    }

    return $sort_prefix . $heading . $sort_suffix;
  }

////
// Recursively go through the categories and retrieve all parent categories IDs
// TABLES: categories
  function tep_get_parent_categories(&$categories, $categories_id) {
    $parent_categories_query = tep_db_query("SELECT parent_id FROM categories WHERE categories_id = " . (int)$categories_id);
    while ($parent_categories = tep_db_fetch_array($parent_categories_query)) {
      if ($parent_categories['parent_id'] == 0) return true;
      $categories[count($categories)] = $parent_categories['parent_id'];
      if ($parent_categories['parent_id'] != $categories_id) {
        tep_get_parent_categories($categories, $parent_categories['parent_id']);
      }
    }
  }

////
// Construct a category path to the product
// TABLES: products_to_categories
  function tep_get_product_path($products_id) {
    $cPath = '';

    $category_query = tep_db_query("SELECT p2c.categories_id FROM products p, products_to_categories p2c WHERE p.products_id = " . (int)$products_id . " AND p.products_status = 1 AND p.products_id = p2c.products_id LIMIT 1");
    if (tep_db_num_rows($category_query)) {
      $category = tep_db_fetch_array($category_query);

      $categories = [];
      tep_get_parent_categories($categories, $category['categories_id']);

      $categories = array_reverse($categories);

      $cPath = implode('_', $categories);

      if (tep_not_null($cPath)) $cPath .= '_';
      $cPath .= $category['categories_id'];
    }

    return $cPath;
  }

////
// Return a product ID with attributes
  function tep_get_uprid($prid, $params) {
    if (is_numeric($prid)) {
      $uprid = (int)$prid;

      if (is_array($params) && (count($params) > 0)) {
        $attributes_check = true;
        $attributes_ids = '';

        foreach($params as $option => $value) {
          if (is_numeric($option) && is_numeric($value)) {
            $attributes_ids .= '{' . (int)$option . '}' . (int)$value;
          } else {
            $attributes_check = false;
            break;
          }
        }

        if ($attributes_check == true) {
          $uprid .= $attributes_ids;
        }
      }
    } else {
      $uprid = tep_get_prid($prid);

      if (is_numeric($uprid)) {
        if (strpos($prid, '{') !== false) {
          $attributes_check = true;
          $attributes_ids = '';

// strpos()+1 to remove up to and including the first { which would create an empty array element in explode()
          $attributes = explode('{', substr($prid, strpos($prid, '{')+1));

          foreach ($attributes as $attribute) {
            $pair = explode('}', $attribute);

            if (is_numeric($pair[0]) && is_numeric($pair[1])) {
              $attributes_ids .= '{' . (int)$pair[0] . '}' . (int)$pair[1];
            } else {
              $attributes_check = false;
              break;
            }
          }

          if ($attributes_check == true) {
            $uprid .= $attributes_ids;
          }
        }
      } else {
        return false;
      }
    }

    return $uprid;
  }

////
// Return a product ID from a product ID with attributes
  function tep_get_prid($uprid) {
    $pieces = explode('{', $uprid);

    if (is_numeric($pieces[0])) {
      return (int)$pieces[0];
    } else {
      return false;
    }
  }

////
//! Send email (text/html) using MIME
// This is the central mail function. The SMTP Server should be configured
// correct in php.ini
// Parameters:
// $to_name           The name of the recipient, e.g. "Jan Wildeboer"
// $to_email_address  The eMail address of the recipient,
//                    e.g. jan.wildeboer@gmx.de
// $email_subject     The subject of the eMail
// $email_text        The text of the eMail, may contain HTML entities
// $from_email_name   The name of the sender, e.g. Shop Administration
// $from_email_adress The eMail address of the sender,
//                    e.g. info@mytepshop.com
  function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) {
    if (SEND_EMAILS != 'true') {
      return false;
    }

    // Instantiate a new mail object
    $message = new email();
    $message->add_message($email_text);
    $message->build_message();
    $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
  }

  function tep_notify($trigger, $subject) {
    if (defined('MODULE_NOTIFICATIONS_INSTALLED') && tep_not_null(MODULE_NOTIFICATIONS_INSTALLED)) {
      foreach ((array)explode(';', MODULE_NOTIFICATIONS_INSTALLED) as $basename) {
        $class = pathinfo($basename, PATHINFO_FILENAME);

        if (!isset($GLOBALS[$class])) {
          $GLOBALS[$class] = new $class();
        }

        if (!$GLOBALS[$class]->isEnabled()) {
          continue;
        }

        if (in_array($trigger, $class::TRIGGERS)) {
          $GLOBALS[$class]->notify($subject);
        }
      }
    }
  }

////
// Check if product has attributes
  function tep_has_product_attributes($products_id) {
    $attributes_query = tep_db_query("SELECT COUNT(*) AS count FROM products_attributes WHERE products_id = " . (int)$products_id);
    $attributes = tep_db_fetch_array($attributes_query);

    return $attributes['count'] > 0;
  }

  function tep_count_modules($modules = '') {
    if (empty($modules)) {
      return 0;
    }

    $count = 0;
    foreach (explode(';', $modules) as $module) {
      $class = pathinfo($module, PATHINFO_FILENAME);

      if (isset($GLOBALS[$class]) && $GLOBALS[$class] instanceof $class && $GLOBALS[$class]->enabled) {
        $count++;
      }
    }

    return $count;
  }

  function tep_count_payment_modules() {
    return tep_count_modules(MODULE_PAYMENT_INSTALLED);
  }

  function tep_count_shipping_modules() {
    return tep_count_modules(MODULE_SHIPPING_INSTALLED);
  }

  function tep_create_random_value($length, $type = 'mixed') {
    if ( !in_array($type, ['mixed', 'chars', 'digits']) ) {
      $type = 'mixed';
    }

    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $digits = '0123456789';

    $base = '';

    if ( ($type == 'mixed') || ($type == 'chars') ) {
      $base .= $chars;
    }

    if ( ($type == 'mixed') || ($type == 'digits') ) {
      $base .= $digits;
    }

    $value = '';

    $hasher = new PasswordHash(10, true);

    do {
      $random = base64_encode($hasher->get_random_bytes($length));

      for ($i = 0, $n = strlen($random); $i < $n; $i++) {
        $char = substr($random, $i, 1);

        if ( strpos($base, $char) !== false ) {
          $value .= $char;
        }
      }
    } while ( strlen($value) < $length );

    if ( strlen($value) > $length ) {
      $value = substr($value, 0, $length);
    }

    return $value;
  }

  function tep_array_to_string($array, $excludes = [], $equals = '=', $separator = '&') {
    $get_string = '';
    foreach ($array as $key => $value) {
      if ( (!in_array($key, $excludes)) && ($key != 'x') && ($key != 'y') ) {
        $get_string .= $key . $equals . $value . $separator;
      }
    }

    $displacement = -strlen($separator);
    if (substr($get_string, $displacement) === $separator) {
      $get_string = substr($get_string, 0, $displacement);
    }

    return $get_string;
  }

  function tep_not_null($value) {
    if (is_array($value)) {
      return count($value) > 0;
    }

    return (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0));
  }

////
// Output the tax percentage with optional padded decimals
  function tep_display_tax_value($value, $padding = TAX_DECIMAL_PLACES) {
    if (strpos($value, '.')) {
      $loop = true;
      while ($loop) {
        if (substr($value, -1) == '0') {
          $value = substr($value, 0, -1);
        } else {
          $loop = false;
          if (substr($value, -1) == '.') {
            $value = substr($value, 0, -1);
          }
        }
      }
    }

    if ($padding > 0) {
      if ($decimal_pos = strpos($value, '.')) {
        $decimals = strlen(substr($value, ($decimal_pos+1)));
        for ($i=$decimals; $i<$padding; $i++) {
          $value .= '0';
        }
      } else {
        $value .= '.';
        for ($i=0; $i<$padding; $i++) {
          $value .= '0';
        }
      }
    }

    return $value;
  }

////
// Parse and secure the cPath parameter values
  function tep_parse_category_path($cPath) {
// make sure the category IDs are integers
// make sure no duplicate category IDs exist which could lock the server in a loop
    return array_unique(array_map(function ($s) { return (int)$s; }, explode('_', $cPath)), SORT_NUMERIC);
  }

////
// Return a random value
  function tep_rand($min = null, $max = null) {
    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }

  function tep_setcookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = 0) {
    setcookie($name, $value, $expire, $path, (tep_not_null($domain) ? $domain : ''), $secure);
  }

  function tep_validate_ip_address($ip_address) {
    return filter_var($ip_address, FILTER_VALIDATE_IP, ['flags' => FILTER_FLAG_IPV4]);
  }

  function tep_get_ip_address() {
    $ip_addresses = [];

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      foreach ( array_reverse(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) as $x_ip ) {
        $ip_addresses[] = trim($x_ip);
      }
    }

    $ip_addresses[] = $_SERVER['HTTP_CLIENT_IP'] ?? null;
    $ip_addresses[] = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] ?? null;
    $ip_addresses[] = $_SERVER['HTTP_PROXY_USER'] ?? null;
    $ip_addresses[] = $_SERVER['REMOTE_ADDR'] ?? null;

    foreach ( $ip_addresses as $ip ) {
      if (!empty($ip) && tep_validate_ip_address($ip)) {
        return $ip;
      }
    }

    return false;
  }

  function tep_count_customer_orders($id = '', $check_session = true) {
    if (!is_numeric($id)) {
      $id = $_SESSION['customer_id'] ?? 0;
    }

    if ($check_session && ($id !== ($_SESSION['customer_id'] ?? null)) ) {
      return 0;
    }

    $orders_check_query = tep_db_query("SELECT COUNT(*) AS total FROM orders o, orders_status s WHERE o.customers_id = " . (int)$id . " AND o.orders_status = s.orders_status_id AND s.language_id = " . (int)$_SESSION['languages_id'] . " AND s.public_flag = 1");
    $orders_check = tep_db_fetch_array($orders_check_query);

    return $orders_check['total'];
  }

// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
  function tep_convert_linefeeds($from, $to, $string) {
    return str_replace($from, $to, $string);
  }

  function tep_delete_order($order_id) {
    tep_db_query('DELETE FROM orders WHERE orders_id = ' . (int)$order_id);
    tep_db_query('DELETE FROM orders_total WHERE orders_id = ' . (int)$order_id);
    tep_db_query('DELETE FROM orders_status_history WHERE orders_id = ' . (int)$order_id);
    tep_db_query('DELETE FROM orders_products WHERE orders_id = ' . (int)$order_id);
    tep_db_query('DELETE FROM orders_products_attributes WHERE orders_id = ' . (int)$order_id);
    tep_db_query('DELETE FROM orders_products_download WHERE orders_id = ' . (int)$order_id);
  }

  function tep_validate_form_action_is($action = 'process', $level = 1) {
    $requested_action = $_GET['action'] ?? $_POST['action'] ?? null;
    $formid = $_POST['formid'] ?? $_GET['formid'] ?? null;
    if (is_null($requested_action) || is_null($formid)) {
      return false;
    }

    $sessiontoken = $_SESSION['sessiontoken'];
    for (; $level > 1; $level--) {
      $sessiontoken = md5($sessiontoken);
    }

    $matched = is_array($action) ? in_array($requested_action, $action)
                                 : ($requested_action === $action);

    return ($matched && ($formid == $sessiontoken));
  }

  /**
   * For use by injectFormVerify hooks and Apps that need to block form processing.
   */
  function tep_block_form_processing() {
    $GLOBALS['error'] = true;
  }

  function tep_form_processing_is_valid() {
    return !($GLOBALS['error'] ?? false);
  }

  function tep_require_login($parameters = null) {
    if (!isset($_SESSION['customer_id'])) {
      $_SESSION['navigation']->set_snapshot($parameters);
      tep_redirect(tep_href_link('login.php', '', 'SSL'));
    }
  }

  function tep_ltrim_once($s, $prefix) {
    $length = strlen($prefix);
    if (substr($s, 0, $length) === $prefix) {
      return substr($s, $length);
    }

    return $s;
  }
