<?php
/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010 - 2013 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License

 */
if (file_exists('mailhive/configbeez/config_shopvoting/includes/inc_shopvoting.php')) {
    require_once('mailhive/configbeez/config_shopvoting/includes/inc_shopvoting.php');
} else {
    ?>
    Please install Shopvoting module
<?php
}
?>