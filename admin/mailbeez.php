<?php
/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010 - 2014 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License

 */


///////////////////////////////////////////////////////////////////////////////
///																			 //
///                 MailBeez Core file - do not edit                         //
///                                                                          //
///////////////////////////////////////////////////////////////////////////////

require('includes/application_top.php');

if (substr(DIR_FS_CATALOG, -1) != '/') {
    define('MH_DIR_FS_CATALOG', DIR_FS_CATALOG . '/');
} else {
    define('MH_DIR_FS_CATALOG', DIR_FS_CATALOG);
}
if (substr(DIR_WS_CATALOG, -1) != '/') {
    define('MH_DIR_WS_CATALOG', DIR_WS_CATALOG . '/');
} else {
    define('MH_DIR_WS_CATALOG', DIR_WS_CATALOG);
}

if (isset($_POST['cloudloader_mode']) || isset($_GET['cloudloader_mode'])) {
    // installer entrypoint
    @include(MH_DIR_FS_CATALOG . 'mailhive/cloudbeez/dev_environment.php');
    require_once(MH_DIR_FS_CATALOG . 'mailhive/cloudbeez/cloudloader/bootstrap/inc_mailbeez.php');
} else {
    if (file_exists(MH_DIR_FS_CATALOG . 'mailhive/common/main/inc_mailbeez.php')) {
        // mailbeez installed
        require_once(MH_DIR_FS_CATALOG . 'mailhive/common/main/inc_mailbeez.php');
    } else {
        // not yet installed, load installer
        @include(MH_DIR_FS_CATALOG . 'mailhive/common/local/devsettings.php');
        // Please install MailBeez
        if (defined('MAILBEEZ_INSTALLER_DISABLED') && MAILBEEZ_INSTALLER_DISABLED) {
            echo "installer disabled";
        } else {
            require_once(MH_DIR_FS_CATALOG . 'mailhive/cloudbeez/cloudloader/bootstrap/inc_cloudloader_core_bootstrap.php');
        }
    }
}

?>