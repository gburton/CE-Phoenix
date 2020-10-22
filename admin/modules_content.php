<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $check_query = tep_db_query("SELECT configuration_value FROM configuration WHERE configuration_key = 'MODULE_CONTENT_INSTALLED' LIMIT 1");
  if (tep_db_num_rows($check_query) < 1) {
    tep_db_query(<<<'EOSQL'
INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added)
 VALUES ('Installed Modules', 'MODULE_CONTENT_INSTALLED', '', 'This is automatically updated. No need to edit.', 6, 0, NOW())
EOSQL
      );
    define('MODULE_CONTENT_INSTALLED', '');
  }

  $modules_installed = (tep_not_null(MODULE_CONTENT_INSTALLED) ? explode(';', MODULE_CONTENT_INSTALLED) : []);
  $modules = ['installed' => [], 'new' => []];

  if ($maindir = @dir(DIR_FS_CATALOG . 'includes/modules/content/')) {
    while ($group = $maindir->read()) {
      if ( ($group != '.') && ($group != '..') && is_dir(DIR_FS_CATALOG . 'includes/modules/content/' . $group)) {
        if ($dir = @dir(DIR_FS_CATALOG . 'includes/modules/content/' . $group)) {
          while ($file = $dir->read()) {
            if (!is_dir(DIR_FS_CATALOG . 'includes/modules/content/' . $group . '/' . $file)) {
              if ('php' === pathinfo($file, PATHINFO_EXTENSION)) {
                $class = pathinfo($file, PATHINFO_FILENAME);

                if (class_exists($class)) {
                  $module = new $class();

                  if (in_array($group . '/' . $class, $modules_installed)) {
                    $modules['installed'][] = [
                      'code' => $class,
                      'title' => $module->title,
                      'group' => $group,
                      'sort_order' => (int)$module->sort_order,
                    ];
                  } else {
                    $modules['new'][] = [
                      'code' => $class,
                      'title' => $module->title,
                      'group' => $group,
                    ];
                  }
                }
              }
            }
          }

          $dir->close();
        }
      }
    }

    $maindir->close();

    function _sortContentModulesInstalled($a, $b) {
      return strnatcmp($a['group'] . '-' . (int)$a['sort_order'] . '-' . $a['title'], $b['group'] . '-' . (int)$b['sort_order'] . '-' . $b['title']);
    }

    function _sortContentModuleFiles($a, $b) {
      return strnatcmp($a['group'] . '-' . $a['title'], $b['group'] . '-' . $b['title']);
    }

    usort($modules['installed'], '_sortContentModulesInstalled');
    usort($modules['new'], '_sortContentModuleFiles');
  }

// Update sort order in MODULE_CONTENT_INSTALLED
  $_installed = [];

  foreach ( $modules['installed'] as $m ) {
    $_installed[] = $m['group'] . '/' . $m['code'];
  }

  if ( implode(';', $_installed) != MODULE_CONTENT_INSTALLED ) {
    tep_db_query("UPDATE configuration SET configuration_value = '" . implode(';', $_installed) . "' WHERE configuration_key = 'MODULE_CONTENT_INSTALLED'");
  }

  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('modules_content', 'preAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'save':
        $class = basename($_GET['module']);

        foreach ( $modules['installed'] as $m ) {
          if ( $m['code'] == $class ) {
            foreach ($_POST['configuration'] as $key => $value) {
              $key = tep_db_prepare_input($key);
              $value = tep_db_prepare_input($value);

              tep_db_query("UPDATE configuration SET configuration_value = '" . tep_db_input($value) . "' WHERE configuration_key = '" . tep_db_input($key) . "'");
            }

            break;
          }
        }

        $OSCOM_Hooks->call('modules_content', 'saveAction');

        tep_redirect(tep_href_link('modules_content.php', 'module=' . $class));

        break;

      case 'install':
        $class = basename($_GET['module']);

        foreach ( $modules['new'] as $m ) {
          if ( $m['code'] == $class ) {
            $module = new $class();

            $module->install();

            $modules_installed[] = $m['group'] . '/' . $m['code'];

            tep_db_query("UPDATE configuration SET configuration_value = '" . implode(';', $modules_installed) . "' WHERE configuration_key = 'MODULE_CONTENT_INSTALLED'");

            tep_redirect(tep_href_link('modules_content.php', 'module=' . $class . '&action=edit'));
          }
        }

        $OSCOM_Hooks->call('modules_content', 'installAction');

        tep_redirect(tep_href_link('modules_content.php', 'action=list_new&module=' . $class));

        break;

      case 'remove':
        $class = basename($_GET['module']);

        foreach ( $modules['installed'] as $m ) {
          if ( $m['code'] == $class ) {
            $module = new $class();

            $module->remove();

            $modules_installed = explode(';', MODULE_CONTENT_INSTALLED);

            if (in_array($m['group'] . '/' . $m['code'], $modules_installed)) {
              unset($modules_installed[array_search($m['group'] . '/' . $m['code'], $modules_installed)]);
            }

            tep_db_query("UPDATE configuration SET configuration_value = '" . implode(';', $modules_installed) . "' WHERE configuration_key = 'MODULE_CONTENT_INSTALLED'");

            tep_redirect(tep_href_link('modules_content.php'));
          }
        }

        $OSCOM_Hooks->call('modules_content', 'removeAction');

        tep_redirect(tep_href_link('modules_content.php', 'module=' . $class));

        break;
    }
  }

  $OSCOM_Hooks->call('modules_content', 'postAction');

  require 'includes/template_top.php';
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?= HEADING_TITLE ?></h1>
    </div>
    <div class="col-sm-4 text-right align-self-center">
      <?php
      if ('list_new' === $action) {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('modules_content.php'), null, null, 'btn-light');
      } else {
        echo tep_draw_bootstrap_button(IMAGE_MODULE_INSTALL . ' (' . count($modules['new']) . ')', 'fas fa-plus', tep_href_link('modules_content.php', 'action=list_new'), null, null, 'btn-danger');
      }
      ?>
    </div>
  </div>

  <div class="row no-gutters">
    <div class="col-12 col-sm-8">

