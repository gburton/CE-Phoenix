<div class="col-sm-<?php echo $content_width; ?> text-right cm-header-buttons">
  <div class="btn-group" role="group" aria-label="...">
    <?php
    echo tep_draw_button(MODULE_CONTENT_HEADER_BUTTONS_TITLE_CART_CONTENTS . ($cart->count_contents() > 0 ? ' (' . $cart->count_contents() . ')' : ''), 'fa fa-shopping-cart', tep_href_link('shopping_cart.php')) .
         tep_draw_button(MODULE_CONTENT_HEADER_BUTTONS_TITLE_CHECKOUT, 'fa fa-credit-card', tep_href_link('checkout_shipping.php', '', 'SSL')) .
         tep_draw_button(MODULE_CONTENT_HEADER_BUTTONS_TITLE_MY_ACCOUNT, 'fa fa-user', tep_href_link('account.php', '', 'SSL'));

    if ( tep_session_is_registered('customer_id') ) {
      echo tep_draw_button(MODULE_CONTENT_HEADER_BUTTONS_TITLE_LOGOFF, 'fas fa-sign-out-alt', tep_href_link('logoff.php', '', 'SSL'));
    }
    ?>
  </div>
</div>

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
