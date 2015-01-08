<div class="paypal-login <?php echo (MODULE_CONTENT_PAYPAL_LOGIN_CONTENT_WIDTH == 'Half') ? 'col-sm-6' : 'col-sm-12'; ?>">
  <div class="panel panel-success">
    <div class="panel-body">
      <h2><?php echo MODULE_CONTENT_PAYPAL_LOGIN_TEMPLATE_TITLE; ?></h2>

<?php
  if ( MODULE_CONTENT_PAYPAL_LOGIN_SERVER_TYPE == 'Sandbox' ) {
    echo '    <p class="messageStackError">' . MODULE_CONTENT_PAYPAL_LOGIN_TEMPLATE_SANDBOX . '</p>';
  }
?>

      <p class="alert alert-success"><?php echo MODULE_CONTENT_PAYPAL_LOGIN_TEMPLATE_CONTENT; ?></p>

      <div id="PayPalLoginButton" class="text-right"></div>
    </div>
  </div>
</div>

<script src="https://www.paypalobjects.com/js/external/api.js"></script>
<script>
paypal.use( ["login"], function(login) {
  login.render ({

<?php
  if ( MODULE_CONTENT_PAYPAL_LOGIN_SERVER_TYPE == 'Sandbox' ) {
    echo '    "authend": "sandbox",';
  }

  if ( MODULE_CONTENT_PAYPAL_LOGIN_THEME == 'Neutral' ) {
    echo '    "theme": "neutral",';
  }

  if ( defined('MODULE_CONTENT_PAYPAL_LOGIN_LANGUAGE_LOCALE') && tep_not_null(MODULE_CONTENT_PAYPAL_LOGIN_LANGUAGE_LOCALE) ) {
    echo '    "locale": "' . MODULE_CONTENT_PAYPAL_LOGIN_LANGUAGE_LOCALE . '",';
  }
?>

    "appid": "<?php echo MODULE_CONTENT_PAYPAL_LOGIN_CLIENT_ID; ?>",
    "scopes": "<?php echo implode(' ', $use_scopes); ?>",
    "containerid": "PayPalLoginButton",
    "returnurl": "<?php echo str_replace('&amp;', '&', tep_href_link(FILENAME_LOGIN, 'action=paypal_login', 'SSL', false)); ?>"
  });
});
</script>
