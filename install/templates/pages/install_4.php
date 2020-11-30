<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  osc_db_connect(trim($_POST['DB_SERVER']), trim($_POST['DB_SERVER_USERNAME']), trim($_POST['DB_SERVER_PASSWORD']));
  osc_db_select_db(trim($_POST['DB_DATABASE']));

  osc_db_query("UPDATE configuration SET configuration_value = '" . trim($_POST['CFG_STORE_NAME']) . "' WHERE configuration_key = 'STORE_NAME'");
  osc_db_query("UPDATE configuration SET configuration_value = '" . trim($_POST['CFG_STORE_OWNER_NAME']) . "' WHERE configuration_key = 'STORE_OWNER'");
  osc_db_query("UPDATE configuration SET configuration_value = '" . trim($_POST['CFG_STORE_OWNER_EMAIL_ADDRESS']) . "' WHERE configuration_key = 'STORE_OWNER_EMAIL_ADDRESS'");

  if ( !empty($_POST['CFG_ADMINISTRATOR_USERNAME']) ) {
    osc_db_query(sprintf(<<<'EOSQL'
INSERT INTO administrators (user_name, user_password) VALUES ('%s', '%s')
 ON DUPLICATE KEY UPDATE user_password = VALUES(user_password)
EOSQL
      , trim($_POST['CFG_ADMINISTRATOR_USERNAME']), osc_encrypt_password(trim($_POST['CFG_ADMINISTRATOR_PASSWORD']))));
  }

  $dir_fs_document_root = $_POST['DIR_FS_DOCUMENT_ROOT'];
  if ((substr($dir_fs_document_root, -1) != '\\') && (substr($dir_fs_document_root, -1) != '/')) {
    if (false === strrpos($dir_fs_document_root, '\\')) {
      $dir_fs_document_root .= '/';
    } else {
      $dir_fs_document_root .= '\\';
    }
  }

  osc_db_query("UPDATE configuration SET configuration_value = '" . $dir_fs_document_root . "includes/work/' WHERE configuration_key = 'DIR_FS_CACHE'");
  osc_db_query("UPDATE configuration SET configuration_value = '" . $dir_fs_document_root . "includes/work/' WHERE configuration_key = 'SESSION_WRITE_DIRECTORY'");

  if ($handle = opendir($dir_fs_document_root . 'includes/work/')) {
    while (false !== ($filename = readdir($handle))) {
      if ('cache' === pathinfo($filename, PATHINFO_EXTENSION)) {
        @unlink($dir_fs_document_root . 'includes/work/' . $filename);
      }
    }

    closedir($handle);
  }

  $http_url = parse_url($_POST['HTTP_WWW_ADDRESS']);
  $http_server = $http_url['scheme'] . '://' . $http_url['host'];
  $http_catalog = $http_url['path'];
  if (!empty($http_url['port'])) {
    $http_server .= ':' . $http_url['port'];
  }

  if (substr($http_catalog, -1) != '/') {
    $http_catalog .= '/';
  }

  $http_cookie_domain = $http_url['host'];
  if ('on' === getenv('HTTPS')) {
    $secure = "\n    'secure' => true,";
  } else {
    $secure = '';
  }

  $admin_folder = 'admin';
  if (!empty($_POST['CFG_ADMIN_DIRECTORY']) && osc_is_writable($dir_fs_document_root) && osc_is_writable($dir_fs_document_root . 'admin')) {
    $admin_folder = preg_replace('/[^a-zA-Z0-9]/', '', trim($_POST['CFG_ADMIN_DIRECTORY']));

    if (empty($admin_folder)) {
      $admin_folder = 'admin';
    }
  }

  if (isset($_POST['CFG_TIME_ZONE'])) {
    $time_zone = "'" . trim($_POST['CFG_TIME_ZONE']) . "'";
  } else {
    $time_zone = 'date_default_timezone_get()';
  }

  $db_server = trim($_POST['DB_SERVER']);
  $db_username = trim($_POST['DB_SERVER_USERNAME']);
  $db_password = trim($_POST['DB_SERVER_PASSWORD']);
  $db_database = trim($_POST['DB_DATABASE']);

  $file_contents = <<<"EOPHP"
<?php
// set the level of error reporting
  error_reporting(E_ALL);

  const HTTP_SERVER = '$http_server';
  const COOKIE_OPTIONS = [
    'lifetime' => 0,
    'domain' => '$http_cookie_domain',
    'path' => '$http_catalog',
    'samesite' => 'Lax',$secure
  ];
  const DIR_WS_CATALOG = '$http_catalog';

  const DIR_FS_CATALOG = '$dir_fs_document_root';

  date_default_timezone_set($time_zone);

