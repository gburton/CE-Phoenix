<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(MODULE_CONTENT_ACCOUNT_BRAINTREE_CARDS_NAVBAR_TITLE_1, tep_href_link('account.php'));
  $breadcrumb->add(MODULE_CONTENT_ACCOUNT_BRAINTREE_CARDS_NAVBAR_TITLE_2, tep_href_link('ext/modules/content/account/braintree/cards.php'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo MODULE_CONTENT_ACCOUNT_BRAINTREE_CARDS_HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size('cards') > 0) {
    echo $messageStack->output('cards');
  }
?>

  <?php echo MODULE_CONTENT_ACCOUNT_BRAINTREE_CARDS_TEXT_DESCRIPTION; ?>

  <h4><?php echo MODULE_CONTENT_ACCOUNT_BRAINTREE_CARDS_SAVED_CARDS_TITLE; ?></h4>

  <div class="contentText row align-items-center">

<?php
  $tokens_query = tep_db_query("SELECT id, card_type, number_filtered, expiry_date FROM customers_braintree_tokens WHERE customers_id = " . (int)$_SESSION['customer_id'] . " ORDER BY date_added");

  if ( tep_db_num_rows($tokens_query) > 0 ) {
    while ( $tokens = tep_db_fetch_array($tokens_query) ) {
?>

      <div class="col-sm-6"><strong><?php echo tep_output_string_protected($tokens['card_type']); ?></strong>&nbsp;&nbsp;****<?php echo tep_output_string_protected($tokens['number_filtered']) . '&nbsp;&nbsp;' . tep_output_string_protected(substr($tokens['expiry_date'], 0, 2) . '/' . substr($tokens['expiry_date'], 2)); ?></p>
      <div class="col-sm-6 text-right"><?php echo tep_draw_button(SMALL_IMAGE_BUTTON_DELETE, 'fas fa-trash', tep_href_link('ext/modules/content/account/braintree/cards.php', 'action=delete&id=' . (int)$tokens['id'] . '&formid=' . md5($_SESSION['sessiontoken']))); ?></span>

<?php
    }
  } else {
?>

    <div class="alert alert-danger col"><?php echo MODULE_CONTENT_ACCOUNT_BRAINTREE_CARDS_TEXT_NO_CARDS; ?></div>

<?php
  }
?>

  </div>

  <div class="buttonSet">
    <?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('account.php')); ?>
  </div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
