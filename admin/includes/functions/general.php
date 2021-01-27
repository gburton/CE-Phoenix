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
    if ( strstr($url, "\n") || strstr($url, "\r") ) {
      tep_redirect(tep_href_link('index.php', '', 'SSL', false));
    }

    if ( strpos($url, '&amp;') !== false ) {
      $url = str_replace('&amp;', '&', $url);
    }

    header('Location: ' . $url);

    if (STORE_PAGE_PARSE_TIME == 'true') {
      Guarantor::ensure_global('logger')->timer_stop();
    }

    exit();
  }

////
// Parse the data used in the html tags to ensure the tags will not break
  function tep_parse_input_field_data($data, $parse) {
    trigger_error('The tep_parse_input_field_data function has been deprecated.', E_USER_DEPRECATED);
    return strtr(trim($data), $parse);
  }

  function tep_output_string($string, $translate = false, $protected = false) {
    if ($protected) {
      trigger_error('Calling the tep_output_string function with $protected true has been deprecated.', E_USER_DEPRECATED);
      return htmlspecialchars($string);
    }

    return Text::output($string, $translate);
  }

  function tep_output_string_protected($string) {
    trigger_error('The tep_output_string_protected function has been deprecated.', E_USER_DEPRECATED);
    return htmlspecialchars($string);
  }

  function tep_sanitize_string($string) {
    return Text::sanitize($string);
  }

  function tep_customers_name($customers_id) {
    $customer = new customer($customers_id);

    return $customer->get('name');
  }

  function tep_get_path($current_category_id = '') {
    if (empty($GLOBALS['cPath_array'])) {
      $cPath_new = $current_category_id;
    } elseif ('' === $current_category_id) {
      $cPath_new = implode('_', $GLOBALS['cPath_array']);
    } else {
      $cPath_new = Guarantor::ensure_global('category_tree')->find_path($current_category_id);
    }

    return 'cPath=' . $cPath_new;
  }

  function tep_get_all_get_params($excludes = []) {
    $excludes += [ session_name(), 'error'];

    $get_url = '';
    foreach ($_GET as $key => $value) {
      if (!in_array($key, $excludes)) {
        $get_url .= urlencode($key) . '=' . urlencode($value) . '&';
      }
    }

    return $get_url;
  }

  function tep_date_long($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = (int)substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    return strftime(DATE_FORMAT_LONG, mktime($hour, $minute, $second, $month, $day, $year));
  }

