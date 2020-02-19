<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>

  <p class="text-right"><?php echo FORM_REQUIRED_INFORMATION; ?></p>

  <div class="contentText">

<?php
  if (is_numeric($_GET['edit'] ?? null)) {
    $customer_data->display_input($customer_data->get_fields_for_page('address_book'), $customer->fetch_to_address($_GET['edit']));
  } else {
    $customer_data->display_input($customer_data->get_fields_for_page('address_book'));
  }

  if ( !isset($_GET['edit']) || ($customer->get_default_address_id() != $_GET['edit']) ) {
?>

      <div class="form-group row">
        <label for="primary" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo SET_AS_PRIMARY; ?></label>
        <div class="col-sm-9">
          <div class="checkbox">
            <label><?php echo tep_draw_checkbox_field('primary', 'on', false, 'id="primary"'); ?></label>
          </div>
        </div>
      </div>

<?php
  }
?>
  </div>
