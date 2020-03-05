<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!isset($_SESSION['customer_id'])) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }
  $OSCOM_Hooks->call('gdpr', 'injectRedirect'); 

  $port_my_data = array();
  $OSCOM_Hooks->call('gdpr', 'injectData');

  require "includes/languages/$language/gdpr.php";

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('gdpr.php'));
  
  $page_content = $oscTemplate->getContent('gdpr');
  
  $OSCOM_Hooks->call('gdpr', 'portData');
  
  require('includes/template_top.php');

?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">
  <div class="row">
    <?php echo $page_content; ?>
  </div>  
</div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
