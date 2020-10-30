<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_LOGIN_Cfg_theme {
    var $default = 'Blue';
    var $title;
    var $description;
    var $sort_order = 600;

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_login_theme_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_login_theme_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;

      $input = null;
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="themeSelectionBlue" name="theme" value="Blue"' . (OSCOM_APP_PAYPAL_LOGIN_THEME == 'Blue' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="themeSelectionBlue">' . $OSCOM_PayPal->getDef('cfg_login_theme_blue') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="themeSelectionNeutral" name="theme" value="Neutral"' . (OSCOM_APP_PAYPAL_LOGIN_THEME == 'Neutral' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="themeSelectionNeutral">' . $OSCOM_PayPal->getDef('cfg_login_theme_neutral') . '</label>';
      $input .= '</div>';

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div class="mb-3" id="themeSelection">{$input}</div>
EOT;

      return $result;
    }
  }
?>
