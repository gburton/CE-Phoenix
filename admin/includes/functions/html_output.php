<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

////
// The HTML href link wrapper function
  function tep_href_link($page = '', $parameters = '', $connection = 'SSL', $add_session_id = true) {
    $page = tep_output_string($page);

    if ($page == '') {
      die(<<<EOERROR
<h5>Error!</h5>
<p>Unable to determine the page link!</p>
<p>Function used:</p>
<p>tep_href_link('$page', '$parameters', '$connection', '$add_session_id')</p>
EOERROR
);
    }

    $link = HTTP_SERVER . DIR_WS_ADMIN . $page;

    if (tep_not_null($parameters)) {
      $link .= '?' . tep_output_string($parameters);
      $separator = '&';
    } else {
      $separator = '?';
    }

    $link = rtrim($link, '&?');

// Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
    if ( $add_session_id && isset($SID) && (SESSION_FORCE_COOKIE_USE == 'False') && tep_not_null($SID) ) {
      $_sid = $SID;
    }

    if (isset($_sid)) {
      $link .= $separator . tep_output_string($_sid);
    }

    while (strpos($link, '&&') !== false) {
      $link = str_replace('&&', '&', $link);
    }

    return $link;
  }

  function tep_catalog_href_link($page = '', $parameters = '', $connection = 'NONSSL') {
    $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG . $page;

    if ('' !== $parameters) {
      $link .= '?' . $parameters;
    }

    return rtrim($link, '&?');
  }

////
// The HTML image wrapper function
  function tep_image($src, $alt = '', $width = '', $height = '', $parameters = '', $responsive = true, $bootstrap_css = '') {
    $image = '<img src="' . tep_output_string($src) . '" border="0" alt="' . tep_output_string($alt) . '"';

    if (tep_not_null($alt)) {
      $image .= ' title="' . tep_output_string($alt) . '"';
    }

    if (tep_not_null($width) && tep_not_null($height)) {
      $image .= ' width="' . tep_output_string($width) . '" height="' . tep_output_string($height) . '"';
    }

    $image .= ' class="';

    if ($responsive === true) {
      $image .= 'img-fluid';
    }

    if (tep_not_null($bootstrap_css)) {
      $image .= ' ' . $bootstrap_css;
    }

    $image .= '"';

    if (tep_not_null($parameters)) {
      $image .= ' ' . $parameters;
    }

    $image .= ' />';

    return $image;
  }

////
// Draw a 1 pixel black line
// DEPRECATE THIS ASAP
  function tep_black_line() {
    return null;
    //return tep_image('images/pixel_black.gif', '', '100%', '1', null, false);
  }

////
// Output a separator either through whitespace, or with an image
// DEPRECATE THIS ASAP
  function tep_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {
    return null;
    //return tep_image('images/' . $image, '', $width, $height, null, false);
  }

////
// javascript to dynamically update the states/provinces list when the country is changed
// TABLES: zones
  function tep_js_zone_list($country, $form, $field) {
    $countries_query = tep_db_query("select distinct zone_country_id from zones order by zone_country_id");
    $num_country = 1;
    $output_string = '';
    while ($countries = tep_db_fetch_array($countries_query)) {
      if ($num_country == 1) {
        $output_string .= '  if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";
      } else {
        $output_string .= '  } else if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";
      }

      $states_query = tep_db_query("select zone_name, zone_id from zones where zone_country_id = '" . $countries['zone_country_id'] . "' order by zone_name");

      $num_state = 1;
      while ($states = tep_db_fetch_array($states_query)) {
        if ($num_state == '1') $output_string .= '    ' . $form . '.' . $field . '.options[0] = new Option("' . PLEASE_SELECT . '", "");' . "\n";
        $output_string .= '    ' . $form . '.' . $field . '.options[' . $num_state . '] = new Option("' . $states['zone_name'] . '", "' . $states['zone_id'] . '");' . "\n";
        $num_state++;
      }
      $num_country++;
    }
    $output_string .= '  } else {' . "\n" .
                      '    ' . $form . '.' . $field . '.options[0] = new Option("' . TYPE_BELOW . '", "");' . "\n" .
                      '  }' . "\n";

    return $output_string;
  }

