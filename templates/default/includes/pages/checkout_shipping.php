<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $OSCOM_Hooks->register_pipeline('progress');

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('checkout_shipping.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('checkout_shipping.php', '', 'SSL'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php echo tep_draw_form('checkout_address', tep_href_link('checkout_shipping.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process'); ?>

  <div class="row">
    <div class="col-sm-7">
      <h5 class="mb-1"><?php echo TABLE_HEADING_SHIPPING_METHOD; ?></h5>
      <div>
        <?php
  if ($module_count > 0) {
    if ($free_shipping) {
?>
        <div class="alert alert-info mb-0" role="alert">
          <p class="lead"><b><?php echo FREE_SHIPPING_TITLE; ?></b></p>
          <p class="lead"><?php echo sprintf(FREE_SHIPPING_DESCRIPTION, $currencies->format(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER)) . tep_draw_hidden_field('shipping', 'free_free'); ?></p>
        </div>
            <?php
    } else {
            ?>
        <table class="table border-right border-left border-bottom table-hover m-0">
          <?php
          // GDPR - can't be checked by default?
      $checked = null;

      $n = count($quotes);
      foreach ($quotes as $quote) {
        $n2 = count($quote['methods']);
        foreach (($quote['methods'] ?? []) as $method) {
          // set the radio button to be checked if it is the method chosen
          // $checked = (($quote['id'] . '_' . $method['id'] == $shipping['id']) ? true : false);
?>
          <tr class="table-selection">
            <td>
                  <?php
          echo $quote['module'];

          if (tep_not_null($quote['icon'] ?? '')) {
            echo '&nbsp;' . $quote['icon'];
          }

          if (isset($quote['error'])) {
            echo '<div class="form-text">' . $quote['error'] . '</div>';
          }

          if (tep_not_null($method['title'])) {
            echo '<div class="form-text">' . $method['title'] . '</div>';
          }
?>
            </td>
            <?php
          if ( ($n > 1) || ($n2 > 1) ) {
?>
            <td class="text-right">
              <?php
            if (isset($quote['error'])) {
              echo '<div class="alert alert-error">' . $quote['error'] . '</div>';
            } else {
              echo '<div class="custom-control custom-radio custom-control-inline">';
              echo tep_draw_radio_field('shipping',  $quote['id'] . '_' . $method['id'], $checked, 'id="d_' . $method['id'] . '" required aria-required="true" aria-describedby="d_' . $method['id'] . '" class="custom-control-input"');
              echo '<label class="custom-control-label" for="d_' . $method['id'] . '">' . $currencies->format(tep_add_tax($method['cost'], (isset($quote['tax']) ? $quote['tax'] : 0))) . '</label>';
              echo '</div>';
            }
?>
            </td>
              <?php
          } else {
?>
            <td class="text-right"><?php echo $currencies->format(tep_add_tax($method['cost'], (isset($quote['tax']) ? $quote['tax'] : 0))) . tep_draw_hidden_field('shipping', $quote['id'] . '_' . $method['id']); ?></td>
              <?php
          }
?>
          </tr>
          <?php
        }
      }
    }
?>
        </table>
        <?php
    if ( !$free_shipping && (1 === $module_count) ) {
?>
        <p class="m-2 font-weight-lighter"><?php echo TEXT_ENTER_SHIPPING_INFORMATION; ?></p>
          <?php
    }
  }
?>
      </div>
    </div>

    <div class="col-sm-5">
      <h5 class="mb-1">
        <?php
  echo TABLE_HEADING_SHIPPING_ADDRESS;
  echo sprintf(LINK_TEXT_EDIT, 'font-weight-lighter ml-3', tep_href_link('checkout_shipping_address.php', '', 'SSL'));
?>
      </h5>
      <div class="border">
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><?php echo SHIPPING_FA_ICON . $customer->make_address_label($_SESSION['sendto'], true, ' ', '<br>'); ?></li>
        </ul>
      </div>
    </div>
  </div>

  <hr>

  <div class="form-group row">
    <label for="inputComments" class="col-form-label col-sm-4 text-left text-sm-right"><?php echo ENTRY_COMMENTS; ?></label>
    <div class="col-sm-8">
      <?php
  echo tep_draw_textarea_field('comments', 'soft', 60, 5, ($_SESSION['comments'] ?? null), 'id="inputComments" placeholder="' . ENTRY_COMMENTS_PLACEHOLDER . '"');
?>
    </div>
  </div>

  <?php
  echo $OSCOM_Hooks->call('siteWide', 'injectFormDisplay');
?>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(BUTTON_CONTINUE_CHECKOUT_PROCEDURE, 'fas fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
  </div>

  <?php
  $parameters = ['style' => 'progress-bar progress-bar-striped progress-bar-animated bg-info', 'markers' => ['position' => 1, 'min' => 0, 'max' => 100, 'now' => 33]];
  echo $OSCOM_Hooks->call('progress', 'progressBar', $parameters);
?>

</form>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
