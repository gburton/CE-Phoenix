<div class="col-sm-<?php echo $content_width; ?> cm-cs-thank-you">
  <h5 class="mb-1"><?php echo MODULE_CONTENT_CHECKOUT_SUCCESS_TEXT_THANKS_FOR_SHOPPING; ?></h5>
  
  <div class="border">
    <ul class="list-group list-group-flush">
      <li class="list-group-item bg-light"><?php echo MODULE_CONTENT_CHECKOUT_SUCCESS_TEXT_SUCCESS; ?></li>
    </ul>
  
    <div class="list-group list-group-flush">      
      <?php 
      echo sprintf(MODULE_CONTENT_CHECKOUT_SUCCESS_TEXT_SEE_ORDERS, tep_href_link('account_history.php', '', 'SSL'));
      echo sprintf(MODULE_CONTENT_CHECKOUT_SUCCESS_TEXT_CONTACT_STORE_OWNER, tep_href_link('contact_us.php')); 
      ?>
    </div>
  </div>
  
</div>

<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/
?>