<?php
  if ( $action == 'list_new' ) {
?>
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_MODULES ?></th>
              <th><?= TABLE_HEADING_GROUP ?></th>
              <th class="text-right"><?= TABLE_HEADING_ACTION ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ( $modules['new'] as $m ) {
              $module = new $m['code']();

              if (!isset($mInfo) && (!isset($_GET['module']) || ($_GET['module'] === $module->code))) {
                $module_info = [
                  'code' => $module->code,
                  'title' => $module->title,
                  'description' => $module->description,
                  'signature' => ($module->signature ?? null),
                  'api_version' => ($module->api_version ?? null),
                ];

                $mInfo = new objectInfo($module_info);
              }

              if (isset($mInfo->code) && ($module->code == $mInfo->code) ) {
                echo '<tr class="table-active">';
                $icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('modules_content.php', 'action=list_new&module=' . $module->code) . '\'">';
                $icon = '<a href="' . tep_href_link('modules_content.php', 'action=list_new&module=' . $module->code) . '"><i class="fas fa-info-circle text-muted"></i></a>';
              }
              ?>
                <td><?= $module->title ?></td>
                <td><?php if (is_callable([$module, 'get_group'])) { echo $module->get_group(); } elseif (isset($module->group)) { echo $module->group; } ?></td>
                <td class="text-right"><?= $icon ?></td>
              </tr>
              <?php
              }
            ?>
          </tbody>
        </table>
      </div>

<?php
  } else {
?>

      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_MODULES ?></th>
              <th><?= TABLE_HEADING_GROUP ?></th>
              <th class="text-right"><?= TABLE_HEADING_SORT_ORDER ?></th>
              <th class="text-right"><?= TABLE_HEADING_MODULE_ACTIVE ?></th>
              <th class="text-right"><?= TABLE_HEADING_ACTION ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ( $modules['installed'] as $m ) {
              $module = new $m['code']();

              if (!isset($mInfo) && (!isset($_GET['module']) || ($_GET['module'] == $module->code))) {
                $module_info = [
                  'code' => $module->code,
                  'title' => $module->title,
                  'description' => $module->description,
                  'signature' => ($module->signature ?? null),
                  'api_version' => ($module->api_version ?? null),
                  'sort_order' => (int)$module->sort_order,
                  'keys' => [],
                ];

                foreach ($module->keys() as $key) {
                  $key = tep_db_prepare_input($key);

                  $key_value_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT configuration_title, configuration_value, configuration_description, use_function, set_function
 FROM configuration
 WHERE configuration_key = '%s'
EOSQL
                    , tep_db_input($key)));
                  $key_value = tep_db_fetch_array($key_value_query);

                  $module_info['keys'][$key] = [
                    'title' => $key_value['configuration_title'],
                    'value' => $key_value['configuration_value'],
                    'description' => $key_value['configuration_description'],
                    'use_function' => $key_value['use_function'],
                    'set_function' => $key_value['set_function'],
                  ];
                }

                $mInfo = new objectInfo($module_info);
              }

              if (isset($mInfo->code) && ($module->code == $mInfo->code) ) {
                echo '<tr class="table-active">';
                $icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('modules_content.php', 'module=' . $module->code) . '\'">';
                $icon = '<a href="' . tep_href_link('modules_content.php', 'module=' . $module->code) . '"><i class="fas fa-info-circle text-muted"></i></a>';
              }
              ?>
                <td><?= $module->title ?></td>
                <td><?php if (is_callable([$module, 'get_group'])) { echo $module->get_group(); } elseif (isset($module->group)) { echo $module->group; } ?></td>
                <td class="text-right"><?= $module->sort_order ?></td>
                <td class="text-right">
                  <?= ($module->enabled == 1) ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>' ?>
                </td>
                <td class="text-right"><?= $icon ?></td>
              </tr>
              <?php
              }
            ?>
          </tbody>
        </table>
      </div>
