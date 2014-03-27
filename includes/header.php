<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  if ($messageStack->size('header') > 0) {
    echo '<div class="col-md-12">' . $messageStack->output('header') . '</div>';
  }
?>

<nav class="navbar navbar-inverse navbar-no-corners navbar-no-margin" role="navigation">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse-1">
      <span class="sr-only"><?php echo HEADER_TOGGLE_NAV; ?></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
  </div>
  <div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
    <div class="container-fluid">
      <ul class="nav navbar-nav">
        <?php echo '<li><a class="store-brand" href="' . tep_href_link(FILENAME_DEFAULT) . '">' . HEADER_HOME . '</a></li>'; ?>
        <?php echo '<li><a href="' . tep_href_link(FILENAME_PRODUCTS_NEW) . '">' . HEADER_WHATS_NEW . '</a></li>'; ?>
        <?php echo '<li><a href="' . tep_href_link(FILENAME_SPECIALS) . '">' . HEADER_SPECIALS . '</a></li>'; ?>
        <?php echo '<li><a href="' . tep_href_link(FILENAME_REVIEWS) . '">' . HEADER_REVIEWS . '</a></li>'; ?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo HEADER_SITE_SETTINGS; ?></a>
          <ul class="dropdown-menu">
            <li class="text-center text-muted bg-primary"><?php echo sprintf(USER_LOCALIZATION, ucwords($language), $currency); ?></li>
            <?php
            if (substr(basename($PHP_SELF), 0, 8) != 'checkout') {
              // languages
              if (!isset($lng) || (isset($lng) && !is_object($lng))) {
                include(DIR_WS_CLASSES . 'language.php');
                $lng = new language;
              }
              if (count($lng->catalog_languages) > 1) {
                echo '<li class="divider"></li>';
                reset($lng->catalog_languages);
                while (list($key, $value) = each($lng->catalog_languages)) {
                  echo '<li><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('language', 'currency')) . 'language=' . $key, $request_type) . '">' . tep_image(DIR_WS_LANGUAGES .  $value['directory'] . '/images/' . $value['image'], $value['name']) . '</a></li>';
                }
              }
              // currencies
              if (isset($currencies) && is_object($currencies) && (count($currencies->currencies) > 1)) {
                echo '<li class="divider"></li>';
                reset($currencies->currencies);
                $currencies_array = array();
                while (list($key, $value) = each($currencies->currencies)) {
                  $currencies_array[] = array('id' => $key, 'text' => $value['title']);
                  echo '<li><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('language', 'currency')) . 'currency=' . $key, $request_type) . '">' . $value['title'] . '</a></li>';

                }
              }
            }
            ?>
          </ul>
        </li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo (tep_session_is_registered('customer_id')) ? sprintf(HEADER_ACCOUNT_LOGGED_IN, $customer_first_name) : HEADER_ACCOUNT_LOGGED_OUT; ?></a>
          <ul class="dropdown-menu">
            <?php
            if (tep_session_is_registered('customer_id')) {
              echo '<li><a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . '">' . HEADER_ACCOUNT_LOGOFF . '</a>';
            }
            else {
               echo '<li><a href="' . tep_href_link(FILENAME_LOGIN, '', 'SSL') . '">' . HEADER_ACCOUNT_LOGIN . '</a>';
               echo '<li><a href="' . tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL') . '">' . HEADER_ACCOUNT_REGISTER . '</a>';
            }
            ?>
            <li class="divider"></li>
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . HEADER_ACCOUNT . '</a>'; ?></li>
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . HEADER_ACCOUNT_HISTORY . '</a>'; ?></li>
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . HEADER_ACCOUNT_ADDRESS_BOOK . '</a>'; ?></li>
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL') . '">' . HEADER_ACCOUNT_PASSWORD . '</a>'; ?></li>
          </ul>
        </li>
        <?php
        if ($cart->count_contents() > 0) {
          ?>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo sprintf(HEADER_CART_CONTENTS, $cart->count_contents()); ?></a>
            <ul class="dropdown-menu">
              <li><?php echo '<a href="' . tep_href_link(FILENAME_SHOPPING_CART) . '">' . sprintf(HEADER_CART_HAS_CONTENTS, $cart->count_contents(), $currencies->format($cart->show_total())) . '</a>'; ?></li>
              <?php
              if ($cart->count_contents() > 0) {
                echo '<li class="divider"></li>';
                echo '<li><a href="' . tep_href_link(FILENAME_SHOPPING_CART) . '">' . HEADER_CART_VIEW_CART . '</a></li>';
              }
              ?>
            </ul>
          </li>
          <?php
          echo '<li><a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '">' . HEADER_CART_CHECKOUT . '</a></li>';
        }
        else {
          echo '<li class="nav navbar-text">' . HEADER_CART_NO_CONTENTS . '</li>';
        }
        ?>
      </ul>
    </div>
  </div>
</nav>

<div class="clearfix"></div>

<div id="header">
  <div class="col-sm-6"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image(DIR_WS_IMAGES . 'store_logo.png', STORE_NAME) . '</a>'; ?></div>

  <div class="col-sm-6 text-right">
    <div class="btn-group">
<?php
  echo tep_draw_button(HEADER_TITLE_CART_CONTENTS . ($cart->count_contents() > 0 ? ' (' . $cart->count_contents() . ')' : ''), 'glyphicon-shopping-cart', tep_href_link(FILENAME_SHOPPING_CART)) .
       tep_draw_button(HEADER_TITLE_CHECKOUT, 'glyphicon-chevron-right', tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL')) .
       tep_draw_button(HEADER_TITLE_MY_ACCOUNT, 'glyphicon-user', tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));

  if (tep_session_is_registered('customer_id')) {
    echo tep_draw_button(HEADER_TITLE_LOGOFF, 'glyphicon-log-out', tep_href_link(FILENAME_LOGOFF, '', 'SSL'));
  }
?>
    </div>
  </div>
</div>

<div class="clearfix"></div>

<div class="col-xs-12"><?php echo $breadcrumb->trail(); ?></div>

<?php
  if (isset($HTTP_GET_VARS['error_message']) && tep_not_null($HTTP_GET_VARS['error_message'])) {
?>
<div class="clearfix"></div>
<div class="col-xs-12">
  <div class="alert alert-danger">
    <a href="#" class="close glyphicon glyphicon-remove" data-dismiss="alert"></a>
    <?php echo htmlspecialchars(stripslashes(urldecode($HTTP_GET_VARS['error_message']))); ?>
  </div>
</div>
<?php
  }

  if (isset($HTTP_GET_VARS['info_message']) && tep_not_null($HTTP_GET_VARS['info_message'])) {
?>
<div class="clearfix"></div>
<div class="col-xs-12">
  <div class="alert alert-info">
    <a href="#" class="close glyphicon glyphicon-remove" data-dismiss="alert"></a>
    <?php echo htmlspecialchars(stripslashes(urldecode($HTTP_GET_VARS['info_message']))); ?>
  </div>
</div>
<?php
  }
?>