////
// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
// NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
  function tep_date_short($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

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

  function tep_datetime_short($raw_datetime) {
    if ( ($raw_datetime == '0000-00-00 00:00:00') || ($raw_datetime == '') ) return false;

    $year = (int)substr($raw_datetime, 0, 4);
    $month = (int)substr($raw_datetime, 5, 2);
    $day = (int)substr($raw_datetime, 8, 2);
    $hour = (int)substr($raw_datetime, 11, 2);
    $minute = (int)substr($raw_datetime, 14, 2);
    $second = (int)substr($raw_datetime, 17, 2);

    return strftime(DATE_TIME_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
  }

  function tep_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = [], $include_itself = false) {
    $category_tree =& Guarantor::ensure_global('category_tree');
    if (!is_array($category_tree_array)) $category_tree_array = [];
    if ( (count($category_tree_array) < 1) && ($exclude !== '0') ) $category_tree_array[] = ['id' => '0', 'text' => TEXT_TOP];

    if ($include_itself) {
      $category_tree_array[] = ['id' => $parent_id, 'text' => $category_tree->get($parent_id, 'name')];
    }

    $categories_query = tep_db_query("SELECT c.categories_id, cd.categories_name, c.parent_id FROM categories c, categories_description cd WHERE c.categories_id = cd.categories_id AND cd.language_id = " . (int)$_SESSION['languages_id'] . " AND c.parent_id = " . (int)$parent_id . " ORDER BY c.sort_order, cd.categories_name");
    foreach ($category_tree->get_children($parent_id) as $category_id) {
      if ($exclude != $category_id) $category_tree_array[] = ['id' => $category_id, 'text' => $spacing . $category_tree->get($category_id, 'name')];
      $category_tree_array = tep_get_category_tree($category_id, $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
    }

    return $category_tree_array;
  }

  function tep_draw_products_pull_down($name, $parameters = '', $exclude = [], $class = 'class="form-control"') {
    global $currencies;

    if ($exclude) {
      $exclude = [];
    }

    $select_string = '<select name="' . $name . '"';

    if (!Text::is_empty($parameters)) {
      $select_string .= " $parameters";
    }
    if (!Text::is_empty($class)) {
      $select_string .= " $class";
    }

    $select_string .= '>';

    $products_query = tep_db_query("SELECT p.products_id, pd.products_name, p.products_price FROM products p, products_description pd WHERE p.products_id = pd.products_id AND pd.language_id = " . (int)$_SESSION['languages_id'] . " ORDER BY products_name");
    while ($products = $products_query->fetch_assoc()) {
      if (!in_array($products['products_id'], $exclude)) {
        $select_string .= '<option value="' . $products['products_id'] . '">' . $products['products_name'] . ' (' . $currencies->format($products['products_price']) . ')</option>';
      }
    }

    $select_string .= '</select>';

    return $select_string;
  }

  function tep_format_system_info_array($array) {

    $output = '';
    foreach ($array as $section => $child) {
      $output .= '[' . $section . ']' . "\n";
      foreach ($child as $variable => $value) {
        if (is_array($value)) {
          $output .= $variable . ' = ' . implode(',', $value) ."\n";
        } else {
          $output .= $variable . ' = ' . $value . "\n";
        }
      }

    $output .= "\n";
    }
    return $output;

  }

  function tep_options_name($options_id) {
    $options = tep_db_query("SELECT products_options_name FROM products_options WHERE products_options_id = " . (int)$options_id . " AND language_id = " . (int)$_SESSION['languages_id']);
    $options_values = $options->fetch_assoc();

    return $options_values['products_options_name'];
  }

  function tep_values_name($values_id) {
    $values = tep_db_query("SELECT products_options_values_name FROM products_options_values WHERE products_options_values_id = " . (int)$values_id . " AND language_id = " . (int)$_SESSION['languages_id']);
    $values_values = $values->fetch_assoc();

    return $values_values['products_options_values_name'];
  }

  function tep_info_image($image, $alt, $width = '', $height = '') {
    if (!Text::is_empty($image) && (file_exists(DIR_FS_CATALOG . "images/$image")) ) {
      $image = tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . "images/$image", $alt, $width, $height);
    } else {
      $image = TEXT_IMAGE_NON_EXISTENT;
    }

    return $image;
  }

  function tep_break_string($string, $len, $break_char = '-') {
    $l = 0;
    $output = '';
    for ($i=0, $n=strlen($string); $i<$n; $i++) {
      if (' ' === $string[$i]) {
        $l = 0;
      } else {
        $l++;
      }

      if ($l > $len) {
        $l = 1;
        $output .= $break_char;
      }

      $output .= $string[$i];
    }

    return $output;
  }

  function tep_get_country_name($country_id) {
    $country_query = tep_db_query("SELECT countries_name FROM countries WHERE countries_id = " . (int)$country_id);

    if ($country = $country_query->fetch_assoc()) {
      return $country['countries_name'];
    } else {
      return $country_id;
    }
  }

  function tep_get_zone_name($country_id, $zone_id, $default_zone) {
    $zone_query = tep_db_query("SELECT zone_name FROM zones WHERE zone_country_id = " . (int)$country_id . " AND zone_id = " . (int)$zone_id);
    $zone = $zone_query->fetch_assoc();

    return $zone['zone_name'] ?? $default_zone;
  }

  function tep_not_null($value) {
    if (is_null($value)) {
      return false;
    }

    if (is_array($value)) {
      return count($value) > 0;
    }

    return ($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0);
  }

  function tep_browser_detect($component) {
    global $HTTP_USER_AGENT;

    return stristr($HTTP_USER_AGENT, $component);
  }

  function tep_tax_classes_pull_down($parameters, $selected = '') {
    $select_string = '<select ' . $parameters . '>';
    $classes_query = tep_db_query("SELECT tax_class_id, tax_class_title FROM tax_class ORDER BY tax_class_title");
    while ($classes = $classes_query->fetch_assoc()) {
      $select_string .= '<option value="' . $classes['tax_class_id'] . '"';
      if ($selected == $classes['tax_class_id']) $select_string .= ' SELECTED';
      $select_string .= '>' . $classes['tax_class_title'] . '</option>';
    }
    $select_string .= '</select>';

    return $select_string;
  }

  function tep_geo_zones_pull_down($parameters, $selected = '') {
    $select_string = '<select ' . $parameters . '>';
    $zones_query = tep_db_query("SELECT geo_zone_id, geo_zone_name FROM geo_zones ORDER BY geo_zone_name");
    while ($zones = $zones_query->fetch_assoc()) {
      $select_string .= '<option value="' . $zones['geo_zone_id'] . '"';
      if ($selected == $zones['geo_zone_id']) $select_string .= ' SELECTED';
      $select_string .= '>' . $zones['geo_zone_name'] . '</option>';
    }
    $select_string .= '</select>';

    return $select_string;
  }

  function tep_get_geo_zone_name($geo_zone_id) {
    $zones_query = tep_db_query("SELECT geo_zone_name FROM geo_zones WHERE geo_zone_id = " . (int)$geo_zone_id);
    $zone = $zones_query->fetch_assoc();

    return $zone['geo_zone_name'] ?? $geo_zone_id;
  }

  function tep_address_format($address_format_id, $address, $html, $boln, $eoln) {
    trigger_error('The tep_address_format function has been deprecated.', E_USER_DEPRECATED);

    $module = Guarantor::ensure_global('customer_data')->get_module('address');

    if (!isset($address['format_id'])) {
      $address['format_id'] = $address_format_id;
    }

    return $module->format($address, $html, $boln, $eoln);
  }

  ////////////////////////////////////////////////////////////////////////////////////////////////
  //
  // Function    : tep_get_zone_code
  //
  // Arguments   : country           country code string
  //               zone              state/province zone_id
  //               def_state         default string if zone==0
  //
  // Return      : state_prov_code   state/province code
  //
  // Description : Function to retrieve the state/province code (as in FL for Florida etc)
  //
  ////////////////////////////////////////////////////////////////////////////////////////////////
  function tep_get_zone_code($country, $zone, $default_state) {
    $state_prov_query = tep_db_query("SELECT zone_code FROM zones WHERE zone_country_id = " . (int)$country . " AND zone_id = " . (int)$zone);
    $state_prov_values = $state_prov_query->fetch_assoc();

    return $state_prov_values['zone_code'] ?? $default_state;
  }

  function tep_get_uprid($prid, $params) {
    trigger_error('The tep_get_uprid function has been deprecated.', E_USER_DEPRECATED);
    return Product::build_uprid($prid, $params);
  }

  function tep_get_prid($uprid) {
    trigger_error('The tep_get_prid function has been deprecated.', E_USER_DEPRECATED);
    return Product::build_prid($uprid);
  }

  function tep_get_languages() {
    $languages = [];

    $languages_query = tep_db_query("SELECT languages_id AS id, name, code, image, directory FROM languages ORDER BY sort_order");
    while ($language = $languages_query->fetch_assoc()) {
      $languages[] = $language;
    }

    return $languages;
  }

  function tep_get_category_name($category_id, $language_id) {
    trigger_error('The tep_get_category_name function has been deprecated.', E_USER_DEPRECATED);
    $category_query = tep_db_query("SELECT categories_name FROM categories_description WHERE categories_id = " . (int)$category_id . " AND language_id = " . (int)$language_id);
    $category = $category_query->fetch_assoc();

    return $category['categories_name'];
  }

  function tep_get_orders_status_name($orders_status_id, $language_id = '') {
    if (!$language_id) {
      $language_id = $_SESSION['languages_id'];
    }

    $orders_status_query = tep_db_query("SELECT orders_status_name FROM orders_status WHERE orders_status_id = " . (int)$orders_status_id . " AND language_id = " . (int)$language_id);
    $orders_status = $orders_status_query->fetch_assoc();

    return $orders_status['orders_status_name'];
  }

  function tep_get_orders_status() {
    $order_statuses = [];
    $order_status_query = tep_db_query("SELECT orders_status_id AS id, orders_status_name AS text FROM orders_status WHERE language_id = " . (int)$_SESSION['languages_id'] . " ORDER BY orders_status_id");
    while ($order_status = $order_status_query->fetch_assoc()) {
      $order_statuses[] = $order_status;
    }

    return $order_statuses;
  }

  function tep_get_products_name($product_id, $language_id = 0) {
    trigger_error('The tep_get_products_name function has been deprecated.', E_USER_DEPRECATED);
    return Product::fetch_name($product_id, $language_id);
  }

  function tep_get_products_description($product_id, $language_id) {
    trigger_error('The tep_get_products_description function has been deprecated.', E_USER_DEPRECATED);

    return product_by_id::build($product_id)->get('translations')[$language_id]['description'];
  }

  function tep_get_products_url($product_id, $language_id) {
    trigger_error('The tep_get_products_url function has been deprecated.', E_USER_DEPRECATED);

    return product_by_id::build($product_id)->get('translations')[$language_id]['url'];
  }

////
// Return the manufacturers URL in the needed language
// TABLES: manufacturers_info
  function tep_get_manufacturer_url($manufacturer_id, $language_id) {
    $manufacturer_query = tep_db_query("SELECT manufacturers_url FROM manufacturers_info WHERE manufacturers_id = " . (int)$manufacturer_id . " AND languages_id = " . (int)$language_id);
    $manufacturer = $manufacturer_query->fetch_assoc();

    return $manufacturer['manufacturers_url'];
  }

////
// Returns an array with countries
// TABLES: countries
  function tep_get_countries($default = '') {
    $countries = [];
    if ($default) {
      $countries[] = [
        'id' => '',
        'text' => $default];
    }
    $countries_query = tep_db_query("SELECT countries_id AS id, countries_name AS text FROM countries ORDER BY countries_name");
    while ($country = $countries_query->fetch_assoc()) {
      $countries[] = $country;
    }

    return $countries;
  }

////
// return an array with country zones
  function tep_get_country_zones($country_id) {
    $zones_array = [];
    $zones_query = tep_db_query("SELECT zone_id, zone_name FROM zones WHERE zone_country_id = " . (int)$country_id . " ORDER BY zone_name");
    while ($zone = $zones_query->fetch_assoc()) {
      $zones_array[] = [
        'id' => $zone['zone_id'],
        'text' => $zone['zone_name'],
      ];
    }

    return $zones_array;
  }

  function tep_prepare_country_zones_pull_down($country_id = '') {
// preset the width of the drop-down for Netscape
    $pre = '';
    if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) {
      for ($i=0; $i<45; $i++) $pre .= '&nbsp;';
    }

    $zones = tep_get_country_zones($country_id);

    if (count($zones) > 0) {
      $zones_select = [['id' => '', 'text' => PLEASE_SELECT]];
      $zones = array_merge($zones_select, $zones);
    } else {
      $zones = [['id' => '', 'text' => TYPE_BELOW]];
// create dummy options for Netscape to preset the height of the drop-down
      if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) {
        for ($i=0; $i<9; $i++) {
          $zones[] = ['id' => '', 'text' => $pre];
        }
      }
    }

    return $zones;
  }