////
// Output a form
  function tep_draw_form($name, $action, $parameters = '', $method = 'post', $params = '') {
    $form = '<form name="' . tep_output_string($name) . '" action="';
    if (tep_not_null($parameters)) {
      $form .= tep_href_link($action, $parameters);
    } else {
      $form .= tep_href_link($action);
    }
    $form .= '" method="' . tep_output_string($method) . '"';
    if (tep_not_null($params)) {
      $form .= ' ' . $params;
    }
    $form .= '>';

    return $form;
  }

////
// Output a form input field
  function tep_draw_input_field($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true, $class = 'class="form-control"') {
    $field = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

    if ( $reinsert_value ) {
      $request = $_GET[$name] ?? $_POST[$name] ?? null;
      if (is_string($request)) {
        $value = stripslashes($request);
      }
    }

    if (tep_not_null($value)) {
      $field .= ' value="' . tep_output_string($value) . '"';
    }

    if (tep_not_null($parameters)) $field .= " $parameters";
    if (tep_not_null($class) && (false === strpos($parameters, 'class="'))) $field .= " $class";

    $field .= ' />';

    return $field;
  }

////
// Output a form filefield
  function tep_draw_file_field($name) {
    $field = tep_draw_input_field($name, '', '', 'file');

    return $field;
  }

////
// Output a selection field - alias function for tep_draw_checkbox_field() and tep_draw_radio_field()
  function tep_draw_selection_field($name, $type, $value = '', $checked = false, $parameters = null) {
    $selection = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

    if (tep_not_null($value)) $selection .= ' value="' . tep_output_string($value) . '"';

    $request = $_GET[$name] ?? $_POST[$name] ?? null;
    if ( $checked || (is_string($request) && (('on' === $request) || (stripslashes($request) == $value))) ) {
      $selection .= ' checked="checked"';
    }

    if (tep_not_null($parameters)) $selection .= ' ' . $parameters;

    $selection .= ' />';

    return $selection;
  }

////
// Output a form checkbox field
// DEPRECATE this from Phoenix over time.
  function tep_draw_checkbox_field($name, $value = '', $checked = false, $compare = '') {
    return tep_draw_selection_field($name, 'checkbox', $value, ($checked || (tep_not_null($compare) && ($value == $compare))));
  }

////
// Output a form radio field
// DEPRECATE this from Phoenix over time.
  function tep_draw_radio_field($name, $value = '', $checked = false, $compare = '') {
    return tep_draw_selection_field($name, 'radio', $value, ($checked || (tep_not_null($compare) && ($value == $compare))));
  }

////
// Output a form textarea field
// The $wrap parameter is no longer used in the core xhtml template
  function tep_draw_textarea_field($name, $wrap, $width, $height, $text = '', $parameters = '', $reinsert_value = true, $class = 'class="form-control"') {
    $field = '<textarea name="' . tep_output_string($name) . '" cols="' . tep_output_string($width) . '" rows="' . tep_output_string($height) . '"';

    if (tep_not_null($parameters)) $field .= " $parameters";
    if (tep_not_null($class) && (false === strpos($parameters, 'class="'))) $field .= " $class";

    $field .= '>';

    if ( $reinsert_value && is_string($requested_value = $_GET[$name] ?? $_POST[$name] ?? null) ) {
      $field .= htmlspecialchars(stripslashes($requested_value));
    } elseif (tep_not_null($text)) {
      $field .= htmlspecialchars($text);
    }

    $field .= '</textarea>';

    return $field;
  }

////
// Output a form hidden field
  function tep_draw_hidden_field($name, $value = '', $parameters = '') {
    $field = '<input type="hidden" name="' . tep_output_string($name) . '"';

    if (tep_not_null($value)) {
      $field .= ' value="' . tep_output_string($value) . '"';
    } else {
      $requested_value = $_GET[$name] ?? $_POST[$name] ?? null;
      if ( is_string($requested_value) ) {
        $field .= ' value="' . tep_output_string(stripslashes($requested_value)) . '"';
      }
    }

    if (tep_not_null($parameters)) {
      $field .= " $parameters";
    }

    $field .= ' />';

    return $field;
  }

////
// Hide form elements
  function tep_hide_session_id() {
    $string = '';

    if (defined('SID') && tep_not_null(SID)) {
      $string = tep_draw_hidden_field(session_name(), session_id());
    }

    return $string;
  }

