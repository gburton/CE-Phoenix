<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  include 'includes/application_top.php';

  if (!isset($_SESSION['customer_id'])) die;

// Check download.php was called with proper GET parameters
  if (!is_numeric($_GET['order'] ?? null) || !is_numeric($_GET['id'] ?? null) ) {
    die;
  }
  
// Check that order_id, customer_id and filename match
  $downloads_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT opd.orders_products_filename
  FROM orders o
    INNER JOIN orders_products op ON o.orders_id = op.orders_id
    INNER JOIN orders_products_download opd ON op.orders_products_id = opd.orders_products_id
    INNER JOIN orders_status os ON o.orders_status = os.orders_status_id
  WHERE opd.orders_products_filename != ''
    AND os.downloads_flag = 1
    AND (opd.download_maxdays = 0 OR o.date_purchased >= DATE_SUB(NOW(), INTERVAL opd.download_maxdays DAY))
    AND opd.download_count > 0
    AND o.customers_id = %d
    AND o.orders_id = %d
    AND opd.orders_products_download_id = %d
    AND os.language_id = %d
EOSQL
    , (int)$_SESSION['customer_id'], (int)$_GET['order'], (int)$_GET['id'], (int)$_SESSION['languages_id']));
  if (!tep_db_num_rows($downloads_query)) die;
  $downloads = tep_db_fetch_array($downloads_query);

// Die if file is not there
  if (!file_exists(DIR_FS_CATALOG . 'download/' . $downloads['orders_products_filename'])) die;
  
// Now decrement counter
  tep_db_query("UPDATE orders_products_download SET download_count = download_count-1 WHERE orders_products_download_id = " . (int)$_GET['id']);

// Returns a random name, 16 to 20 characters long
// There are more than 10^28 combinations
// The directory is "hidden", i.e. starts with '.'
function tep_random_name() {
  $letters = 'abcdefghijklmnopqrstuvwxyz';
  $dirname = '.';
  $length = floor(tep_rand(16,20));
  for ($i = 1; $i <= $length; $i++) {
   $q = floor(tep_rand(1,26));
   $dirname .= $letters[$q];
  }
  return $dirname;
}

// Unlinks all subdirectories and files in $dir
// Works only on one subdir level, will not recurse
function tep_unlink_temp_dir($dir) {
  $h1 = opendir($dir);
  while ($subdir = readdir($h1)) {
// Ignore non directories
    if (!is_dir($dir . $subdir)) continue;
// Ignore . and .. and CVS
    if ($subdir == '.' || $subdir == '..' || $subdir == 'CVS') continue;
// Loop and unlink files in subdirectory
    $h2 = opendir($dir . $subdir);
    while ($file = readdir($h2)) {
      if ($file == '.' || $file == '..') continue;
      @unlink($dir . $subdir . '/' . $file);
    }
    closedir($h2); 
    @rmdir($dir . $subdir);
  }
  closedir($h1);
}


// Now send the file with header() magic
  header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
  header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");
  header("Content-Type: Application/octet-stream");
  header("Content-disposition: attachment; filename=" . $downloads['orders_products_filename']);

  if (DOWNLOAD_BY_REDIRECT == 'true') {
// This will work only on Unix/Linux hosts
    tep_unlink_temp_dir('pub/');
    $tempdir = tep_random_name();
    umask(0000);
    mkdir('pub/' . $tempdir, 0777);
    symlink(DIR_FS_CATALOG . 'download/' . $downloads['orders_products_filename'], 'pub/' . $tempdir . '/' . $downloads['orders_products_filename']);
    if (file_exists('pub/' . $tempdir . '/' . $downloads['orders_products_filename'])) {
      tep_redirect(tep_href_link('pub/' . $tempdir . '/' . $downloads['orders_products_filename']));
    }
  }

// Fallback to readfile() delivery method. This will work on all systems, but will need considerable resources
  readfile(DIR_FS_CATALOG . 'download/' . $downloads['orders_products_filename']);
?>