////
// Get list of address_format_id's
  function tep_get_address_formats() {
    $address_format_query = tep_db_query("SELECT address_format_id FROM address_format ORDER BY address_format_id");
    $address_formats = [];
    while ($address_format_values = $address_format_query->fetch_assoc()) {
      $address_formats[] = [
        'id' => $address_format_values['address_format_id'],
        'text' => $address_format_values['address_format_id'],
      ];
    }
    return $address_formats;
  }

////
// Alias function for Store configuration values in the Administration Tool
  function tep_cfg_pull_down_country_list($country_id) {
    return tep_draw_pull_down_menu('configuration_value', tep_get_countries(), $country_id);
  }

  function tep_cfg_pull_down_zone_list($zone_id) {
    return tep_draw_pull_down_menu('configuration_value', tep_get_country_zones(STORE_COUNTRY), $zone_id);
  }

  function tep_cfg_pull_down_tax_classes($tax_class_id, $key = '') {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $tax_class_array = [['id' => '0', 'text' => TEXT_NONE]];
    $tax_class_query = tep_db_query("SELECT tax_class_id, tax_class_title FROM tax_class ORDER BY tax_class_title");
    while ($tax_class = $tax_class_query->fetch_assoc()) {
      $tax_class_array[] = [
        'id' => $tax_class['tax_class_id'],
        'text' => $tax_class['tax_class_title'],
      ];
    }

    return tep_draw_pull_down_menu($name, $tax_class_array, $tax_class_id);
  }

  function tep_cfg_pull_down_customer_data_groups($customer_data_group_id, $key) {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $customer_data_groups = [[ 'id' => '', 'text' => TEXT_NONE]];
    $group_query = tep_db_query(sprintf(<<<EOSQL
SELECT customer_data_groups_id AS id, customer_data_groups_name AS text
 FROM customer_data_groups
 WHERE language_id = %d
 ORDER BY cdg_vertical_sort_order, cdg_horizontal_sort_order
EOSQL
      , (int)$GLOBALS['languages_id']));
    while ($group = $group_query->fetch_assoc()) {
      $customer_data_groups[] = $group;
    }

    return tep_draw_pull_down_menu($name, $customer_data_groups, $customer_data_group_id);
  }

  function tep_get_customer_data_group_title($customer_data_group_id) {
    if ($customer_data_group_id == '0') {
      return TEXT_NONE;
    }

    $groups_query = tep_db_query("SELECT customer_data_groups_name FROM customer_data_groups WHERE customer_data_groups_id = " . (int)$customer_data_group_id . " AND language_id = " . (int)$GLOBALS['languages_id']);
    $groups = $groups_query->fetch_assoc();

    return $groups['customer_data_groups_name'];
  }

