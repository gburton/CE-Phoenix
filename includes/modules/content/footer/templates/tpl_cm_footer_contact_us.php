<div class="col-sm-6 col-md-<?php echo $content_width; ?> cm-footer-contact-us">
  <h4><?php echo MODULE_CONTENT_FOOTER_CONTACT_US_HEADING_TITLE; ?></h4>
  <address>
    <strong><?php echo STORE_NAME; ?></strong><br>
    <?php echo nl2br(STORE_ADDRESS); ?><br>
    <?php echo MODULE_CONTENT_FOOTER_CONTACT_US_PHONE . STORE_PHONE; ?><br>
    <?php echo MODULE_CONTENT_FOOTER_CONTACT_US_EMAIL . STORE_OWNER_EMAIL_ADDRESS; ?>
  </address>
  <ul class="list-unstyled">
    <li><a class="btn btn-success btn-block" role="button" href="<?php echo tep_href_link('contact_us.php'); ?>"><i class="fas fa-paper-plane"></i> <?php echo MODULE_CONTENT_FOOTER_CONTACT_US_EMAIL_LINK; ?></a></li>
  </ul>
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
