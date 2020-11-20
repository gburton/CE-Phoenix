<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  function tep_sort_secmodules($a, $b) {
    return strcasecmp($a['title'], $b['title']);
  }

  $types = ['info', 'warning', 'error'];

  $modules = [];

  if ($secdir = @dir(DIR_FS_ADMIN . 'includes/modules/security_check/')) {
    while ($file = $secdir->read()) {
      if (!is_dir(DIR_FS_ADMIN . "includes/modules/security_check/$file")) {
        if ('php' === pathinfo($file, PATHINFO_EXTENSION)) {
          $code = pathinfo($file, PATHINFO_FILENAME);
          $class = "securityCheck_$code";

          include(DIR_FS_ADMIN . 'includes/modules/security_check/' . $file);
          $$class = new $class();

          $modules[] = [
            'title' => $$class->title ?? $code,
            'class' => $class,
            'code' => $code,
          ];
        }
      }
    }
    $secdir->close();
  }

  if ($extdir = @dir(DIR_FS_ADMIN . 'includes/modules/security_check/extended/')) {
    while ($file = $extdir->read()) {
      if (!is_dir(DIR_FS_ADMIN . "includes/modules/security_check/extended/$file")) {
        if ('php' === pathinfo($file, PATHINFO_EXTENSION)) {
          $code = pathinfo($file, PATHINFO_FILENAME);
          $class = "securityCheckExtended_$code";

          include(DIR_FS_ADMIN . 'includes/modules/security_check/extended/' . $file);
          $$class = new $class();

          $modules[] = [
            'title' => $$class->title ?? $code,
            'class' => $class,
            'code' => $code,
          ];
        }
      }
    }
    $extdir->close();
  }

  usort($modules, 'tep_sort_secmodules');

  require 'includes/template_top.php';
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?= HEADING_TITLE ?></h1>
    </div>
    <div class="col-sm-4 text-right align-self-center">
      <?= tep_draw_bootstrap_button(BUTTON_TEXT_RELOAD, 'fas fa-cog', tep_href_link('security_checks.php'), null, null, 'btn-info') ?>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <th><?= TABLE_HEADING_TITLE ?></th>
          <th><?= TABLE_HEADING_MODULE ?></th>
          <th class="w-50"><?= TABLE_HEADING_INFO ?></th>
          <th class="text-right">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($modules as $module) {
          $secCheck = ${$module['class']};

          if ( !in_array($secCheck->type, $types) ) {
            $secCheck->type = 'info';
          }

          $output = '';

          if ( $secCheck->pass() ) {
            $secCheck->type = 'success';
          } else {
            $output = $secCheck->getMessage();
          }

          switch($secCheck->type) {
            case 'info':
            $fa = 'fas fa-fw fa-info-circle text-info';
            break;
            case 'warning':
            case 'error':
            $fa = 'fas fa-fw fa-exclamation-circle text-danger';
            break;
            default:
            $fa = 'fas fa-fw fa-check-circle text-success';
          }

          echo '<tr>';
            echo '<td><i class="' . $fa . '"></i> ' . htmlspecialchars($module['title']) . '</td>';
            echo '<td>' . htmlspecialchars($module['code']) . '</td>';
            echo '<td>' . $output . '</td>';
            echo '<td class="text-right">' . (empty($secCheck->has_doc) ? '' : '<a href="http://library.oscommerce.com/Wiki&oscom_2_3&security_checks&' . $module['code'] . '" target="_blank" rel="noreferrer"><i class="fas fa-chevron-circle-right text-info"></i></a>') . '</td>';
          echo '</tr>';
        }
      ?>
      </tbody>
    </table>
  </div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
