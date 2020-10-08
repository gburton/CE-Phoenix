<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/
?>

<div class="row row-cols-1 row-cols-md-2">
  <div class="col mb-4">
    <div class="card">
      <div class="card-header">
        <?php echo $OSCOM_PayPal->getDef('onboarding_intro_title'); ?>
      </div>
      <div class="card-body">
        <?= $OSCOM_PayPal->getDef('onboarding_intro_body', array('button_retrieve_live_credentials' => $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('button_retrieve_live_credentials'), tep_href_link('paypal.php', 'action=start&subaction=process&type=live'), 'info'), 'button_retrieve_sandbox_credentials' => $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('button_retrieve_sandbox_credentials'), tep_href_link('paypal.php', 'action=start&subaction=process&type=sandbox'), 'info'))); ?>
      </div>
    </div>
  </div>
  <div class="col mb-4">
    <div class="card">
      <div class="card-header">
        <?= $OSCOM_PayPal->getDef('manage_credentials_title'); ?>
      </div>
      <div class="card-body">
        <?= $OSCOM_PayPal->getDef('manage_credentials_body', array('button_manage_credentials' => $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('button_manage_credentials'), tep_href_link('paypal.php', 'action=credentials'), 'warning'))); ?> 
      </div>
    </div>
  </div>
</div>