////
// Function to read in text area in admin
 function tep_cfg_textarea($text) {
    return tep_draw_textarea_field('configuration_value', false, 35, 5, $text);
  }

  function tep_cfg_get_zone_name($zone_id) {
    $zone_query = tep_db_query("SELECT zone_name FROM zones WHERE zone_id = " . (int)$zone_id);
    $zone = $zone_query->fetch_assoc();

    return $zone['zone_name'] ?? $zone_id;
  }

////
// Sets the status of a product
// DEPRECATE THIS ASAP
/*
  function tep_set_product_status($products_id, $status) {
    if ($status == '1') {
      return tep_db_query("UPDATE products SET products_status = 1, products_last_modified = NOW() WHERE products_id = " . (int)$products_id);
    } elseif ($status == '0') {
      return tep_db_query("UPDATE products SET products_status = 0, products_last_modified = NOW() WHERE products_id = " . (int)$products_id);
    } else {
      return -1;
    }
  }
*/

////
// Sets the status of a review
// DEPRECATE THIS ASAP
/*
  function tep_set_review_status($reviews_id, $status) {
    if ($status == '1') {
      return tep_db_query("UPDATE reviews SET reviews_status = 1, last_modified = NOW() WHERE reviews_id = " . (int)$reviews_id);
    } elseif ($status == '0') {
      return tep_db_query("UPDATE reviews SET reviews_status = 0, last_modified = NOW() WHERE reviews_id = " . (int)$reviews_id);
    } else {
      return -1;
    }
  }
*/

////
// Sets the status of a product on special
// DEPRECATE THIS ASAP
/*
  function tep_set_specials_status($specials_id, $status) {
    if ($status == '1') {
      return tep_db_query("UPDATE specials SET status = 1, expires_date = NULL, date_status_change = NULL WHERE specials_id = " . (int)$specials_id);
    } elseif ($status == '0') {
      return tep_db_query("UPDATE specials SET status = 0, date_status_change = NOW() WHERE specials_id = " . (int)$specials_id);
    } else {
      return -1;
    }
  }
*/

////
// Sets timeout for the current script.
// Cant be used in safe mode.
  function tep_set_time_limit($limit) {
    set_time_limit($limit);
  }

////
// Alias function for Store configuration values in the Administration Tool
  function tep_cfg_select_option($select_options, $key_value, $key = '') {
    $string = '';

    foreach ($select_options as $select_option) {
      $name = Text::is_empty($key) ? 'configuration_value' : 'configuration[' . $key . ']';

      $string .= '<br /><input type="radio" name="' . $name . '" value="' . $select_option . '"';

      if ($key_value == $select_option) $string .= ' checked="checked"';

      $string .= ' /> ' . $select_option;
    }

    return $string;
  }

////
// set_function for checkbox selections
  function tep_cfg_multiple_select_option($selections, $key_values, $key_name = null) {
    if (empty($key_values)) {
      $key_values = [];
    } elseif (!is_array($key_values)) {
      if (false === strpos($key_values, ';')) {
        $key_values = [$key_values => true];
      } else {
        $key_values = array_fill_keys(explode(';', $key_values), true);
      }
    }

    $key_name = isset($key_name) ? 'configuration[' . $key_name . ']' : 'configuration_value';

    $string = '';
    foreach ($selections as $key => $value) {
      if (is_int($key)) {
        $key = $value;
      }

      $string .= '<br /><label><input type="checkbox" name="' . $key_name . '[]" value="' . $key . '"';
      if (isset($key_values[$key]) || array_key_exists($key, $key_values)) {
        $string .= ' checked="checked"';
      }
      $string .= ' />' . $value . '</label>';
    }

    return $string;
  }

  function tep_cfg_select_template($key_value, $key = null) {
    $templates = [];
    foreach (scandir(DIR_FS_CATALOG . 'templates', SCANDIR_SORT_ASCENDING) as $template) {
      if ('.' !== $template[0]) {
        $templates[] = $template;
      }
    }

    return tep_cfg_select_option($templates, $key_value, $key);
  }

