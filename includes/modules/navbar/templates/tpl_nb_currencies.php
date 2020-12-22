<li class="nav-item dropdown nb-currencies">
  <a class="nav-link dropdown-toggle" href="#" id="navDropdownCurrencies" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?= sprintf(MODULE_NAVBAR_CURRENCIES_SELECTED_CURRENCY, $_SESSION['currency']) ?>
  </a>
  <div class="dropdown-menu<?= (('Right' === MODULE_NAVBAR_CURRENCIES_CONTENT_PLACEMENT) ? ' dropdown-menu-right' : '') ?>" aria-labelledby="navDropdownCurrencies">
    <?php
    foreach ($GLOBALS['currencies']->currencies as $key => $value) {
      echo '<a class="dropdown-item" href="'
      . tep_href_link($GLOBALS['PHP_SELF'], tep_get_all_get_params(['language', 'currency']) . 'currency=' . $key, $GLOBALS['request_type']) . '">'
      . $value['title']
      . '</a>' . PHP_EOL;
    }
    ?>
  </div>
</li>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>