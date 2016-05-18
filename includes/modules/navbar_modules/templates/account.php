<li class="dropdown"> 
  <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo (tep_session_is_registered('customer_id')) ? sprintf(MODULE_NAVBAR_ACCOUNT_LOGGED_IN, $customer_first_name) : MODULE_NAVBAR_ACCOUNT_LOGGED_OUT; ?></a>
  <ul class="dropdown-menu">
    <?php
    if (tep_session_is_registered('customer_id')) {
      echo '<li><a href="' . tep_href_link('logoff.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT_LOGOFF . '</a></li>';
    }
    else {
      echo '<li><a href="' . tep_href_link('login.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT_LOGIN . '</a></li>';
      echo '<li><a href="' . tep_href_link('create_account.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT_REGISTER . '</a></li>';
    }
    ?>
    <li class="divider"></li>
    <li><?php echo '<a href="' . tep_href_link('account.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT . '</a>'; ?></li>
    <li><?php echo '<a href="' . tep_href_link('account_history.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT_HISTORY . '</a>'; ?></li>
    <li><?php echo '<a href="' . tep_href_link('address_book.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT_ADDRESS_BOOK . '</a>'; ?></li>
    <li><?php echo '<a href="' . tep_href_link('account_password.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT_PASSWORD . '</a>'; ?></li>
  </ul>
</li>