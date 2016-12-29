<div class="paypal-login <?php echo (OSCOM_APP_PAYPAL_LOGIN_CONTENT_WIDTH == 'Half') ? 'col-sm-6' : 'col-sm-12'; ?>">
  <div class="login-login panel panel-info">
    <div class="panel-body">
    <h2><?php echo $cm_paypal_login->_app->getDef('module_login_template_title'); ?></h2>

<?php
  if ( OSCOM_APP_PAYPAL_LOGIN_STATUS == '0' ) {
    echo '    <p class="alert alert-warning">' . $cm_paypal_login->_app->getDef('module_login_template_sandbox_alert') . '</p>';
  }
?>

    	<p class="alert alert-info"><?php echo $cm_paypal_login->_app->getDef('module_login_template_content'); ?></p>

    	<div id="PayPalLoginButton" class="text-right"></div>
    </div>
  </div>
</div>

<?php
if ( OSCOM_APP_PAYPAL_LOGIN_STATUS == '0' ) {
  $authend = 'sandbox';
}
if ( OSCOM_APP_PAYPAL_LOGIN_THEME == 'Neutral' ) {
  $theme = 'neutral';
}

$paypal_login_script = '<script type="text/javascript">
paypal.use( ["login"], function(login) {
  login.render ({
    "authend": "' . $authend . '",
    "theme": "' . $theme . '",
    "locale": "' . $cm_paypal_login->_app->getDef('module_login_language_locale') . '",
    "appid": "' . (OSCOM_APP_PAYPAL_LOGIN_STATUS == '1' ? OSCOM_APP_PAYPAL_LOGIN_LIVE_CLIENT_ID : OSCOM_APP_PAYPAL_LOGIN_SANDBOX_CLIENT_ID) . '",
    "scopes": "' . implode(' ', $use_scopes) . '",
    "containerid": "PayPalLoginButton",
    "returnurl": "' . str_replace('&amp;', '&', tep_href_link(FILENAME_LOGIN, 'action=paypal_login', 'SSL', false)) . '"
  });
});
</script>' . "\n";

$oscTemplate->addBlock('<script type="text/javascript" src="https://www.paypalobjects.com/js/external/api.js"></script>' . "\n", 'footer_scripts');
$oscTemplate->addBlock($paypal_login_script, 'footer_scripts');
?>
