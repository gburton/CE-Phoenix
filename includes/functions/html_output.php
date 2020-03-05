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
  function tep_href_link($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
    global $request_type, $session_started, $SID;

    $page = tep_output_string($page);

    if (!tep_not_null($page)) {
      die('<h5>Error!</h5><p>Unable to determine the page link!</p>');
    }

    if ($connection == 'NONSSL') {
      $link = HTTP_SERVER . DIR_WS_HTTP_CATALOG;
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL) {
        $link = HTTPS_SERVER . DIR_WS_HTTPS_CATALOG;
      } else {
        $link = HTTP_SERVER . DIR_WS_HTTP_CATALOG;
      }
    } else {
      die('<h5>Error!</h5><p>Unable to determine connection method on a link!</p><p>Known methods: NONSSL SSL</p>');
    }

    if (tep_not_null($parameters)) {
      $link .= $page . '?' . tep_output_string($parameters);
      $separator = '&';
    } else {
      $link .= $page;
      $separator = '?';
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

// Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
    if ( $add_session_id && $session_started && (SESSION_FORCE_COOKIE_USE == 'False') ) {
      if (isset($SID) && tep_not_null($SID)) {
        $_sid = $SID;
      } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && ENABLE_SSL ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
        if (HTTP_COOKIE_DOMAIN != HTTPS_COOKIE_DOMAIN) {
          $_sid = session_name() . '=' . session_id();
        }
      }
    }

    if (isset($_sid)) {
      $link .= $separator . tep_output_string($_sid);
    }

    while (strpos($link, '&&') !== false) $link = str_replace('&&', '&', $link);
    
    $link = str_replace('&', '&amp;', $link);

    return $link;
  }

