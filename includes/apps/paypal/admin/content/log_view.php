<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/
?>

<div class="row">
  <div class="col">
    <h1 class="display-4"><?= $OSCOM_PayPal->getDef('heading_log_view'); ?></h1>
  </div>
  <div class="col text-right">
    <h1 class="display-4"><?= $OSCOM_PayPal->drawButton($OSCOM_PayPal->getDef('button_back'), tep_href_link('paypal.php', 'action=log&page=' . $_GET['page']), 'info'); ?></h1>
  </div>
</div>

<table class="table table-hover">
  <thead class="thead-dark">
    <tr>
      <th colspan="2"><?php echo $OSCOM_PayPal->getDef('table_heading_entries_request'); ?></th>
    </tr>
  </thead>
  <tbody>

<?php
  foreach ( $log_request as $key => $value ) {
?>

    <tr>
      <td class="w-25"><?php echo tep_output_string_protected($key); ?></td>
      <td><?php echo tep_output_string_protected($value); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

<table class="table table-hover">
  <thead class="thead-dark">
    <tr>
      <th colspan="2"><?php echo $OSCOM_PayPal->getDef('table_heading_entries_response'); ?></th>
    </tr>
  </thead>
  <tbody>

<?php
  foreach ( $log_response as $key => $value ) {
?>

    <tr>
      <td class="w-25"><?php echo tep_output_string_protected($key); ?></td>
      <td><?php echo tep_output_string_protected($value); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>
