<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  function tep_opendir($path) {
    $path = rtrim($path, '/') . '/';

    $exclude_array = array('.', '..', '.DS_Store', 'Thumbs.db', '.github');

    $result = array();

    if ($handle = opendir($path)) {
      while (false !== ($filename = readdir($handle))) {
        if (!in_array($filename, $exclude_array)) {
          $file = array('name' => $path . $filename,
                        'is_dir' => is_dir($path . $filename),
                        'writable' => tep_is_writable($path . $filename));

          $result[] = $file;

          if ($file['is_dir'] == true) {
            $result = array_merge($result, tep_opendir($path . $filename));
          }
        }
      }

      closedir($handle);
    }

    return $result;
  }

  $whitelist_array = array();

  $whitelist_query = tep_db_query("select directory from sec_directory_whitelist");
  while ($whitelist = tep_db_fetch_array($whitelist_query)) {
    $whitelist_array[] = $whitelist['directory'];
  }

  $admin_dir = basename(DIR_FS_ADMIN);

  if ($admin_dir != 'admin') {
    for ($i=0, $n=sizeof($whitelist_array); $i<$n; $i++) {
      if (substr($whitelist_array[$i], 0, 6) == 'admin/') {
        $whitelist_array[$i] = $admin_dir . substr($whitelist_array[$i], 5);
      }
    }
  }

  require('includes/template_top.php');
?>

  <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>

  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <th><?php echo TABLE_HEADING_DIRECTORIES; ?></th>
          <th class="text-center"><?php echo TABLE_HEADING_WRITABLE; ?></th>
          <th class="text-center"><?php echo TABLE_HEADING_RECOMMENDED; ?></th>
        </tr>
      </thead>
      <tbody>
      <?php
      foreach (tep_opendir(DIR_FS_CATALOG) as $file) {
        if ($file['is_dir']) {
          ?>
          <tr>
            <td><?php echo substr($file['name'], strlen(DIR_FS_CATALOG)); ?></td>
            <td class="text-center"><?php echo (($file['writable'] == true) ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'); ?></td>
            <td class="text-center"><?php echo (in_array(substr($file['name'], strlen(DIR_FS_CATALOG)), $whitelist_array) ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'); ?></td>
          </tr>
          <?php
        }
      }
      ?>
      </tbody>
    </table>
  </div>
  
  <p><?php echo TEXT_DIRECTORY . ' ' . DIR_FS_CATALOG; ?></p>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
