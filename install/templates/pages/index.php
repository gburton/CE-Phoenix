<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>

<div class="alert alert-info" role="alert">
  <h1>Welcome to OSCOM CE Phoenix</h1>

  <p>OSCOM CE Phoenix helps you sell products worldwide with your own online store. Its Administration Tool manages products, customers, orders, newsletters, specials and more to successfully build your online business.</p>
  <p>Phoenix has attracted a community of store owners and developers who support each other and have provided many free and paid-for add-ons that will extend the features and potential of your online store.</p>
</div>

<div class="row">
  <div class="col-sm-12 col-md-9 order-last">

    <h1 class="dislay-4">New Phoenix v<?php echo osc_get_version(); ?> Installation</h1>

<?php
    $configfile_array = [];

    if (file_exists(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php') && !osc_is_writable(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php')) {
      @chmod(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php', 0777);
    }

    if (file_exists(osc_realpath(dirname(__FILE__) . '/../../../admin/includes') . '/configure.php') && !osc_is_writable(osc_realpath(dirname(__FILE__) . '/../../../admin/includes') . '/configure.php')) {
      @chmod(osc_realpath(dirname(__FILE__) . '/../../../admin/includes') . '/configure.php', 0777);
    }

    if (file_exists(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php') && !osc_is_writable(osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php')) {
      $configfile_array[] = osc_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php';
    }

    if (file_exists(osc_realpath(dirname(__FILE__) . '/../../../admin/includes') . '/configure.php') && !osc_is_writable(osc_realpath(dirname(__FILE__) . '/../../../admin/includes') . '/configure.php')) {
      $configfile_array[] = osc_realpath(dirname(__FILE__) . '/../../../admin/includes') . '/configure.php';
    }

    $warning_array = [];

    if (!extension_loaded('mysqli')) {
      $warning_array['mysql'] = 'The MySQLi extension is required but is not installed. Please enable the extension to continue installation.';
    }

    $php_version_thumb = '<i class="fas fa-thumbs-up text-success"></i>';

    if (version_compare(PHP_VERSION, PHP_VERSION_MIN, '<')) {
      $warning_array['php_version'] = sprintf('The version of PHP must be at least <strong>%s</strong>.  The version here is %s.', PHP_VERSION_MIN, PHP_VERSION);
      $php_version_thumb = '<i class="fas fa-thumbs-down text-danger"></i>';
    }
    
    if (version_compare(PHP_VERSION, PHP_VERSION_MAX, '>=')) {
      $warning_array['php_version'] = sprintf('Performance on versions higher than <strong>%s</strong> has not been tested.  The version here is %s.', PHP_VERSION_MAX, PHP_VERSION);
      $php_version_thumb = '<i class="fas fa-thumbs-down text-danger"></i>';
    }
    
    if ((int)ini_get('allow_url_fopen') == 0) {
      $warning_array['allow_url_fopen'] = 'Fopen Wrappers must be turned on.  This is a <em>hosting</em> setting which you or your host may be able to change by setting allow_url_fopen to "1" or "On" in php.ini';
    }

    if (!empty($configfile_array) || !empty($warning_array)) {
?>

      <div class="noticeBox">

<?php
      if (!empty($warning_array)) {
?>

        <table class="table table-condensed table-striped">

<?php
        foreach ( $warning_array as $key => $value ) {
          echo '        <tr>' . "\n" .
               '          <td valign="top"><strong>' . $key . '</strong></td>' . "\n" .
               '          <td valign="top">' . $value . '</td>' . "\n" .
               '        </tr>' . "\n";
        }
?>

        </table>
<?php
      }

      if (!empty($configfile_array)) {
?>

        <div class="alert alert-danger" role="alert">
          <p>The webserver is not able to save the installation parameters to its configuration files.</p>
          <p>The following files need to have their file permissions set to world-writeable (chmod 777):</p>
          <p>

<?php
        echo implode("<br>\n", $configfile_array);
?>

          </p>
        </div>

<?php
      }
?>

      </div>

<?php
    }

    if (!empty($configfile_array) || !empty($warning_array)) {
?>

      <div class="alert alert-danger" role="alert">Please correct the above errors and retry the installation procedure with the changes in place.</div>

<?php
      if (!empty($warning_array)) {
        echo '    <div class="alert alert-info" role="alert"><i>Changing webserver configuration parameters may require the webserver service to be restarted before the changes take affect.</i></div>' . "\n";
      }
?>

      <p><a href="index.php" class="btn btn-danger btn-block" role="button">Retry</a></p>

<?php
    } else {
?>

      <div class="alert alert-success" role="alert">The webserver environment has been verified to proceed with a successful installation and configuration of your online store.</div>

      <div id="jsOn" style="display: none;">
        <p><a href="install.php" class="btn btn-success btn-block" role="button">Start the installation procedure</a></p>
      </div>

      <div id="jsOff">
        <p class="text-danger">Please enable Javascript in your browser to be able to start the installation procedure.</p>
        <p><a href="index.php" class="btn btn-danger btn-block" role="button">Retry</a></p>
      </div>

<script>
$(function() {
  $('#jsOff').hide();
  $('#jsOn').show();
});
</script>

<?php
  }
?>
  </div>
  <div class="col-sm-12 col-md-3 order-first">
    <h4>Server Capabilities</h4>

    <table class="table table-condensed table-striped">
      <tr>
        <th colspan="3">PHP Version</th>
      </tr>
      <tr>
        <th><?php echo implode('.', [PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION]); ?></th>
        <td colspan="2" class="text-right"><?php echo $php_version_thumb; ?></td>
      </tr>
<?php
  if (function_exists('ini_get')) {
?>
      <tr>
        <th colspan="3">PHP Settings</th>
      </tr>
      <tr>
        <th>file_uploads</th>
        <td class="text-right"><?php echo (((int)ini_get('file_uploads') == 0) ? 'Off' : 'On'); ?></td>
        <td class="text-right"><?php echo (((int)ini_get('file_uploads') == 1) ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-thumbs-down text-danger"></i>'); ?></td>
      </tr>
      <tr>
        <th>auto_start</th>
        <td class="text-right"><?php echo (((int)ini_get('session.auto_start') == 0) ? 'Off' : 'On'); ?></td>
        <td class="text-right"><?php echo (((int)ini_get('session.auto_start') == 0) ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-thumbs-down text-danger"></i>'); ?></td>
      </tr>
      <tr>
        <th>use_trans_sid</th>
        <td class="text-right"><?php echo (((int)ini_get('session.use_trans_sid') == 0) ? 'Off' : 'On'); ?></td>
        <td class="text-right"><?php echo (((int)ini_get('session.use_trans_sid') == 0) ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-thumbs-down text-danger"></i>'); ?></td>
      </tr>
      <tr>
        <th colspan="3">Required Extensions</th>
      </tr>
      <tr>
        <th>MySQL<?php echo extension_loaded('mysqli') ? 'i' : ''; ?></th>
        <td colspan="2" class="text-right"><?php echo (extension_loaded('mysqli') ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-thumbs-down text-danger"></i>'); ?></td>
      </tr>
      <tr>
        <th>allow_url_fopen</th>
        <td class="text-right"><?php echo (((int)ini_get('allow_url_fopen') == 0) ? 'Off' : 'On'); ?></td>
        <td class="text-right"><?php echo (((int)ini_get('allow_url_fopen') == 1) ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-thumbs-down text-danger"></i>'); ?></td>
      </tr>
      <tr>
        <th colspan="3">Recommended Extensions</th>
      </tr>
      <tr>
        <th>GD</th>
        <td colspan="2" class="text-right"><?php echo (extension_loaded('gd') ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-thumbs-down text-danger"></i>'); ?></td>
      </tr>
      <tr>
        <th>cURL</th>
        <td colspan="2" class="text-right"><?php echo (extension_loaded('curl') ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-thumbs-down text-danger"></i>'); ?></td>
      </tr>
      <tr>
        <th>OpenSSL</th>
        <td colspan="2" class="text-right"><?php echo (extension_loaded('openssl') ? '<i class="fas fa-thumbs-up text-success"></i>' : '<i class="fas fa-thumbs-down text-danger"></i>'); ?></td>
      </tr>
    </table>
<?php
  }
?>
  </div>
</div>
