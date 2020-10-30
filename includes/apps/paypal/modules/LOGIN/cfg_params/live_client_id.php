<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_LOGIN_Cfg_live_client_id {
    var $default = '';
    var $title;
    var $description;
    var $sort_order = 200;

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_login_live_client_id_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_login_live_client_id_desc');
    }

    function getSetField() {
      $input = tep_draw_input_field('live_client_id', OSCOM_APP_PAYPAL_LOGIN_LIVE_CLIENT_ID, 'id="inputLogInLiveClientId"');

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div class="mb-3">{$input}</div>
EOT;

      return $result;
    }
  }
?>