////
// Retrieve server information
  function tep_get_system_information() {
    $db_query = tep_db_query("SELECT NOW() AS datetime");
    $db = $db_query->fetch_assoc();

    @list($system, $host, $kernel) = preg_split('/[\s,]+/', @exec('uname -a'), 5);

    $data = [];

    $data['oscommerce']  = ['version' => tep_get_version()];

    $data['system'] = [
      'date' => date('Y-m-d H:i:s O T'),
      'os' => PHP_OS,
      'kernel' => $kernel,
      'uptime' => @exec('uptime'),
      'http_server' => $_SERVER['SERVER_SOFTWARE'],
    ];

    $data['mysql']  = [
      'version' => tep_db_get_server_info(),
      'date' => $db['datetime'],
    ];

    $data['php']    = [
      'version' => PHP_VERSION,
      'zend' => zend_version(),
      'sapi' => PHP_SAPI,
      'int_size'	=> defined('PHP_INT_SIZE') ? PHP_INT_SIZE : '',
      'open_basedir' => (int) @ini_get('open_basedir'),
      'memory_limit' => @ini_get('memory_limit'),
      'error_reporting' => error_reporting(),
      'display_errors' => (int)@ini_get('display_errors'),
      'allow_url_fopen' => (int) @ini_get('allow_url_fopen'),
      'allow_url_include' => (int) @ini_get('allow_url_include'),
      'file_uploads' => (int) @ini_get('file_uploads'),
      'upload_max_filesize' => @ini_get('upload_max_filesize'),
      'post_max_size' => @ini_get('post_max_size'),
      'disable_functions' => @ini_get('disable_functions'),
      'disable_classes' => @ini_get('disable_classes'),
      'enable_dl'	=> (int) @ini_get('enable_dl'),
      'filter.default'   => @ini_get('filter.default'),
      'zend.ze1_compatibility_mode' => (int) @ini_get('zend.ze1_compatibility_mode'),
      'unicode.semantics' => (int) @ini_get('unicode.semantics'),
      'zend_thread_safty'	=> (int) function_exists('zend_thread_id'),
      'extensions' => get_loaded_extensions(),
    ];

    return $data;
  }

  function tep_generate_category_path($id, $from = 'category', $categories = [], $index = 0) {
    $category_tree =& Guarantor::ensure_global('category_tree');
    if ($from == 'product') {
      $categories_query = tep_db_query("SELECT categories_id FROM products_to_categories WHERE products_id = " . (int)$id);
      while ($category = $categories_query->fetch_assoc()) {
        $categories[$index] = [];
        if ($category['categories_id'] == '0') {
          $categories[$index][] = ['id' => '0', 'text' => TEXT_TOP];
        } else {
          $categories = tep_generate_category_path($category['categories_id'], 'category', $categories, $index);
        }
        $index++;
      }
    } elseif ($from == 'category') {
      $ancestors = array_reverse($category_tree->get_ancestors($id));
      $ancestors[] = $id;
      foreach ($ancestors as $category_id) {
        $categories[$index][] = ['id' => $category_id, 'text' => $category_tree->get($category_id, 'name')];
      }
    }

    return $categories;
  }

  function tep_output_generated_category_path($id, $from = 'category') {
    return implode('<br />', array_map(function ($categories) {
      return implode('&nbsp;&gt;&nbsp;', array_column($categories, 'text'));
    }, tep_generate_category_path($id, $from))) ?: TEXT_TOP;
  }

  function tep_get_generated_category_path_ids($id, $from = 'category') {
    return implode('<br />', array_map(function ($c) {
      return implode('_', array_column($c, 'id'));
    }, tep_generate_category_path($id, $from))) ?: TEXT_TOP;
  }

  function tep_remove_category($category_id) {
    $duplicate_image_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT c1.categories_image
 FROM categories c1
   LEFT JOIN categories c2
     ON c1.categories_image = c2.categories_image
    AND c1.categories_id != c2.categories_id
 WHERE c1.categories_id = %d AND c2.categories_id IS NULL
EOSQL
      , (int)$category_id));
    $duplicate_image = $duplicate_image_query->fetch_assoc();

    if (isset($duplicate_image['categories_image'])
     && (is_file($image = DIR_FS_CATALOG . 'images/' . $duplicate_image['categories_image'])
       || is_link($image)))
    {
      @unlink(DIR_FS_CATALOG . 'images/' . $duplicate_image['categories_image']);
    }

    tep_db_query("DELETE FROM categories WHERE categories_id = " . (int)$category_id);
    tep_db_query("DELETE FROM categories_description WHERE categories_id = " . (int)$category_id);
    tep_db_query("DELETE FROM products_to_categories WHERE categories_id = " . (int)$category_id);
  }

  function tep_remove_product($product_id) {
    $product_image_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT p.products_image
 FROM products p
   LEFT JOIN products p2 ON p.products_image = p2.products_image AND p.products_id != p2.products_id
 WHERE p.products_id = %d AND p2.products_id IS NULL
EOSQL
      , (int)$product_id));
    $product_image = $product_image_query->fetch_assoc();

    if (isset($product_image['products_image'])
     && (is_file($image = DIR_FS_CATALOG . 'images/' . $product_image['products_image'])
      || is_link($image)))
    {
      @unlink($image);
    }

    $product_images_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT dpi.image
 FROM products_images dpi
   LEFT JOIN products_images pi ON dpi.image = pi.image AND dpi.products_id != pi.products_id
 WHERE dpi.products_id = %d AND pi.image IS NULL