////
// The HTML image wrapper function
  function tep_image($src, $alt = '', $width = '', $height = '', $parameters = '', $responsive = true, $bootstrap_css = '') {
    if (defined('DEFAULT_IMAGE') && tep_not_null(DEFAULT_IMAGE) && !is_file(DIR_FS_CATALOG . $src)) {
      $src = DEFAULT_IMAGE;
    } elseif ( (empty($src) || ($src == 'images/')) && (IMAGE_REQUIRED == 'false') ) {
      return false;
    }

// alt is added to the img tag even if it is null to prevent browsers from outputting
// the image filename as default
    $image = '<img src="' . tep_output_string($src) . '" alt="' . tep_output_string($alt) . '"';

    if (tep_not_null($alt)) {
      $image .= ' title="' . tep_output_string($alt) . '"';
    }

    if ( (CONFIG_CALCULATE_IMAGE_SIZE == 'true') && (empty($width) || empty($height)) ) {
      if ($image_size = @getimagesize($src)) {
        if (empty($width) && empty($height)) {
          $width = $image_size[0];
          $height = $image_size[1];
        } elseif (empty($width)) {
          $ratio = $height / $image_size[1];
          $width = (int)($image_size[0] * $ratio);
        } else {
          $ratio = $width / $image_size[0];
          $height = (int)($image_size[1] * $ratio);
        }
      } elseif (IMAGE_REQUIRED == 'false') {
        return false;
      }
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
// Output a form
  function tep_draw_form($name, $action, $method = 'post', $parameters = '', $tokenize = false) {
    $form = '<form name="' . tep_output_string($name) . '" action="' . tep_output_string($action) . '" method="' . tep_output_string($method) . '"';

    if (tep_not_null($parameters)) $form .= ' ' . $parameters;

    $form .= '>';

    if ( $tokenize && isset($_SESSION['sessiontoken']) ) {
      $form .= '<input type="hidden" name="formid" value="' . tep_output_string($_SESSION['sessiontoken']) . '" />';
    }

    return $form;
  }

////
// Output a form input field
  function tep_draw_input_field($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true, $class = 'class="form-control"') {
    $field = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

    if ( $reinsert_value && is_string($request_value = $_GET[$name] ?? $_POST[$name] ?? null) ) {
      $value = stripslashes($request_value);
    }

    if (tep_not_null($value)) {
      $field .= ' value="' . tep_output_string($value) . '"';
    }

    if (tep_not_null($parameters)) {
      $field .= " $parameters";
    }

    if (tep_not_null($class)) {
      $field .= " $class";
    }

    $field .= ' />';

    return $field;
  }

////
// Output a selection field - alias function for tep_draw_checkbox_field() and tep_draw_radio_field()
  function tep_draw_selection_field($name, $type, $value = '', $checked = false, $parameters = '') {
    $selection = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

    if (tep_not_null($value)) $selection .= ' value="' . tep_output_string($value) . '"';

    $request_value = $_GET[$name] ?? $_POST[$name] ?? null;
    if ( $checked || ('on' === $request_value) || (is_string($request_value) && (stripslashes($request_value) == $value)) ) {
      $selection .= ' checked="checked"';
    }

    if (tep_not_null($parameters)) $selection .= ' ' . $parameters;

    $selection .= ' />';

    return $selection;
  }

////
// Output a form checkbox field
  function tep_draw_checkbox_field($name, $value = '', $checked = false, $parameters = '') {
    return tep_draw_selection_field($name, 'checkbox', $value, $checked, $parameters);
  }

////
// Output a form radio field
  function tep_draw_radio_field($name, $value = '', $checked = false, $parameters = '') {
    return tep_draw_selection_field($name, 'radio', $value, $checked, $parameters);
  }

////
// Output a form textarea field
// The $wrap parameter is no longer used in the core xhtml template
  function tep_draw_textarea_field($name, $wrap, $width, $height, $text = '', $parameters = '', $reinsert_value = true) {
    $field = '<textarea class="form-control" name="' . tep_output_string($name) . '" cols="' . tep_output_string($width) . '" rows="' . tep_output_string($height) . '"';

    if (tep_not_null($parameters)) {
      $field .= ' ' . $parameters;
    }

    $field .= '>';

    if ( $reinsert_value && is_string($request_value = $_GET[$name] ?? $_POST[$name] ?? null) ) {
      $field .= tep_output_string_protected(stripslashes($request_value));
    } elseif (tep_not_null($text)) {
      $field .= tep_output_string_protected($text);
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
    } elseif ( is_string($request_value = $_GET[$name] ?? $_POST[$name] ?? null) ) {
      $field .= ' value="' . tep_output_string(stripslashes($request_value)) . '"';
    }

    if (tep_not_null($parameters)) {
      $field .= ' ' . $parameters;
    }

    $field .= ' />';

    return $field;
  }

////
// Hide form elements
  function tep_hide_session_id() {
    global $session_started, $SID;

    if ($session_started && tep_not_null($SID)) {
      return tep_draw_hidden_field(session_name(), session_id());
    }
  }

////
// Output a form pull down menu
  function tep_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
    $field = '<select name="' . tep_output_string($name) . '"';

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= ' class="form-control">';

    if (empty($default)) {
      if (is_string($_GET[$name] ?? null)) {
        $default = stripslashes($_GET[$name]);
      } elseif (is_string($_POST[$name] ?? null)) {
        $default = stripslashes($_POST[$name]);
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

    if ($required == true) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }

////
// Creates a pull-down list of countries
  function tep_get_country_list($name, $selected = '', $parameters = '') {
    $countries = [['id' => '', 'text' => PULL_DOWN_DEFAULT]];

    foreach (tep_get_countries() as $country) {
      $countries[] = ['id' => $country['countries_id'], 'text' => $country['countries_name']];
    }

    return tep_draw_pull_down_menu($name, $countries, $selected, $parameters);
  }

////
// Output a jQuery UI Button
  function tep_draw_button($title = null, $icon = null, $link = null, $priority = null, $params = [], $style = null) {
    static $button_counter = 1;

    if ( !isset($params['type']) || !in_array($params['type'], ['submit', 'button', 'reset']) ) {
      $params['type'] = 'submit';
    }

    if ( ($params['type'] == 'submit') && isset($link) ) {
      $params['type'] = 'button';
    }

    if (!isset($priority)) {
      $priority = 'secondary';
    }

    if ( ($params['type'] == 'button') && isset($link) ) {
      $button = '<a id="btn' . $button_counter . '" href="' . $link . '"';

      if ( isset($params['newwindow']) ) {
        $button .= ' target="_blank" rel="noopener"';
      }

      $closing_tag = '</a>';
    } else {
      $button = '<button ';
      $button .= ' type="' . tep_output_string($params['type']) . '"';
      $closing_tag = '</button>';
    }

    if ( isset($params['params']) ) {
      $button .= ' ' . $params['params'];
    }

    $button .= ' class="btn ';

    $button .= $style ?? 'btn-outline-secondary';

    $button .= '">';

    if (tep_not_null($icon ?? '')) {
      $button .= ' <span class="' . $icon . '" aria-hidden="true"></span> ';
    }

    $button .= $title;
    $button .= $closing_tag;

    $button_counter++;

    return $button;
  }

  // review stars
  function tep_draw_stars($rating = 0) {
    $star_rating = round($rating, 0, PHP_ROUND_HALF_UP);
    $stars = str_repeat('<i class="fas fa-star"></i>', $star_rating);
    $stars .= str_repeat('<i class="far fa-star"></i>', 5-$star_rating);

    return '<span class="text-warning" title="' . $rating . '">' . $stars . '</span>';
  }

