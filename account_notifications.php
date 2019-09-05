<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

// needs to be included earlier to set the success message in the messageStack
  require('includes/languages/' . $language . '/account_notifications.php');

  $global_query = tep_db_query("select global_product_notifications from customers_info where customers_info_id = '" . (int)$customer_id . "'");
  $global = tep_db_fetch_array($global_query);

  if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $sessiontoken)) {
    if (isset($_POST['product_global']) && is_numeric($_POST['product_global'])) {
      $product_global = tep_db_prepare_input($_POST['product_global']);
    } else {
      $product_global = '0';
    }

    (array)$products = $_POST['products'];

    if ($product_global != $global['global_product_notifications']) {
      $product_global = (($global['global_product_notifications'] == '1') ? '0' : '1');

      tep_db_query("update customers_info set global_product_notifications = '" . (int)$product_global . "' where customers_info_id = '" . (int)$customer_id . "'");
    } elseif (sizeof($products) > 0) {
      $products_parsed = array();
      foreach($products as $value) {
        if (is_numeric($value)) {
          $products_parsed[] = $value;
        }
      }

      if (sizeof($products_parsed) > 0) {
        $check_query = tep_db_query("select count(*) as total from products_notifications where customers_id = '" . (int)$customer_id . "' and products_id not in (" . implode(',', $products_parsed) . ")");
        $check = tep_db_fetch_array($check_query);

        if ($check['total'] > 0) {
          tep_db_query("delete from products_notifications where customers_id = '" . (int)$customer_id . "' and products_id not in (" . implode(',', $products_parsed) . ")");
        }
      }
    } else {
      $check_query = tep_db_query("select count(*) as total from products_notifications where customers_id = '" . (int)$customer_id . "'");
      $check = tep_db_fetch_array($check_query);

      if ($check['total'] > 0) {
        tep_db_query("delete from products_notifications where customers_id = '" . (int)$customer_id . "'");
      }
    }

    $messageStack->add_session('account', SUCCESS_NOTIFICATIONS_UPDATED, 'success');

    tep_redirect(tep_href_link('account.php', '', 'SSL'));
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_notifications.php', '', 'SSL'));

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php echo tep_draw_form('account_notifications', tep_href_link('account_notifications.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process'); ?>

<div class="contentContainer">

  <div class="alert alert-info" role="alert">
    <?php echo MY_NOTIFICATIONS_DESCRIPTION; ?>
  </div>

  <div class="form-group row align-items-center">
    <div class="col-form-label col-sm-4 text-left text-sm-right"><?php echo GLOBAL_NOTIFICATIONS_TITLE; ?></div>
    <div class="col-sm-8">
      <div class="custom-control custom-switch">
        <?php echo tep_draw_checkbox_field('product_global', '1', (($global['global_product_notifications'] == '1') ? true : false), 'class="custom-control-input" id="inputGlobalNotification"');
        echo '<label for="inputGlobalNotification" class="custom-control-label">' . GLOBAL_NOTIFICATIONS_DESCRIPTION . '&nbsp;</label>';      
        ?>
      </div>
    </div>
  </div>
    
<?php
  if ($global['global_product_notifications'] != '1') {
    $products_check_query = tep_db_query("select count(*) as total from products_notifications where customers_id = '" . (int)$customer_id . "'");
    $products_check = tep_db_fetch_array($products_check_query);
    if ($products_check['total'] > 0) {
?>

    <div class="w-100"></div>
    <div class="alert alert-warning" role="alert"><?php echo NOTIFICATIONS_DESCRIPTION; ?></div>

    <div class="form-group row align-items-center">
      <div class="col-form-label col-sm-4 text-left text-sm-right"><?php echo MY_NOTIFICATIONS_TITLE; ?></div>
      <div class="col-sm-8">
        <?php
        $products_query = tep_db_query("select pd.products_id, pd.products_name from products_description pd, products_notifications pn where pn.customers_id = '" . (int)$customer_id . "' and pn.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by pd.products_name");
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
  
</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