EOSQL
      , (int)$product_id));
    while ($product_images = $product_images_query->fetch_assoc()) {
      $image = DIR_FS_CATALOG . 'images/' . $product_images['image'];

      if (is_file($image) || is_link($image)) {
        @unlink($image);
      }
    }

    tep_db_query("DELETE FROM products_images WHERE products_id = " . (int)$product_id);
    tep_db_query("DELETE FROM specials WHERE products_id = " . (int)$product_id);
    tep_db_query("DELETE FROM products_to_categories WHERE products_id = " . (int)$product_id);
    tep_db_query("DELETE FROM products_description WHERE products_id = " . (int)$product_id);
    tep_db_query("DELETE FROM products_attributes WHERE products_id = " . (int)$product_id);
    tep_db_query("DELETE FROM customers_basket WHERE products_id = " . (int)$product_id . " OR products_id LIKE '" . (int)$product_id . "{%'");
    tep_db_query("DELETE FROM customers_basket_attributes WHERE products_id = " . (int)$product_id . " OR products_id LIKE '" . (int)$product_id . "{%'");
    tep_db_query("DELETE r, rd FROM reviews r INNER JOIN reviews_description rd ON r.reviews_id = rd.reviews_id WHERE r.products_id = " . (int)$product_id);
    tep_db_query("DELETE FROM products WHERE products_id = " . (int)$product_id);
  }

  function tep_remove_order($order_id, $restock = false) {
    if ('on' === $restock) {
      tep_db_query(sprintf(<<<'EOSQL'
UPDATE products p INNER JOIN orders_products op ON p.products_id = op.products_id
  SET p.products_quantity = p.products_quantity + op.products_quantity,
      p.products_ordered = p.products_ordered - op.products_quantity
  WHERE op.orders_id = %d
EOSQL
        , (int)$order_id));
    }

    tep_db_query("DELETE FROM orders_products_attributes WHERE orders_id = " . (int)$order_id);
    tep_db_query("DELETE FROM orders_products WHERE orders_id = " . (int)$order_id);
    tep_db_query("DELETE FROM orders_status_history WHERE orders_id = " . (int)$order_id);
    tep_db_query("DELETE FROM orders_total WHERE orders_id = " . (int)$order_id);
    tep_db_query("DELETE FROM orders WHERE orders_id = " . (int)$order_id);
  }

  function tep_remove($source) {
    global $messageStack, $tep_remove_error;

    if (isset($tep_remove_error)) {
      $tep_remove_error = false;
    }

    if (is_dir($source)) {
      $dir = dir($source);
      while ($file = $dir->read()) {
        if ( ($file != '.') && ($file != '..') ) {
          if (tep_is_writable($source . '/' . $file)) {
            tep_remove($source . '/' . $file);
          } else {
            $messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source . '/' . $file), 'error');
            $tep_remove_error = true;
          }
        }
      }
      $dir->close();

      if (tep_is_writable($source)) {
        rmdir($source);
      } else {
        $messageStack->add(sprintf(ERROR_DIRECTORY_NOT_REMOVEABLE, $source), 'error');
        $tep_remove_error = true;
      }
    } else {
      if (tep_is_writable($source)) {
        unlink($source);
      } else {
        $messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source), 'error');
        $tep_remove_error = true;
      }
    }
  }

////
// Output the tax percentage with optional padded decimals
  function tep_display_tax_value($value, $padding = TAX_DECIMAL_PLACES) {
    if (false === ($decimal_position = strpos($value, '.'))) {
      $value .= '.';
    } else {
      $value = rtrim($value, '0');
      $decimal_position++;
      $padding += $decimal_position - strlen($value);
    }

    if ($padding > 0) {
      return $value . str_repeat('0', $padding);
    }

    return rtrim($value, '.');
  }

  function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) {
    if (SEND_EMAILS != 'true') {
      return false;
    }

    // Instantiate a new mail object
    $message = new email();
    $message->add_message($email_text);
    $message->build_message();
    return $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
  }

  function tep_notify($trigger, $subject) {
    $notified = false;

    if (defined('MODULE_NOTIFICATIONS_INSTALLED') && !Text::is_empty(MODULE_NOTIFICATIONS_INSTALLED)) {
      foreach ((array)explode(';', MODULE_NOTIFICATIONS_INSTALLED) as $basename) {
        $class = pathinfo($basename, PATHINFO_FILENAME);

        if (!isset($GLOBALS[$class])) {
          $GLOBALS[$class] = new $class();
        }

        if (!$GLOBALS[$class]->isEnabled()) {
          continue;
        }

        if (in_array($trigger, $class::TRIGGERS)) {
          $result = $GLOBALS[$class]->notify($subject);
          if (!is_null($result)) {
            $notified = $notified || $result;
          }
        }
      }
    }

    return $notified;
  }

  function tep_get_tax_class_title($tax_class_id) {
    if ($tax_class_id == '0') {
      return TEXT_NONE;
    }

    $classes_query = tep_db_query("SELECT tax_class_title FROM tax_class WHERE tax_class_id = " . (int)$tax_class_id);
    $classes = $classes_query->fetch_assoc();

    return $classes['tax_class_title'];
  }

////
// Wrapper function for round() for php3 compatibility
  function tep_round($value, $precision) {
    return round($value, $precision);
  }

////
// Add tax to a products price
  function tep_add_tax($price, $tax, $override = false) {
    if ( ( (DISPLAY_PRICE_WITH_TAX == 'true') || ($override == true) ) && ($tax > 0) ) {
      return $price + tep_calculate_tax($price, $tax);
    } else {
      return $price;
    }
  }

// Calculates Tax rounding the result
  function tep_calculate_tax($price, $tax) {
    return $price * $tax / 100;
  }

