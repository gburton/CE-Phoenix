<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_notifications.php', '', 'SSL'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php echo tep_draw_form('account_notifications', tep_href_link('account_notifications.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process'); ?>

  <div class="alert alert-info" role="alert">
    <?php echo MY_NOTIFICATIONS_DESCRIPTION; ?>
  </div>

  <div class="form-group row align-items-center">
    <div class="col-form-label col-sm-4 text-left text-sm-right"><?php echo GLOBAL_NOTIFICATIONS_TITLE; ?></div>
    <div class="col-sm-8">
      <div class="custom-control custom-switch">
        <?php echo tep_draw_checkbox_field('product_global', '1', ($global['global_product_notifications'] == '1'), 'class="custom-control-input" id="inputGlobalNotification"');
        echo '<label for="inputGlobalNotification" class="custom-control-label">' . GLOBAL_NOTIFICATIONS_DESCRIPTION . '&nbsp;</label>';
        ?>
      </div>
    </div>
  </div>

<?php
  if ($global['global_product_notifications'] != '1') {
    $products_check_query = tep_db_query("SELECT COUNT(*) AS total FROM products_notifications WHERE customers_id = " . (int)$_SESSION['customer_id']);
    $products_check = tep_db_fetch_array($products_check_query);
    if ($products_check['total'] > 0) {
?>

    <div class="w-100"></div>
    <div class="alert alert-warning" role="alert"><?php echo NOTIFICATIONS_DESCRIPTION; ?></div>

    <div class="form-group row align-items-center">
      <div class="col-form-label col-sm-4 text-left text-sm-right"><?php echo MY_NOTIFICATIONS_TITLE; ?></div>
      <div class="col-sm-8">
        <?php
      $products_query = tep_db_query("SELECT pd.products_id, pd.products_name FROM products_description pd, products_notifications pn WHERE pn.customers_id = " . (int)$_SESSION['customer_id'] . " AND pn.products_id = pd.products_id AND pd.language_id = " . (int)$_SESSION['languages_id'] . " ORDER BY pd.products_name");
      while ($products = tep_db_fetch_array($products_query)) {
        echo '<div class="custom-control custom-switch">';
        echo tep_draw_checkbox_field('products[]', $products['products_id'], true, 'class="custom-control-input" id="input_' . $products['products_id'] . 'Notification"');
        echo '<label for="input_' . $products['products_id'] . 'Notification" class="custom-control-label">' . $products['products_name'] . '</label>';
        echo '</div>';
      }
?>
      </div>
    </div>

<?php
    } else {
?>

    <div class="alert alert-warning" role="alert">
      <?php echo NOTIFICATIONS_NON_EXISTING; ?>
    </div>

<?php
    }
  }
?>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_UPDATE_PREFERENCES, 'fas fa-users-cog', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('account.php', '', 'SSL')); ?></p>
  </div>

</form>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
