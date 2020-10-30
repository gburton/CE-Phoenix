<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/
?>

<div id="appPayPalToolbar" style="padding-bottom: 15px;">
  <?= $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('section_paypal'), tep_href_link('paypal.php', 'action=credentials&module=PP'), 'info', 'data-module="PP"'); ?>
  <?= $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('section_payflow'), tep_href_link('paypal.php', 'action=credentials&module=PF'), 'info', 'data-module="PF"'); ?>

<?php
  if ($current_module == 'PP') {
?>

  <span style="float: right;">
    <?= $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('button_retrieve_live_credentials'), tep_href_link('paypal.php', 'action=start&subaction=process&type=live'), 'warning'); ?>
    <?= $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('button_retrieve_sandbox_credentials'), tep_href_link('paypal.php', 'action=start&subaction=process&type=sandbox'), 'warning'); ?>
  </span>

<?php
  }
?>

</div>

<form name="paypalCredentials" action="<?php echo tep_href_link('paypal.php', 'action=credentials&subaction=process&module=' . $current_module); ?>" method="post">

<?php
  if ( $current_module == 'PP' ) {
  ?>
  
  <div class="row row-cols-1 row-cols-md-2">
    <div class="col mb-4">
      <div class="card">
        <div class="card-header">
          <?= $OSCOM_PayPal->getDef('paypal_live_title'); ?>
        </div>
        <div class="card-body">
          <div class="form-group row">
            <label for="live_username" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('paypal_live_api_username'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('live_username', OSCOM_APP_PAYPAL_LIVE_API_USERNAME); ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="live_password" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('paypal_live_api_password'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('live_password', OSCOM_APP_PAYPAL_LIVE_API_PASSWORD); ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="live_signature" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('paypal_live_api_signature'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('live_signature', OSCOM_APP_PAYPAL_LIVE_API_SIGNATURE); ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="live_merchant_id" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('paypal_live_merchant_id'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('live_merchant_id', OSCOM_APP_PAYPAL_LIVE_MERCHANT_ID); ?>
              <small class="form-text text-muted">
                <?php echo $OSCOM_PayPal->getDef('paypal_live_merchant_id_desc'); ?>
              </small>
            </div>
          </div>
          <div class="form-group row">
            <label for="live_email" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('paypal_live_email_address'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('live_email', OSCOM_APP_PAYPAL_LIVE_SELLER_EMAIL); ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="live_email_primary" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('paypal_live_primary_email_address'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('live_email_primary', OSCOM_APP_PAYPAL_LIVE_SELLER_EMAIL_PRIMARY); ?>
              <small class="form-text text-muted">
                <?php echo $OSCOM_PayPal->getDef('paypal_live_primary_email_address_desc'); ?>
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col mb-4">
      <div class="card">
        <div class="card-header">
          <?= $OSCOM_PayPal->getDef('paypal_sandbox_title'); ?>
        </div>
        <div class="card-body">
          <div class="form-group row">
            <label for="sandbox_username" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('paypal_sandbox_api_username'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('sandbox_username', OSCOM_APP_PAYPAL_SANDBOX_API_USERNAME); ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="sandbox_password" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('paypal_sandbox_api_password'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('sandbox_password', OSCOM_APP_PAYPAL_SANDBOX_API_PASSWORD); ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="sandbox_signature" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('paypal_sandbox_api_signature'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('sandbox_signature', OSCOM_APP_PAYPAL_SANDBOX_API_SIGNATURE); ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="sandbox_merchant_id" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('paypal_sandbox_merchant_id'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('sandbox_merchant_id', OSCOM_APP_PAYPAL_SANDBOX_MERCHANT_ID); ?>
              <small class="form-text text-muted">
                <?php echo $OSCOM_PayPal->getDef('paypal_sandbox_merchant_id_desc'); ?>
              </small>
            </div>
          </div>
          <div class="form-group row">
            <label for="sandbox_email" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('paypal_sandbox_email_address'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('sandbox_email', OSCOM_APP_PAYPAL_SANDBOX_SELLER_EMAIL); ?>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="sandbox_email_primary" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('paypal_sandbox_primary_email_address'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('sandbox_email_primary', OSCOM_APP_PAYPAL_SANDBOX_SELLER_EMAIL_PRIMARY); ?>
              <small class="form-text text-muted">
                <?php echo $OSCOM_PayPal->getDef('paypal_sandbox_primary_email_address_desc'); ?>
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php
  } elseif ( $current_module == 'PF' ) {
?>

  <div class="row row-cols-1 row-cols-md-2">
    <div class="col mb-4">
      <div class="card">
        <div class="card-header">
          <?= $OSCOM_PayPal->getDef('payflow_live_title'); ?>
        </div>
        <div class="card-body">
          <div class="form-group row">
            <label for="live_partner" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('payflow_live_partner'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('live_partner', OSCOM_APP_PAYPAL_PF_LIVE_PARTNER); ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="live_vendor" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('payflow_live_merchant_login'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('live_vendor', OSCOM_APP_PAYPAL_PF_LIVE_VENDOR); ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="live_user" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('payflow_live_user'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('live_user', OSCOM_APP_PAYPAL_PF_LIVE_USER); ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="live_password" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('payflow_live_password'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('live_password', OSCOM_APP_PAYPAL_PF_LIVE_PASSWORD); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col mb-4">
      <div class="card">
        <div class="card-header">
          <?= $OSCOM_PayPal->getDef('payflow_sandbox_title'); ?>
        </div>
        <div class="card-body">
          <div class="form-group row">
            <label for="sandbox_partner" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('payflow_sandbox_partner'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('sandbox_partner', OSCOM_APP_PAYPAL_PF_SANDBOX_PARTNER); ?>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="sandbox_vendor" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('payflow_sandbox_merchant_login'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('sandbox_vendor', OSCOM_APP_PAYPAL_PF_SANDBOX_VENDOR); ?>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="sandbox_user" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('payflow_sandbox_user'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('sandbox_user', OSCOM_APP_PAYPAL_PF_SANDBOX_USER); ?>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="sandbox_password" class="col-form-label col-12"><?= $OSCOM_PayPal->getDef('payflow_sandbox_password'); ?></label>
            <div class="col-12">
              <?= tep_draw_input_field('sandbox_password', OSCOM_APP_PAYPAL_PF_SANDBOX_PASSWORD); ?>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>

<?php
  }
?>

<p><?php echo $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('button_save'), null, 'success'); ?></p>

</form>

<script>
$(function() {
  $('#appPayPalToolbar a[data-module="<?php echo $current_module; ?>"]').addClass('active');
});
</script>