// If you are asked to provide configure.php details
// please remove the data below before sharing
  const DB_SERVER = '$db_server';
  const DB_SERVER_USERNAME = '$db_username';
  const DB_SERVER_PASSWORD = '$db_password';
  const DB_DATABASE = '$db_database';

EOPHP;

  $fp = fopen($dir_fs_document_root . 'includes/configure.php', 'w');
  fputs($fp, $file_contents);
  fclose($fp);

  @chmod($dir_fs_document_root . 'includes/configure.php', 0644);

  $file_contents = <<<"EOPHP"
<?php
// set the level of error reporting
  error_reporting(E_ALL);

  const HTTP_SERVER = '$http_server';
  const COOKIE_OPTIONS = [
    'lifetime' => 0,
    'domain' => '$http_cookie_domain',
    'path' => '$http_catalog$admin_folder',
    'samesite' => 'Lax',$secure
  ];
  const DIR_WS_ADMIN = '$http_catalog$admin_folder/';

  const DIR_FS_DOCUMENT_ROOT = '$dir_fs_document_root';
  const DIR_FS_ADMIN = '$dir_fs_document_root$admin_folder/';
  const DIR_FS_BACKUP = DIR_FS_ADMIN . 'backups/';

  const HTTP_CATALOG_SERVER = '$http_server';
  const DIR_WS_CATALOG = '$http_catalog';
  const DIR_FS_CATALOG = '$dir_fs_document_root';

  date_default_timezone_set($time_zone);

// If you are asked to provide configure.php details
// please remove the data below before sharing
  const DB_SERVER = '$db_server';
  const DB_SERVER_USERNAME = '$db_username';
  const DB_SERVER_PASSWORD = '$db_password';
  const DB_DATABASE = '$db_database';

EOPHP;

  $fp = fopen($dir_fs_document_root . 'admin/includes/configure.php', 'w');
  fputs($fp, $file_contents);
  fclose($fp);

  @chmod($dir_fs_document_root . 'admin/includes/configure.php', 0644);

  if ($admin_folder != 'admin') {
    @rename($dir_fs_document_root . 'admin', $dir_fs_document_root . $admin_folder);
  }
?>

<div class="row">
  <div class="col-sm-9">
    <div class="alert alert-info" role="alert">
      <h1>Finished!</h1>

      <p>The installation of your online store was successful! Click on either button to start your online selling experience:</p>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card mb-2">
      <div class="card-body">
        <ol>
          <li class="text-muted">Database Server</li>
          <li class="text-muted">Web Server</li>
          <li class="text-muted">Online Store Settings</li>
          <li class="text-success"><strong>Finished!</strong></li>
        </ol>
      </div>
      <div class="text-footer">
        <div class="progress">
          <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">100%</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="w-100"></div>

<div class="row">
  <div class="col-12 col-sm-9">
    <div class="row">
      <div class="col"><?= osc_draw_button('Admin (Backend)', '<i class="fas fa-lock mr-2"></i>', $http_server . $http_catalog . $admin_folder . '/index.php', 'primary', ['newwindow' => 1], 'btn-info btn-block') ?></div>
      <div class="col"><?= osc_draw_button('Store (Frontend)', '<i class="fas fa-shopping-cart mr-2"></i>', $http_server . $http_catalog . 'index.php', 'primary', ['newwindow' => 1], 'btn-success btn-block') ?></div>
      <div class="col"><?= osc_draw_button('Phoenix Club', '<img src="images/icon_phoenix.png" class="mr-2">', 'https://forums.oscommerce.com/clubs/1-phoenix/', 'primary', ['newwindow' => 1], 'btn-dark btn-block') ?></div>
    </div>
  </div>

  <div class="col-12 col-sm-3">
    <h4>Step 4</h4>
    <div class="card mb-2">
      <div class="card-body">
        <p>Congratulations on installing and configuring OSCOM CE Phoenix as your online store solution!</p>
        <p>We wish you all the best with the success of your online store.  Please join and participate in our community.</p>
        <p><?= osc_draw_button('Phoenix Club', '<img src="images/icon_phoenix.png" class="mr-2">', 'https://forums.oscommerce.com/clubs/1-phoenix/', 'primary', ['newwindow' => 1], 'btn-dark btn-block') ?></p>
      </div>
      <div class="card-footer">
        - <a class="card-link" href="https://forums.oscommerce.com/clubs/1-phoenix/" target="_blank" rel="noreferrer">The Phoenix Team</a>
      </div>
    </div>
  </div>
</div>
