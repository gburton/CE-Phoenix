<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  $OSCOM_Hooks->register_pipeline('progress');

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

// if no shipping destination address was selected, use the customers own address as default
  if (!tep_session_is_registered('sendto')) {
    tep_session_register('sendto');
    $sendto = $customer_default_address_id;
  } else {
// verify the selected shipping address
    if ( (is_array($sendto) && empty($sendto)) || is_numeric($sendto) ) {
      $check_address_query = tep_db_query("select count(*) as total from address_book where customers_id = '" . (int)$customer_id . "' and address_book_id = '" . (int)$sendto . "'");
      $check_address = tep_db_fetch_array($check_address_query);

      if ($check_address['total'] != '1') {
        $sendto = $customer_default_address_id;
        if (tep_session_is_registered('shipping')) tep_session_unregister('shipping');
      }
    }
  }

  require('includes/classes/order.php');
  $order = new order;

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
  if (!tep_session_is_registered('cartID')) {
    tep_session_register('cartID');
  } elseif (($cartID != $cart->cartID) && tep_session_is_registered('shipping')) {
    tep_session_unregister('shipping');
  }

  $cartID = $cart->cartID = $cart->generate_cart_id();

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
  if ($order->content_type == 'virtual') {
    if (!tep_session_is_registered('shipping')) tep_session_register('shipping');
    $shipping = false;
    $sendto = false;
    tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
  }

  $total_weight = $cart->show_weight();
  $total_count = $cart->count_contents();

// load all enabled shipping modules
  require('includes/classes/shipping.php');
  $shipping_modules = new shipping;

  if ( defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true') ) {
    $pass = false;

    switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
      case 'national':
        if ($order->delivery['country_id'] == STORE_COUNTRY) {
          $pass = true;
        }
        break;
      case 'international':
        if ($order->delivery['country_id'] != STORE_COUNTRY) {
          $pass = true;
        }
        break;
      case 'both':
        $pass = true;
        break;
    }

    $free_shipping = false;

    if ( ($pass == true) && ($order->info['total'] >= MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER) ) {
      $free_shipping = true;

      include('includes/languages/' . $language . '/modules/order_total/ot_shipping.php');
    }
  } else {
    $free_shipping = false;
  }

// process the selected shipping method
  if ( isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $sessiontoken) ) {
    if (!tep_session_is_registered('comments')) tep_session_register('comments');
    if (tep_not_null($_POST['comments'])) {
      $comments = tep_db_prepare_input($_POST['comments']);
    }

    if (!tep_session_is_registered('shipping')) tep_session_register('shipping');

    if ( (tep_count_shipping_modules() > 0) || ($free_shipping == true) ) {
      if ( (isset($_POST['shipping'])) && (strpos($_POST['shipping'], '_')) ) {
        $shipping = $_POST['shipping'];

        list($module, $method) = explode('_', $shipping);
        if ( is_object($$module) || ($shipping == 'free_free') ) {
          if ($shipping == 'free_free') {
            $quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
            $quote[0]['methods'][0]['cost'] = '0';
          } else {
            $quote = $shipping_modules->quote($method, $module);
          }
          if (isset($quote['error'])) {
            tep_session_unregister('shipping');
          } else {
            if ( (isset($quote[0]['methods'][0]['title'])) && (isset($quote[0]['methods'][0]['cost'])) ) {
              $way = ''; 
              if (!empty($quote[0]['methods'][0]['title'])) {
                  $way = ' (' . $quote[0]['methods'][0]['title'] . ')'; 
              }
              $shipping = array('id' => $shipping,
                                'title' => (($free_shipping == true) ?  $quote[0]['methods'][0]['title'] : $quote[0]['module'] . $way), 
                                'cost' => $quote[0]['methods'][0]['cost']);

              tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
            }
          }
        } else {
          tep_session_unregister('shipping');
        }
      }
    } else {
      if ( defined('SHIPPING_ALLOW_UNDEFINED_ZONES') && (SHIPPING_ALLOW_UNDEFINED_ZONES == 'False') ) {
        tep_session_unregister('shipping');
      } else {
        $shipping = false;
        tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
      }
    }
  }

// get all available shipping quotes
  $quotes = $shipping_modules->quote();

