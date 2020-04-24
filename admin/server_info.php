<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = $_GET['action'] ?? '';

  switch ($action) {
    case 'export':
      $info = tep_get_system_information();
    break;

    case 'submit':
      $target_host = 'usage.oscommerce.com';
      $target_path = '/submit.php';

      $encoded = base64_encode(serialize(tep_get_system_information()));

      $response = false;

      if (function_exists('curl_init')) {
        $data = ['info' => $encoded];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://' . $target_host . $target_path);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = trim(curl_exec($ch));
        curl_close($ch);
      } else {
        if ($fp = @fsockopen($target_host, 80, $errno, $errstr, 30)) {
          $data = 'info=' . $encoded;

          fputs($fp, "POST " . $target_path . " HTTP/1.1\r\n");
          fputs($fp, "Host: " . $target_host . "\r\n");
          fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
          fputs($fp, "Content-length: " . strlen($data) . "\r\n");
          fputs($fp, "Connection: close\r\n\r\n");
          fputs($fp, $data."\r\n\r\n");

          $response = '';

          while (!feof($fp)) {
            $response .= fgets($fp, 4096);
          }

          fclose($fp);

          $response = trim(substr($response, strrpos($response, "\r\n\r\n")));
        }
      }

      if ($response != 'OK') {
        $messageStack->add_session(ERROR_INFO_SUBMIT, 'error');
      } else {
        $messageStack->add_session(SUCCESS_INFO_SUBMIT, 'success');
      }

      tep_redirect(tep_href_link('server_info.php'));
    break;

    case 'save':
      $info = tep_get_system_information();
      $info_file = 'server_info-' . date('YmdHis') . '.txt';
      header('Content-type: text/plain');
      header('Content-disposition: attachment; filename=' . $info_file);
      echo tep_format_system_info_array($info);
      exit;

    break;

    default:
      $info = tep_get_system_information();
      break;
  }

  require('includes/template_top.php');
?>

  <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($action == 'export') {
?>
    <div class="alert alert-info">
      <?php echo TEXT_EXPORT_INTRO; ?>
    </div>
    
    <?php 
    echo tep_draw_textarea_field('server configuration', 'soft', '100', '15', tep_format_system_info_array($info)); 
    
    echo tep_draw_bootstrap_button(BUTTON_SAVE_TO_DISK, 'fas fa-save', tep_href_link('server_info.php', 'action=save'), 'primary', null, 'btn-success btn-block btn-lg my-2');

    echo tep_draw_bootstrap_button(BUTTON_SEND_TO_OSCOMMERCE, 'fas fa-file-upload', tep_href_link('server_info.php', 'action=submit'), 'primary', null, 'btn-light');
    
  } else {
    $server = parse_url(HTTP_SERVER);
?>
      
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <th><?php echo TABLE_HEADING_KEY; ?></th>
          <th><?php echo TABLE_HEADING_VALUE; ?></th>
        </tr>
      </thead>
      <tbody>      
        <tr>
          <td><?php echo TITLE_SERVER_HOST; ?></td>
          <td><?php echo $server['host'] . ' (' . gethostbyname($server['host']) . ')'; ?></td>
        </tr>
        <tr>        
          <td><?php echo TITLE_DATABASE_HOST; ?></td>
          <td><?php echo DB_SERVER . ' (' . gethostbyname(DB_SERVER) . ')'; ?></td>
        </tr>
        <tr>
          <td><?php echo TITLE_SERVER_OS; ?></td>
          <td><?php echo $info['system']['os'] . ' ' . $info['system']['kernel']; ?></td>
        </tr>
        <tr>
          <td><?php echo TITLE_DATABASE; ?></td>
          <td><?php echo 'MySQL ' . $info['mysql']['version']; ?></td>
        </tr>
        <tr>
          <td><?php echo TITLE_SERVER_DATE; ?></td>
          <td><?php echo $info['system']['date']; ?></td>
        </tr>
        <tr>
          <td><?php echo TITLE_DATABASE_DATE; ?></td>
          <td><?php echo $info['mysql']['date']; ?></td>
        </tr>
        <tr>
          <td><?php echo TITLE_SERVER_UP_TIME; ?></td>
          <td><?php echo $info['system']['uptime']; ?></td>
        </tr>
        <tr>
          <td><?php echo TITLE_HTTP_SERVER; ?></td>
          <td><?php echo $info['system']['http_server']; ?></td>
        </tr>
        <tr>
          <td><?php echo TITLE_PHP_VERSION; ?></td>
          <td><?php echo $info['php']['version'] . ' (' . TITLE_ZEND_VERSION . ' ' . $info['php']['zend'] . ')'; ?></td>
        </tr>              
      </tbody>
    </table>
  </div>
  
  <?php 
  echo tep_draw_bootstrap_button(IMAGE_EXPORT, 'fas fa-save', tep_href_link('server_info.php', 'action=export'), null, null, 'btn-danger');

  }

  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
