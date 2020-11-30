<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $OSCOM_Hooks->register_pipeline('progress');

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('checkout_shipping.php'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('checkout_payment.php'));

  require $oscTemplate->map_to_template('template_top.php', 'component');

  echo $payment_modules->javascript_validation();
?>

<h1 class="display-4"><?= HEADING_TITLE; ?></h1>

<?= tep_draw_form('checkout_payment', tep_href_link('checkout_confirmation.php'), 'post', 'onsubmit="return check_form();"', true);

  if (isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error())) {
    echo '<div class="alert alert-danger">' . "\n";
    echo '<p class="lead"><b>' . htmlspecialchars($error['title']) . "</b></p>\n";
    echo '<p>' . htmlspecialchars($error['error']) . "</p>\n";
    echo '</div>';
  }

  $selection = $payment_modules->selection();
?>

  <div class="row">
    <div class="col-sm-7">
      <h5 class="mb-1"><?= TABLE_HEADING_PAYMENT_METHOD; ?></h5>
      <div>
        <table class="table border-right border-left border-bottom table-hover m-0">
          <?php
          foreach ($selection as $choice) {
            ?>
            <tr class="table-selection">
              <td><label for="p_<?= $choice['id']; ?>"><?= $choice['module']; ?></label></td>
              <td class="text-right">
                <?php
                if (count($selection) > 1) {
                  echo '<div class="custom-control custom-radio custom-control-inline">';
                  echo tep_draw_radio_field('payment', $choice['id'], ($choice['id'] === ($_SESSION['payment'] ?? null)), 'id="p_' . $choice['id'] . '" required aria-required="true" class="custom-control-input"');
                  echo '<label class="custom-control-label" for="p_' . $choice['id'] . '">&nbsp;</label>';
                  echo '</div>';
                } else {
                  echo tep_draw_hidden_field('payment', $choice['id']);
                }
                ?>
              </td>
            </tr>
          <?php
          if (isset($choice['error'])) {
            ?>
          <tr>
            <td colspan="2"><?= $choice['error']; ?></td>
          </tr>
          <?php
            } elseif (isset($choice['fields']) && is_array($choice['fields'])) {
              foreach ($choice['fields'] as $field) {
              ?>
              <tr>
                <td><?= $field['title']; ?></td>
                <td><?= $field['field']; ?></td>
              </tr>
              <?php
              }
            }
          }
          ?>
        </table>

        <?php
        if (count($selection) == 1) {
          echo '<p class="m-2 font-weight-lighter">' . TEXT_ENTER_PAYMENT_INFORMATION . "</p>\n";
        }
        ?>
      </div>
    </div>
    <div class="col-sm-5">
      <h5 class="mb-1">
        <?php
        echo TABLE_HEADING_BILLING_ADDRESS;
        printf(LINK_TEXT_EDIT, 'font-weight-lighter ml-3', tep_href_link('checkout_payment_address.php'));
        ?>
      </h5>
      <div class="border">
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><?= PAYMENT_FA_ICON . $customer->make_address_label($_SESSION['billto'], true, ' ', '<br>'); ?>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <hr>

  <div class="form-group row">
    <label for="inputComments" class="col-form-label col-sm-4 text-sm-right"><?= ENTRY_COMMENTS; ?></label>
    <div class="col-sm-8"><?= tep_draw_textarea_field('comments', 'soft', 60, 5, ($_SESSION['comments'] ?? null), 'id="inputComments" placeholder="' . ENTRY_COMMENTS_PLACEHOLDER . '"'); ?></div>
  </div>

  <?= $OSCOM_Hooks->call('siteWide', 'injectFormDisplay'); ?>

  <div class="buttonSet">
    <div class="text-right"><?= tep_draw_button(BUTTON_CONTINUE_CHECKOUT_PROCEDURE, 'fas fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
  </div>

  <div class="progressBarHook">

  <?php
  $parameters = ['style' => 'progress-bar progress-bar-striped progress-bar-animated bg-info', 'markers' => ['position' => 2, 'min' => 0, 'max' => 100, 'now' => 67]];
  echo $OSCOM_Hooks->call('progress', 'progressBar', $parameters);
  ?>

  </div>

</form>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
