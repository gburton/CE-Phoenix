<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $check_query = tep_db_query("select configuration_value from configuration where configuration_key = 'MODULE_CONTENT_INSTALLED' limit 1");
  if (tep_db_num_rows($check_query) < 1) {
    tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Installed Modules', 'MODULE_CONTENT_INSTALLED', '', 'This is automatically updated. No need to edit.', '6', '0', now())");
    define('MODULE_CONTENT_INSTALLED', '');
  }

  $modules_installed = (tep_not_null(MODULE_CONTENT_INSTALLED) ? explode(';', MODULE_CONTENT_INSTALLED) : array());
  $modules = array('installed' => array(), 'new' => array());

  $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));

  if ($maindir = @dir(DIR_FS_CATALOG_MODULES . 'content/')) {
    while ($group = $maindir->read()) {
      if ( ($group != '.') && ($group != '..') && is_dir(DIR_FS_CATALOG_MODULES . 'content/' . $group)) {
        if ($dir = @dir(DIR_FS_CATALOG_MODULES . 'content/' . $group)) {
          while ($file = $dir->read()) {
            if (!is_dir(DIR_FS_CATALOG_MODULES . 'content/' . $group . '/' . $file)) {
              if (substr($file, strrpos($file, '.')) == $file_extension) {
                $class = substr($file, 0, strrpos($file, '.'));

                if (!class_exists($class)) {
                  if ( file_exists(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/content/' . $group . '/' . $file) ) {
                    include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/content/' . $group . '/' . $file);
                  }

                  include(DIR_FS_CATALOG_MODULES . 'content/' . $group . '/' . $file);
                }

                if (class_exists($class)) {
                  $module = new $class();

                  if (in_array($group . '/' . $class, $modules_installed)) {
                    $modules['installed'][] = array('code' => $class,
                                                    'title' => $module->title,
                                                    'group' => $group,
                                                    'sort_order' => (int)$module->sort_order);
                  } else {
                    $modules['new'][] = array('code' => $class,
                                              'title' => $module->title,
                                              'group' => $group);
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
  $_installed = array();

  foreach ( $modules['installed'] as $m ) {
    $_installed[] = $m['group'] . '/' . $m['code'];
  }

  if ( implode(';', $_installed) != MODULE_CONTENT_INSTALLED ) {
    tep_db_query("update configuration set configuration_value = '" . implode(';', $_installed) . "' where configuration_key = 'MODULE_CONTENT_INSTALLED'");
  }

  $action = $_GET['action'] ?? '';

  if (tep_not_null($action)) {
    switch ($action) {
      case 'save':
        $class = basename($_GET['module']);

        foreach ( $modules['installed'] as $m ) {
          if ( $m['code'] == $class ) {
            foreach ($_POST['configuration'] as $key => $value) {
              $key = tep_db_prepare_input($key);
              $value = tep_db_prepare_input($value);

              tep_db_query("update configuration set configuration_value = '" . tep_db_input($value) . "' where configuration_key = '" . tep_db_input($key) . "'");
            }

            break;
          }
        }

        tep_redirect(tep_href_link('modules_content.php', 'module=' . $class));

        break;

      case 'install':
        $class = basename($_GET['module']);

        foreach ( $modules['new'] as $m ) {
          if ( $m['code'] == $class ) {
            $module = new $class();

            $module->install();

            $modules_installed[] = $m['group'] . '/' . $m['code'];

            tep_db_query("update configuration set configuration_value = '" . implode(';', $modules_installed) . "' where configuration_key = 'MODULE_CONTENT_INSTALLED'");

            tep_redirect(tep_href_link('modules_content.php', 'module=' . $class . '&action=edit'));
          }
        }

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

            tep_db_query("update configuration set configuration_value = '" . implode(';', $modules_installed) . "' where configuration_key = 'MODULE_CONTENT_INSTALLED'");

            tep_redirect(tep_href_link('modules_content.php'));
          }
        }

        tep_redirect(tep_href_link('modules_content.php', 'module=' . $class));

        break;
    }
  }

  require('includes/template_top.php');
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
    </div>
    <div class="col-sm-4 text-right align-self-center">
      <?php
      if (isset($_GET['action']) && ($_GET['action'] == 'list_new')) {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('modules_content.php'), null, null, 'btn-light');
      } else {
        echo tep_draw_bootstrap_button(IMAGE_MODULE_INSTALL . ' (' . count($modules['new']) . ')', 'fas fa-cogs', tep_href_link('modules_content.php', 'action=list_new'), null, null, 'btn-danger xxx text-white');
      }
      ?>
    </div>
  </div>
  
  <div class="row no-gutters">
    <div class="col">
    
<?php
  if ( $action == 'list_new' ) {
?>
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?php echo TABLE_HEADING_MODULES; ?></th>
              <th><?php echo TABLE_HEADING_GROUP; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ( $modules['new'] as $m ) {
              $module = new $m['code']();

              if ((!isset($_GET['module']) || (isset($_GET['module']) && ($_GET['module'] == $module->code))) && !isset($mInfo)) {
                $module_info = array('code' => $module->code,
                                     'title' => $module->title,
                                     'description' => $module->description,
                                     'signature' => (isset($module->signature) ? $module->signature : null),
                                     'api_version' => (isset($module->api_version) ? $module->api_version : null));

                $mInfo = new objectInfo($module_info);
              }

              if (isset($mInfo) && is_object($mInfo) && ($module->code == $mInfo->code) ) {
                echo '<tr>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('modules_content.php', 'action=list_new&module=' . $module->code) . '\'">';
              }
              ?>
                <td><?php echo $module->title; ?></td>
                <td><?php if (is_callable([$module, 'get_group'])) { echo $module->get_group(); } elseif (isset($module->group)) { echo $module->group; } ?></td>
                <td class="text-right"><?php if (isset($mInfo) && is_object($mInfo) && ($module->code == $mInfo->code) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('modules_content.php', 'action=list_new&module=' . $module->code) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
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
              <th><?php echo TABLE_HEADING_MODULES; ?></th>
              <th><?php echo TABLE_HEADING_GROUP; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_SORT_ORDER; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_MODULE_ACTIVE; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ( $modules['installed'] as $m ) {
              $module = new $m['code']();

              if ((!isset($_GET['module']) || (isset($_GET['module']) && ($_GET['module'] == $module->code))) && !isset($mInfo)) {
                $module_info = array('code' => $module->code,
                                     'title' => $module->title,
                                     'description' => $module->description,
                                     'signature' => (isset($module->signature) ? $module->signature : null),
                                     'api_version' => (isset($module->api_version) ? $module->api_version : null),
                                     'sort_order' => (int)$module->sort_order,
                                     'keys' => array());

                foreach ($module->keys() as $key) {
                  $key = tep_db_prepare_input($key);

                  $key_value_query = tep_db_query("select configuration_title, configuration_value, configuration_description, use_function, set_function from configuration where configuration_key = '" . tep_db_input($key) . "'");
                  $key_value = tep_db_fetch_array($key_value_query);

                  $module_info['keys'][$key] = array('title' => $key_value['configuration_title'],
                                                     'value' => $key_value['configuration_value'],
                                                     'description' => $key_value['configuration_description'],
                                                     'use_function' => $key_value['use_function'],
                                                     'set_function' => $key_value['set_function']);
                }

                $mInfo = new objectInfo($module_info);
              }

              if (isset($mInfo) && is_object($mInfo) && ($module->code == $mInfo->code) ) {
                echo '<tr>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('modules_content.php', 'module=' . $module->code) . '\'">';
              }
              ?>
                <td><?php echo $module->title; ?></td>
                <td><?php if (is_callable([$module, 'get_group'])) { echo $module->get_group(); } elseif (isset($module->group)) { echo $module->group; } ?></td>
                <td class="text-right"><?php echo $module->sort_order; ?></td>
                <td class="text-right">
                  <?php echo ($module->enabled == 1) ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'; ?>
                </td>
                <td class="dataTableContent" align="right"><?php if (isset($mInfo) && is_object($mInfo) && ($module->code == $mInfo->code) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('modules_content.php', 'module=' . $module->code) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
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
      <p class="smallText"><?php echo TEXT_MODULE_DIRECTORY . ' ' . DIR_FS_CATALOG_MODULES . 'content/'; ?></p>
      
    </div>

<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'edit':
      $keys = '';

      foreach ($mInfo->keys as $key => $value) {
        $keys .= '<strong>' . $value['title'] . '</strong><br />' . $value['description'] . '<br />';

        if ($value['set_function']) {
          eval('$keys .= ' . $value['set_function'] . "'" . tep_db_input($value['value']) . "', '" . $key . "');");
        } else {
          $keys .= tep_draw_input_field('configuration[' . $key . ']', $value['value']);
        }

        $keys .= '<br /><br />';
      }

      $keys = html_entity_decode(stripslashes(substr($keys, 0, strrpos($keys, '<br /><br />'))));

      $heading[] = array('text' => $mInfo->title);

      $contents = array('form' => tep_draw_form('modules', 'modules_content.php', 'module=' . $mInfo->code . '&action=save'));
      $contents[] = array('text' => $keys);
      $contents[] = array('class' => 'text-center', 'text' => tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('modules_content.php', 'module=' . $mInfo->code)));

      break;

    default:
      if ( isset($mInfo) ) {
        $heading[] = array('text' => $mInfo->title);

        if ($action == 'list_new') {
          $contents[] = array('class' => 'text-center', 'text' => tep_draw_button(IMAGE_MODULE_INSTALL, 'plus', tep_href_link('modules_content.php', 'module=' . $mInfo->code . '&action=install')));

          if (isset($mInfo->signature) && (list($scode, $smodule, $sversion, $soscversion) = explode('|', $mInfo->signature))) {
            $contents[] = array('text' => '<i class="fas fa-info-circle text-muted"></i> <strong>' . TEXT_INFO_VERSION . '</strong> ' . $sversion . ' (<a href="http://sig.oscommerce.com/' . $mInfo->signature . '" target="_blank">' . TEXT_INFO_ONLINE_STATUS . '</a>)');
          }

          if (isset($mInfo->api_version)) {
            $contents[] = array('text' => '<i class="fas fa-info-circle text-muted"></i> <strong>' . TEXT_INFO_API_VERSION . '</strong> ' . $mInfo->api_version);
          }

          $contents[] = array('text' => $mInfo->description);
        } else {
          $keys = '';

          foreach ($mInfo->keys as $value) {
            $keys .= '<strong>' . $value['title'] . '</strong><br />';

            if ($value['use_function']) {
              $use_function = $value['use_function'];

              if (preg_match('/->/', $use_function)) {
                $class_method = explode('->', $use_function);

                if (!isset(${$class_method[0]}) || !is_object(${$class_method[0]})) {
                  include('includes/classes/' . $class_method[0] . '.php');
                  ${$class_method[0]} = new $class_method[0]();
                }

                $use_function = [${$class_method[0]}, $class_method[1]];
              }
              $keys .= call_user_func($use_function, $value['value']);
            } else {
              $keys .= $value['value'];
            }

            $keys .= '<br /><br />';
          }

          $keys = html_entity_decode(stripslashes(substr($keys, 0, strrpos($keys, '<br /><br />'))));

          $contents[] = array('class' => 'text-center', 'text' => tep_draw_button(IMAGE_EDIT, 'document', tep_href_link('modules_content.php', 'module=' . $mInfo->code . '&action=edit')) . tep_draw_button(IMAGE_MODULE_REMOVE, 'minus', tep_href_link('modules_content.php', 'module=' . $mInfo->code . '&action=remove')));

          if (isset($mInfo->signature) && (list($scode, $smodule, $sversion, $soscversion) = explode('|', $mInfo->signature))) {
            $contents[] = array('text' => '<i class="fas fa-info-circle text-muted"></i> <strong>' . TEXT_INFO_VERSION . '</strong> ' . $sversion . ' (<a href="http://sig.oscommerce.com/' . $mInfo->signature . '" target="_blank">' . TEXT_INFO_ONLINE_STATUS . '</a>)');
          }

          if (isset($mInfo->api_version)) {
            $contents[] = array('text' => '<i class="fas fa-info-circle text-muted"></i> <strong>' . TEXT_INFO_API_VERSION . '</strong> ' . $mInfo->api_version);
          }

          $contents[] = array('text' => $mInfo->description);
          $contents[] = array('text' => $keys);
        }
      }

      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-3">';
      $box = new box;
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>
          
  </div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