<?php
  }
?>
      <p class="smallText"><?= TEXT_MODULE_DIRECTORY . ' ' . DIR_FS_CATALOG . 'includes/modules/content/' ?></p>

    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'edit':
      $keys = '';

      foreach ($mInfo->keys as $key => $value) {
        $keys .= '<strong>' . $value['title'] . '</strong><br>' . $value['description'] . '<br>';

        if ($value['set_function']) {
          eval('$keys .= ' . $value['set_function'] . "'" . addslashes($value['value']) . "', '" . $key . "');");
        } else {
          $keys .= tep_draw_input_field('configuration[' . $key . ']', $value['value']);
        }

        $keys .= '<br><br>';
      }

      $keys = html_entity_decode(stripslashes(substr($keys, 0, strrpos($keys, '<br><br>'))));

      $heading[] = ['text' => $mInfo->title];

      $contents = ['form' => tep_draw_form('modules', 'modules_content.php', 'module=' . $mInfo->code . '&action=save')];
      $contents[] = ['text' => $keys];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('modules_content.php', 'module=' . $mInfo->code), null, null, 'btn-light')];

      break;

    default:
      if ( isset($mInfo) ) {
        $heading[] = ['text' => $mInfo->title];

        if ($action == 'list_new') {
          $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_MODULE_INSTALL, 'fas fa-plus', tep_href_link('modules_content.php', 'module=' . $mInfo->code . '&action=install'), null, null, 'btn-warning')];

          if (isset($mInfo->signature) && (list($scode, $smodule, $sversion, $soscversion) = explode('|', $mInfo->signature))) {
            $contents[] = ['text' => '<i class="fas fa-info-circle text-muted"></i> <strong>' . TEXT_INFO_VERSION . '</strong> ' . $sversion . ' (<a href="http://sig.oscommerce.com/' . $mInfo->signature . '" target="_blank">' . TEXT_INFO_ONLINE_STATUS . '</a>)'];
          }

          if (isset($mInfo->api_version)) {
            $contents[] = ['text' => '<i class="fas fa-info-circle text-muted"></i> <strong>' . TEXT_INFO_API_VERSION . '</strong> ' . $mInfo->api_version];
          }

          $contents[] = ['text' => $mInfo->description];
        } else {
          $keys = '';

          foreach ($mInfo->keys as $value) {
            $keys .= '<strong>' . $value['title'] . '</strong><br>';

            if ($value['use_function']) {
              if (strpos($value['use_function'], '->')) {
                $class_method = explode('->', $value['use_function']);
                $use_function = [Guarantor::ensure_global($class_method[0]), $class_method[1]];
              } else {
                $use_function = $value['use_function'];
              }

              if (is_callable($use_function)) {
                $keys .= call_user_func($use_function, $value['value']);
              } else {
                $keys .= '0';
                $messageStack->add(
                  sprintf(
                    WARNING_INVALID_USE_FUNCTION,
                    $configuration['use_function'],
                    $configuration['configuration_title']),
                  'warning');
              }
            } else {
              $keys .= tep_break_string($value['value'], 40, '<br>');
            }

            $keys .= '<br><br>';
          }

          $keys = html_entity_decode(stripslashes(substr($keys, 0, strrpos($keys, '<br><br>'))));

          $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-plus', tep_href_link('modules_content.php', 'module=' . $mInfo->code . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_MODULE_REMOVE, 'fas fa-minus', tep_href_link('modules_content.php', 'module=' . $mInfo->code . '&action=remove'), null, null, 'btn-warning')];

          if (isset($mInfo->signature) && (list($scode, $smodule, $sversion, $soscversion) = explode('|', $mInfo->signature))) {
            $contents[] = ['text' => '<i class="fas fa-info-circle text-muted"></i> <strong>' . TEXT_INFO_VERSION . '</strong> ' . $sversion . ' (<a href="http://sig.oscommerce.com/' . $mInfo->signature . '" target="_blank">' . TEXT_INFO_ONLINE_STATUS . '</a>)'];
          }

          if (isset($mInfo->api_version)) {
            $contents[] = ['text' => '<i class="fas fa-info-circle text-muted"></i> <strong>' . TEXT_INFO_API_VERSION . '</strong> ' . $mInfo->api_version];
          }

          $contents[] = ['text' => $mInfo->description];
          $contents[] = ['text' => $keys];
        }
      }

      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-4">';
      $box = new box;
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

  </div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