////
// Output a form pull down menu
  function tep_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $class = 'class="form-control"') {
    $field = '<select name="' . tep_output_string($name) . '"';

    if (tep_not_null($parameters)) $field .= " $parameters";
    if (tep_not_null($class)) $field .= " $class";

    $field .= '>';

    if ( empty($default) ) {
      $request = $_GET[$name] ?? $_POST[$name] ?? null;
      if (is_string($request)) {
        $default = stripslashes($request);
      }
    }

    foreach ($values as $value) {
      $field .= '<option value="' . tep_output_string($value['id']) . '"';
      if ($default == $value['id']) {
        $field .= ' selected="selected"';
      }

      $field .= '>' . tep_output_string($value['text'], ['"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;']) . '</option>';
    }
    $field .= '</select>';

    return $field;
  }

////
// Output a jQuery UI Button
  function tep_draw_button($title = null, $icon = null, $link = null, $priority = null, $params = null) {
    static $button_counter = 1;

    $types = ['submit', 'button', 'reset'];

    if ( !isset($params['type']) ) {
      $params['type'] = 'submit';
    }

    if ( !in_array($params['type'], $types) ) {
      $params['type'] = 'submit';
    }

    if ( ($params['type'] == 'submit') && isset($link) ) {
      $params['type'] = 'button';
    }

    if (!isset($priority)) {
      $priority = 'secondary';
    }

    $button = '<span class="tdbLink">';

    if ( ($params['type'] == 'button') && isset($link) ) {
      $button .= '<a id="tdb' . $button_counter . '" href="' . $link . '"';

      if ( isset($params['newwindow']) ) {
        $button .= ' target="_blank" rel="noreferrer"';
      }
    } else {
      $button .= '<button id="tdb' . $button_counter . '" type="' . tep_output_string($params['type']) . '"';
    }

    if ( isset($params['params']) ) {
      $button .= ' ' . $params['params'];
    }

    $button .= '>' . $title;

    if ( ($params['type'] == 'button') && isset($link) ) {
      $button .= '</a>';
    } else {
      $button .= '</button>';
    }

    $button .= '</span><script>$("#tdb' . $button_counter . '").button(';

    $args = [];

    if ( isset($icon) ) {
      if ( !isset($params['iconpos']) ) {
        $params['iconpos'] = 'left';
      }

      if ( $params['iconpos'] == 'left' ) {
        $args[] = 'icons:{primary:"ui-icon-' . $icon . '"}';
      } else {
        $args[] = 'icons:{secondary:"ui-icon-' . $icon . '"}';
      }
    }

    if (empty($title)) {
      $args[] = 'text:false';
    }

    if (!empty($args)) {
      $button .= '{' . implode(',', $args) . '}';
    }

    $button .= ').addClass("ui-priority-' . $priority . '").parent().removeClass("tdbLink");</script>';

    $button_counter++;

    return $button;
  }

////
// Output a Bootstrap Button
  function tep_draw_bootstrap_button($title = null, $icon = null, $link = null, $priority = 'secondary', $params = [], $style = null) {
    if ( !isset($params['type']) || !in_array($params['type'], ['submit', 'button', 'reset']) ) {
      $params['type'] = 'submit';
    }

    if ( ($params['type'] == 'submit') && isset($link) ) {
      $params['type'] = 'button';
    }

    if ( ($params['type'] == 'button') && isset($link) ) {
      $button = '<a href="' . $link . '"';

      if ( isset($params['newwindow']) ) {
        $button .= ' target="_blank" rel="noreferrer"';
      }
      $closing_tag = '</a>';
    } else {
      $button = '<button type="' . tep_output_string($params['type']) . '"';
      $closing_tag = '</button>';
    }

    if ( isset($params['params']) ) {
      $button .= ' ' . $params['params'];
    }

    $button .= ' class="btn ';
    $button .= (isset($style)) ? $style : 'btn-outline-secondary';
    $button .= '">';

    if (isset($icon) && tep_not_null($icon)) {
      $button .= ' <span class="' . $icon . '" aria-hidden="true"></span> ';
    }

    $button .= $title;
    $button .= $closing_tag;

    return $button;
  }

  // review stars
  function tep_draw_stars($rating = 0) {
    $star_rating = round($rating, 0, PHP_ROUND_HALF_UP);
    $stars = str_repeat('<i class="fas fa-star"></i>', $star_rating);
    $stars .= str_repeat('<i class="far fa-star"></i>', 5-$star_rating);

    return '<span class="text-warning" title="' . $rating . '">' . $stars . '</span>';
  }

