<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  require "includes/languages/$language/create_account_success.php";

  $breadcrumb->add(NAVBAR_TITLE_1);
  $breadcrumb->add(NAVBAR_TITLE_2);

  if (count($navigation->snapshot) > 0) {
    $origin_href = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], [session_name()]), $navigation->snapshot['mode']);
    $navigation->clear_snapshot();
  } else {
    $origin_href = tep_href_link('index.php');
  }

  $contact_us_href = tep_href_link('contact_us.php', '', 'SSL');

  require 'includes/template_top.php';
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">
  <div class="alert alert-success" role="alert">
    <?php echo sprintf(TEXT_ACCOUNT_CREATED, $contact_us_href, $contact_us_href); ?>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-angle-right', $origin_href, null, null, 'btn-success btn-block btn-lg'); ?></div>
  </div>
</div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
