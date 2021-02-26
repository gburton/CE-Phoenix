<li class="nav-item dropdown nb-account">
  <a class="nav-link dropdown-toggle" href="#" id="navDropdownAccount" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?= $navbarAccountText ?>
  </a>
  <div class="dropdown-menu<?= (('Right' === MODULE_NAVBAR_ACCOUNT_CONTENT_PLACEMENT) ? ' dropdown-menu-right' : '') ?>" aria-labelledby="navDropdownAccount">
    <?php
    if (isset($_SESSION['customer_id'])) {
      echo '<a class="dropdown-item" href="' . tep_href_link('logoff.php') . '">' . MODULE_NAVBAR_ACCOUNT_LOGOFF . '</a>' . PHP_EOL;
    } else {
      echo '<a class="dropdown-item" href="' . tep_href_link('login.php') . '">' . MODULE_NAVBAR_ACCOUNT_LOGIN . '</a>' . PHP_EOL;
      echo '<a class="dropdown-item" href="' . tep_href_link('create_account.php') . '">' . MODULE_NAVBAR_ACCOUNT_REGISTER . '</a>' . PHP_EOL;
    }
    ?>
    <div class="dropdown-divider"></div>
    <?php
    echo '<a class="dropdown-item" href="' . tep_href_link('account.php') . '">' . MODULE_NAVBAR_ACCOUNT . '</a>' . PHP_EOL;
    echo '<a class="dropdown-item" href="' . tep_href_link('account_history.php') . '">' . MODULE_NAVBAR_ACCOUNT_HISTORY . '</a>' . PHP_EOL;
    echo '<a class="dropdown-item" href="' . tep_href_link('address_book.php') . '">' . MODULE_NAVBAR_ACCOUNT_ADDRESS_BOOK . '</a>' . PHP_EOL;
    echo '<a class="dropdown-item" href="' . tep_href_link('account_password.php') . '">' . MODULE_NAVBAR_ACCOUNT_PASSWORD . '</a>' . PHP_EOL;
    ?>
  </div>
</li>

<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/
?>