////
// Returns the tax rate for a zone / class
// TABLES: tax_rates, zones_to_geo_zones
  function tep_get_tax_rate($class_id, $country_id = -1, $zone_id = -1) {
    if ( ($country_id == -1) && ($zone_id == -1) ) {
      $country_id = STORE_COUNTRY;
      $zone_id = STORE_ZONE;
    }

    $tax_query = tep_db_query(<<<'EOSQL'
SELECT SUM(tax_rate) as tax_rate
 FROM tax_rates tr left join zones_to_geo_zones za ON tr.tax_zone_id = za.geo_zone_id left join geo_zones tz ON tz.geo_zone_id = tr.tax_zone_id
 WHERE (za.zone_country_id IS NULL OR za.zone_country_id = '0' OR za.zone_country_id =
EOSQL
      . (int)$country_id . ") AND (za.zone_id IS NULL OR za.zone_id = '0' OR za.zone_id = " . (int)$zone_id
      . ") AND tr.tax_class_id = " . (int)$class_id . " GROUP BY tr.tax_priority");
    if (mysqli_num_rows($tax_query)) {
      $tax_multiplier = 0;
      while ($tax = $tax_query->fetch_assoc()) {
        $tax_multiplier += $tax['tax_rate'];
      }
      return $tax_multiplier;
    } else {
      return 0;
    }
  }

////
// Returns the tax rate for a tax class
// TABLES: tax_rates
  function tep_get_tax_rate_value($class_id) {
    return tep_get_tax_rate($class_id, -1, -1);
  }

  function tep_call_function($function, $parameter, $object = '') {
    trigger_error('The tep_call_function function has been deprecated.', E_USER_DEPRECATED);
    if ($object == '') {
      return call_user_func($function, $parameter);
    } else {
      return call_user_func([$object, $function], $parameter);
    }
  }

  function tep_get_zone_class_title($zone_class_id) {
    if ($zone_class_id == '0') {
      return TEXT_NONE;
    }

    $classes_query = tep_db_query("SELECT geo_zone_name FROM geo_zones WHERE geo_zone_id = " . (int)$zone_class_id);
    $classes = $classes_query->fetch_assoc();

    return $classes['geo_zone_name'];
  }

  function tep_cfg_pull_down_zone_classes($zone_class_id, $key = '') {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $zone_classes = [['id' => '0', 'text' => TEXT_NONE]];
    $zone_class_query = tep_db_query("SELECT geo_zone_id AS id, geo_zone_name AS text FROM geo_zones ORDER BY geo_zone_name");
    while ($zone_class = $zone_class_query->fetch_assoc()) {
      $zone_classes[] = $zone_class;
    }

    return tep_draw_pull_down_menu($name, $zone_classes, $zone_class_id);
  }

  function tep_cfg_pull_down_order_statuses($order_status_id, $key = '') {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $statuses_array = [['id' => '0', 'text' => TEXT_DEFAULT]];
    $statuses_query = tep_db_query("SELECT orders_status_id, orders_status_name FROM orders_status WHERE language_id = " . (int)$_SESSION['languages_id'] . " ORDER BY orders_status_name");
    while ($statuses = $statuses_query->fetch_assoc()) {
      $statuses_array[] = [
        'id' => $statuses['orders_status_id'],
        'text' => $statuses['orders_status_name'],
      ];
    }

    return tep_draw_pull_down_menu($name, $statuses_array, $order_status_id);
  }

  function tep_get_order_status_name($order_status_id, $language_id = '') {
    if ($order_status_id < 1) {
      return TEXT_DEFAULT;
    }

    if (!is_numeric($language_id)) {
      $language_id = $_SESSION['languages_id'];
    }

    $status_query = tep_db_query("SELECT orders_status_name FROM orders_status WHERE orders_status_id = " . (int)$order_status_id . " AND language_id = " . (int)$language_id);
    $status = $status_query->fetch_assoc();

    return $status['orders_status_name'];
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

// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
  function tep_convert_linefeeds($from, $to, $string) {
    return str_replace($from, $to, $string);
  }

////
// Parse AND secure the cPath parameter values
  function tep_parse_category_path($cPath) {
// make sure the category IDs are integers
// make sure no duplicate category IDs exist which could lock the server in a loop
    return array_unique(array_map(function ($v) { return (int)$v; }, explode('_', $cPath)));
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

    foreach ( array_filter($ip_addresses) as $ip ) {
      if (tep_validate_ip_address($ip)) {
        return $ip;
      }
    }

    return null;
  }

////
// Wrapper function for is_writable() for Windows compatibility
  function tep_is_writable($file) {
    if (strtolower(substr(PHP_OS, 0, 3)) === 'win') {
      if (file_exists($file)) {
        $file = realpath($file);
        if (is_dir($file)) {
          $result = @tempnam($file, 'osc');
          if (is_string($result) && file_exists($result)) {
            unlink($result);
            return (strpos($result, $file) === 0);
          }
        } else {
          $handle = @fopen($file, 'r+');
          if (is_resource($handle)) {
            fclose($handle);
            return true;
          }
        }
      } else {
        $dir = dirname($file);
        if (file_exists($dir) && is_dir($dir) && tep_is_writable($dir)) {
          return true;
        }
      }
      return false;
    } else {
      return is_writable($file);
    }
  }

  function tep_get_category_description($category_id, $language_id) {
    trigger_error('The tep_get_category_description function has been deprecated.', E_USER_DEPRECATED);
    $category_query = tep_db_query("SELECT categories_description FROM categories_description WHERE categories_id = " . (int)$category_id . " AND language_id = " . (int)$language_id);
    $category = $category_query->fetch_assoc();

    return $category['categories_description'];
  }

  function tep_get_manufacturer_description($manufacturer_id, $language_id) {
    $manufacturer_query = tep_db_query("SELECT manufacturers_description FROM manufacturers_info WHERE manufacturers_id = " . (int)$manufacturer_id . " AND languages_id = " . (int)$language_id);
    $manufacturer = $manufacturer_query->fetch_assoc();

    return $manufacturer['manufacturers_description'];
  }

  function tep_get_category_seo_description($category_id, $language_id) {
    trigger_error('The tep_get_category_seo_description function has been deprecated.', E_USER_DEPRECATED);
    $category_query = tep_db_query("SELECT categories_seo_description FROM categories_description WHERE categories_id = " . (int)$category_id . " AND language_id = " . (int)$language_id);
    $category = $category_query->fetch_assoc();

    return $category['categories_seo_description'];
  }

  function tep_get_category_seo_title($category_id, $language_id = 0) {
    trigger_error('The tep_get_category_seo_title function has been deprecated.', E_USER_DEPRECATED);
    if ($language_id == 0) {
      $language_id = $_SESSION['languages_id'];
    }

    $category_query = tep_db_query("SELECT categories_seo_title FROM categories_description WHERE categories_id = " . (int)$category_id . " AND language_id = " . (int)$language_id);
    $category = $category_query->fetch_assoc();

    return $category['categories_seo_title'];
  }

  function tep_get_manufacturer_seo_description($manufacturer_id, $language_id) {
    $manufacturer_query = tep_db_query("SELECT manufacturers_seo_description FROM manufacturers_info WHERE manufacturers_id = " . (int)$manufacturer_id . " AND languages_id = " . (int)$language_id);
    $manufacturer = $manufacturer_query->fetch_assoc();

    return $manufacturer['manufacturers_seo_description'];
  }

  function tep_get_manufacturer_seo_title($manufacturer_id, $language_id) {
    $manufacturer_query = tep_db_query("SELECT manufacturers_seo_title FROM manufacturers_info WHERE manufacturers_id = " . (int)$manufacturer_id . " AND languages_id = " . (int)$language_id);
    $manufacturer = $manufacturer_query->fetch_assoc();

    return $manufacturer['manufacturers_seo_title'];
  }

  function tep_get_products_seo_description($product_id, $language_id = 0) {
    trigger_error('The tep_get_products_seo_description function has been deprecated.', E_USER_DEPRECATED);

    return product_by_id::build($product_id)->get('translations')[$language_id]['seo_description'];
  }

  function tep_get_products_seo_keywords($product_id, $language_id = 0) {
    trigger_error('The tep_get_products_seo_keywords function has been deprecated.', E_USER_DEPRECATED);

    return product_by_id::build($product_id)->get('translations')[$language_id]['seo_keywords'];
  }

  function tep_get_products_seo_title($product_id, $language_id = 0) {
    trigger_error('The tep_get_products_seo_title function has been deprecated.', E_USER_DEPRECATED);

    return product_by_id::build($product_id)->get('translations')[$language_id]['seo_title'];
  }

  function tep_draw_products($name, $parameters = '', $exclude = [], $class = 'class="form-control"') {
    $select_string = '<select name="' . $name . '"';

    if (!Text::is_empty($parameters)) $select_string .= " $parameters";
    if (!Text::is_empty($class)) $select_string .= " $class";

    $select_string .= '>';

    $select_string .= '<option value="">--- ' . IMAGE_SELECT . ' ---</option>';

    $products_query = tep_db_query(<<<'EOSQL'
SELECT p.products_id, pd.products_name
 FROM products p, products_description pd
 WHERE p.products_id = pd.products_id AND pd.language_id =
EOSQL
      . (int)$_SESSION['languages_id'] . " ORDER BY products_name");
    while ($products = $products_query->fetch_assoc()) {
      if (!in_array($products['products_id'], $exclude)) {
        $select_string .= '<option value="' . $products['products_id'] . '">' . $products['products_name'] . '</option>';
      }
    }

    $select_string .= '</select>';

    return $select_string;
  }

  function tep_generate_customers() {
    global $customer_data;

    $query = tep_db_query($customer_data->add_order_by(
      $customer_data->build_read([ 'id', 'sortable_name'], 'customers'), ['sortable_name']));
    while ($customer_details = $query->fetch_assoc()) {
      $customer_data->get([ 'id', 'sortable_name' ], $customer_details);
      yield $customer_details;
    }
  }

  function tep_draw_customers($name, $parameters = '', $selected = '', $class = 'class="form-control"') {
    $select_string = '<select name="' . $name . '"';

    if (!Text::is_empty($parameters)) {
      $select_string .= " $parameters";
    }
    if (!Text::is_empty($class)) {
      $select_string .= " $class";
    }

    $select_string .= '>';
    $select_string .= '<option value="">--- ' . IMAGE_SELECT . ' ---</option>';

    foreach (tep_generate_customers() as $customer_details) {
      $select_string .= '<option value="' . $customer_details['id'] . '"';
      if ($selected === $customer_details['id']) {
        $select_string .= ' selected="selected"';
      }
      $select_string .= '>';
      $select_string .=  $customer_details['sortable_name'];
      $select_string .= '</option>';
    }

    $select_string .= '</select>';

    return $select_string;
  }

  function tep_draw_account_edit_pages($key_values, $key_name = null) {
    $pages = [
      'account_edit',
      'account_newsletters',
      'account_password',
      'address_book',
      'checkout_new_address',
      'create_account',
      'customers',
    ];

    $parameters = ['pages' => &$pages];
    $GLOBALS['OSCOM_Hooks']->call('siteWide', 'accountEditPages', $parameters);

    return tep_cfg_multiple_select_option($pages, $key_values, $key_name);
  }

  function tep_block_form_processing() {
    $GLOBALS['error'] = true;
  }

  function tep_form_processing_is_valid() {
    return !($GLOBALS['error'] ?? false);
  }

  function tep_ltrim_once($s, $prefix) {
    trigger_error('The tep_ltrim_once function has been deprecated.', E_USER_DEPRECATED);
    return Text::ltrim_once($s, $prefix);
  }
