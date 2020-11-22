<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

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
      $field .= htmlspecialchars(stripslashes($request_value));
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
        $button .= ' target="_blank" rel="noreferrer"';
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

    $button .= $style ?? 'btn-light mt-2';

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

