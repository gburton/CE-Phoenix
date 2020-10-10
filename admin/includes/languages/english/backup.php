<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Database Backup Manager');

define('TABLE_HEADING_TITLE', 'Title');
define('TABLE_HEADING_FILE_DATE', 'Date');
define('TABLE_HEADING_FILE_SIZE', 'Size');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_INFO_HEADING_NEW_BACKUP', 'New Backup');
define('TEXT_INFO_HEADING_RESTORE_LOCAL', 'Restore Local');
define('TEXT_INFO_NEW_BACKUP', 'Do not interrupt the backup process which might take a couple of minutes.');
define('TEXT_INFO_UNPACK', '<br /><br />(after unpacking the file from the archive)');
define('TEXT_INFO_RESTORE', 'Do not interrupt the restoration process.<br /><br />The larger the backup, the longer this process takes!<br /><br />If possible, use the mysql client.<br /><br />For example:<br /><br /><strong>mysql -h' . DB_SERVER . ' -u' . DB_SERVER_USERNAME . ' -p ' . DB_DATABASE . ' < %s </strong> %s');
define('TEXT_INFO_RESTORE_LOCAL', 'Do not interrupt the restoration process.<br /><br />The larger the backup, the longer this process takes!');
define('TEXT_INFO_RESTORE_LOCAL_RAW_FILE', 'The file uploaded must be a raw sql (text) file.');
define('TEXT_INFO_DATE', 'Date: %s');
define('TEXT_INFO_SIZE', 'Size: %s');
define('TEXT_INFO_COMPRESSION', 'Compression: %s');
define('TEXT_INFO_USE_GZIP', 'Use GZIP');
define('TEXT_INFO_USE_ZIP', 'Use ZIP');
define('TEXT_INFO_USE_NO_COMPRESSION', 'No Compression (Pure SQL)');
define('TEXT_INFO_DOWNLOAD_ONLY', 'Download only (do not store server side)');
define('TEXT_INFO_BEST_THROUGH_HTTPS', 'Best through a HTTPS connection');
define('TEXT_DELETE_INTRO', 'Are you sure you want to delete this backup?');
define('TEXT_NO_EXTENSION', 'None');
define('TEXT_BACKUP_DIRECTORY', 'Backup Directory:<br>%s');
define('TEXT_LAST_RESTORATION', 'Last Restoration:<br>%s');
define('TEXT_FORGET', 'Forget');

define('ERROR_BACKUP_DIRECTORY_DOES_NOT_EXIST', '<strong>Error:</strong> Backup directory does not exist. Please set this in configure.php.');
define('ERROR_BACKUP_DIRECTORY_NOT_WRITEABLE', '<strong>Error:</strong> Backup directory is not writeable.');
define('ERROR_DOWNLOAD_LINK_NOT_ACCEPTABLE', '<strong>Error:</strong> Download link not acceptable.');

define('SUCCESS_LAST_RESTORE_CLEARED', '<strong>Success:</strong> The last restoration date has been cleared.');
define('SUCCESS_DATABASE_SAVED', '<strong>Success:</strong> The database has been saved.');
define('SUCCESS_DATABASE_RESTORED', '<strong>Success:</strong> The database has been restored.');
define('SUCCESS_BACKUP_DELETED', '<strong>Success:</strong> The backup has been removed.');

define('TEXT_INFO_BACKUP_SIZE', '%s MB');