// if no shipping method has been selected, automatically select the cheapest method.
// if the modules status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
  if ( !tep_session_is_registered('shipping') || ( tep_session_is_registered('shipping') && ($shipping == false) && (tep_count_shipping_modules() > 1) ) ) $shipping = $shipping_modules->cheapest();

  require('includes/languages/' . $language . '/checkout_shipping.php');

  if ( defined('SHIPPING_ALLOW_UNDEFINED_ZONES') && (SHIPPING_ALLOW_UNDEFINED_ZONES == 'False') && !    tep_session_is_registered('shipping') && ($shipping == false) ) {
  $messageStack->add_session('checkout_address', ERROR_NO_SHIPPING_AVAILABLE_TO_SHIPPING_ADDRESS);
  tep_redirect(tep_href_link('checkout_shipping_address.php', '', 'SSL'));
}

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('checkout_shipping.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('checkout_shipping.php', '', 'SSL'));

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php echo tep_draw_form('checkout_address', tep_href_link('checkout_shipping.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process'); ?>

<div class="contentContainer">
  <div class="row">
    <div class="col-sm-7">
      <h5 class="mb-1"><?php echo TABLE_HEADING_SHIPPING_METHOD; ?></h5>
      <div>
        <?php
        $num_modules = tep_count_shipping_modules();
        if ($num_modules > 0) {
          if ($free_shipping == true) {
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
              // gdpr - cant be checked by default?
              $checked = null; 
              
              for ($i=0, $n=sizeof($quotes); $i<$n; $i++) {
                for ($j=0, $n2=sizeof($quotes[$i]['methods']); $j<$n2; $j++) {
                  // set the radio button to be checked if it is the method chosen
                  // $checked = (($quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $shipping['id']) ? true : false);                                    
                ?>
                <tr class="table-selection">
                  <td>
                    <?php echo $quotes[$i]['module']; ?>
                    <?php
                    if (isset($quotes[$i]['icon']) && tep_not_null($quotes[$i]['icon'])) echo '&nbsp;' . $quotes[$i]['icon'];
                    
                    if (isset($quotes[$i]['error'])) {
                      echo '<div class="form-text">' . $quotes[$i]['error'] . '</div>';
                    }
                    
                    if (tep_not_null($quotes[$i]['methods'][$j]['title'])) echo '<div class="form-text">' . $quotes[$i]['methods'][$j]['title'] . '</div>';
                    ?>
                  </td>
                  <?php
                  if ( ($n > 1) || ($n2 > 1) ) {
                    ?>
                  <td class="text-right">
                    <?php
                    if (isset($quotes[$i]['error'])) {
                      echo '<div class="alert alert-error">' . $quotes[$i]['error'] . '</div>';
                    }
                    else {
                      echo '<div class="custom-control custom-radio custom-control-inline">';
                        echo tep_draw_radio_field('shipping',  $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'], $checked, 'id="d_' . $quotes[$i]['methods'][$j]['id'] . '" required aria-required="true" aria-describedby="d_' . $quotes[$i]['methods'][$j]['id'] . '" class="custom-control-input"');
                        echo '<label class="custom-control-label" for="d_' . $quotes[$i]['methods'][$j]['id'] . '">' . $currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))) . '</label>';
                      echo '</div>';
                
                    }
                    ?>
                  </td>
                  <?php
                  } 
                  else {
                    ?>
                    <td class="text-right"><?php echo $currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))) . tep_draw_hidden_field('shipping', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id']); ?>
                    </td>
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
          if ( ($free_shipping == false) && ($num_modules == 1) ) {
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
        echo sprintf(LINK_TEXT_EDIT, 'font-weight-lighter ml-3', 'checkout_shipping_address.php');      
        ?>
      </h5>
      <div class="border">
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <?php
            echo SHIPPING_FA_ICON;            
            echo tep_address_label($customer_id, $sendto, true, ' ', '<br />'); 
            ?>
          </li>
        </ul>
      </div>
    </div>
  </div>
  
  <hr>
  
  <div class="form-group row">
    <label for="inputComments" class="col-form-label col-sm-4 text-left text-sm-right"><?php echo ENTRY_COMMENTS; ?></label>
    <div class="col-sm-8">
      <?php
      echo tep_draw_textarea_field('comments', 'soft', 60, 5, $comments, 'id="inputComments" placeholder="' . ENTRY_COMMENTS_PLACEHOLDER . '"');
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
  $parameters = [
    'style' => 'progress-bar progress-bar-striped progress-bar-animated bg-info',
    'markers' => ['position' => 1, 'min' => 0, 'max' => 100, 'now' => 33],
  ];
  echo $OSCOM_Hooks->call('progress', 'progressBar', $parameters);
  ?>       
  
</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
