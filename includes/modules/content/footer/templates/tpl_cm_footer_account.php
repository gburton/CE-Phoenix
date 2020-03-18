<div class="col-sm-6 col-md-<?php echo $content_width; ?> cm-footer-account">
  <h4><?php echo MODULE_CONTENT_FOOTER_ACCOUNT_HEADING_TITLE; ?></h4>
  <nav class="nav nav-pills flex-column">

<?php
  if ( isset($_SESSION['customer_id']) ) {
?>
    <a class="nav-link pl-0" href="<?php echo tep_href_link('account.php', '', 'SSL'); ?>"><?php echo MODULE_CONTENT_FOOTER_ACCOUNT_BOX_ACCOUNT; ?></a>
    <a class="nav-link pl-0" href="<?php echo tep_href_link('address_book.php', '', 'SSL'); ?>"><?php echo MODULE_CONTENT_FOOTER_ACCOUNT_BOX_ADDRESS_BOOK; ?></a>
    <a class="nav-link pl-0" href="<?php echo tep_href_link('account_history.php', '', 'SSL'); ?>"><?php echo MODULE_CONTENT_FOOTER_ACCOUNT_BOX_ORDER_HISTORY; ?></a>
    <a class="nav-link mt-2 btn btn-danger btn-block" role="button" href="<?php echo tep_href_link('logoff.php', '', 'SSL'); ?>"><i class="fas fa-sign-out-alt"></i> <?php echo MODULE_CONTENT_FOOTER_ACCOUNT_BOX_LOGOFF; ?></a>

<?php
    } else {
?>
    <a class="nav-link pl-0" href="<?php echo tep_href_link('create_account.php', '', 'SSL'); ?>"><?php echo MODULE_CONTENT_FOOTER_ACCOUNT_BOX_CREATE_ACCOUNT; ?></a>
    <a class="nav-link mt-2 btn btn-success btn-block" role="button" href="<?php echo tep_href_link('login.php', '', 'SSL'); ?>"><i class="fas fa-sign-in-alt"></i> <?php echo MODULE_CONTENT_FOOTER_ACCOUNT_BOX_LOGIN; ?></a>

<?php
    }
?>
  </nav>
</div>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>
