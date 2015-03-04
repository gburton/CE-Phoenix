<nav class="navbar navbar-inverse navbar-no-corners navbar-no-margin" role="navigation">
  <div class="container-fluid">
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
          <?php
          if (substr(basename($PHP_SELF), 0, 8) != 'checkout') {
            ?>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo HEADER_SITE_SETTINGS; ?></a>
              <ul class="dropdown-menu">
                <li class="text-center text-muted bg-primary"><?php echo sprintf(USER_LOCALIZATION, ucwords($language), $currency); ?></li>
                <?php
                // languages
                if (!isset($lng) || (isset($lng) && !is_object($lng))) {
                 include(DIR_WS_CLASSES . 'language.php');
                  $lng = new language;
                }
                if (count($lng->catalog_languages) > 1) {
                  echo '<li class="divider"></li>';
                  reset($lng->catalog_languages);
                  while (list($key, $value) = each($lng->catalog_languages)) {
                    echo '<li><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('language', 'currency')) . 'language=' . $key, $request_type) . '">' . tep_image(DIR_WS_LANGUAGES .  $value['directory'] . '/images/' . $value['image'], $value['name'], null, null, null, false) . ' ' . $value['name'] . '</a></li>';
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
                ?>
              </ul>
            </li>
            <?php
          }
          ?>
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
  </div>
</nav>
