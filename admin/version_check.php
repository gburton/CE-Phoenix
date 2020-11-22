<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $current_version = tep_get_version();
  $major_version = (int)substr($current_version, 0, 1);

  $releases = null;
  $new_versions = [];
  $check_message = [];

  if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.oscommerce.com/version/online_merchant/ce/phoenix/' . $major_version);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ( file_exists(DIR_FS_CATALOG . 'includes/cacert.pem') ) {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($ch, CURLOPT_CAINFO, DIR_FS_CATALOG . 'includes/cacert.pem');
    }
    else {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    $response = trim(curl_exec($ch));
    curl_close($ch);

    if (!empty($response)) {
      $releases = explode("\n", $response);
    }
  } else {
    if ($fp = @fsockopen('www.oscommerce.com', 80, $errno, $errstr, 30)) {
      $header = 'GET /version/online_merchant/ce/phoenix/' . $major_version . ' HTTP/1.0' . "\r\n" .
                'Host: www.oscommerce.com' . "\r\n" .
                'Connection: close' . "\r\n\r\n";

      fwrite($fp, $header);

      $response = '';
      while (!feof($fp)) {
        $response .= fgets($fp, 1024);
      }

      fclose($fp);

      $response = explode("\r\n\r\n", $response); // split header and content

      if (isset($response[1]) && !empty($response[1])) {
        $releases = explode("\n", trim($response[1]));
      }
    }
  }

  if (is_array($releases) && !empty($releases)) {
    $serialized = serialize($releases);
    if ($f = @fopen(DIR_FS_CACHE . 'oscommerce_version_check.cache', 'w')) {
      fwrite ($f, $serialized, strlen($serialized));
      fclose($f);
    }

    foreach ($releases as $version) {
      $version_array = explode('|', $version);

      if (version_compare($current_version, $version_array[0], '<')) {
        $new_versions[] = $version_array;
      }
    }

    if (!empty($new_versions)) {
      $check_message = ['class' => 'alert alert-danger', 'message' => sprintf(VERSION_UPGRADES_AVAILABLE, $new_versions[0][0])];
    } else {
      $check_message = ['class' => 'alert alert-success', 'message' => VERSION_RUNNING_LATEST];
    }
  } else {
    $check_message = ['class' => 'alert alert-warning', 'message' => ERROR_COULD_NOT_CONNECT];
  }

  require('includes/template_top.php');
?>

  <h1 class="display-4 mb-2"><?= HEADING_TITLE ?></h1>

  <p class="lead"><?= TITLE_INSTALLED_VERSION . ' <strong>OSCOM CE Phoenix v' . $current_version . '</strong>' ?></p>

  <div class="<?= $check_message['class'] ?>">
    <p class="lead"><?= $check_message['message'] ?></p>
  </div>

<?php
  if (!empty($new_versions)) {
  ?>
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <th><?= TABLE_HEADING_VERSION ?></th>
          <th><?= TABLE_HEADING_RELEASED ?></th>
          <th class="text-right"><?= TABLE_HEADING_ACTION ?></th>
        </tr>
      </thead>
      <tbody>
      <?php
      foreach ($new_versions as $version) {
        ?>
        <tr>
          <td><?= '<a href="' . $version[2] . '" target="_blank" rel="noreferrer">OSCOM CE Phoenix v' . $version[0] . '</a>' ?></td>
          <td><?= tep_date_long(substr($version[1], 0, 4) . '-' . substr($version[1], 4, 2) . '-' . substr($version[1], 6, 2)) ?></td>
          <td class="text-right"><?= '<a href="' . $version[2] . '" target="_blank" rel="noreferrer"><i class="fas fa-info-circle text-info"></i></a>' ?></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
    </table>
  </div>
  <?php
  }

  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
