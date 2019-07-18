<li class="nav-item dropdown nb-account"> 
  <a class="nav-link dropdown-toggle" href="#" id="navDropdownAccount" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?php echo (tep_session_is_registered('customer_id')) ? sprintf(MODULE_NAVBAR_ACCOUNT_LOGGED_IN, $customer_first_name) : MODULE_NAVBAR_ACCOUNT_LOGGED_OUT; ?>
  </a>
  <div class="dropdown-menu<?php echo $menu_align; ?>" aria-labelledby="navDropdownAccount">
    <?php
    if (tep_session_is_registered('customer_id')) {
      echo '<a class="dropdown-item" href="' . tep_href_link('logoff.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT_LOGOFF . '</a>';
    }
    else {
      echo '<a class="dropdown-item" href="' . tep_href_link('login.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT_LOGIN . '</a>';
      echo '<a class="dropdown-item" href="' . tep_href_link('create_account.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT_REGISTER . '</a>';
    }
    ?>
    <div class="dropdown-divider"></div>
    <?php echo '<a class="dropdown-item" href="' . tep_href_link('account.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT . '</a>'; ?>
    <?php echo '<a class="dropdown-item" href="' . tep_href_link('account_history.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT_HISTORY . '</a>'; ?>
    <?php echo '<a class="dropdown-item" href="' . tep_href_link('address_book.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT_ADDRESS_BOOK . '</a>'; ?>
    <?php echo '<a class="dropdown-item" href="' . tep_href_link('account_password.php', '', 'SSL') . '">' . MODULE_NAVBAR_ACCOUNT_PASSWORD . '</a>'; ?>
  </div>
</li>

<?php
/*
  Copyright (c) 2018, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
?>
