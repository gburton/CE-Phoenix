<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $set = $_GET['set'] ?? '';

  $modules = $cfgModules->getAll();

  if (empty($set) || !$cfgModules->exists($set)) {
    $set = $modules[0]['code'];
  }

  $module_type = $cfgModules->get($set, 'code');
  $module_directory = $cfgModules->get($set, 'directory');
  $module_language_directory = $cfgModules->get($set, 'language_directory')
    . "$language/modules/$module_type/";
  $module_key = $cfgModules->get($set, 'key');
  define('HEADING_TITLE', $cfgModules->get($set, 'title'));
  $template_integration = $cfgModules->get($set, 'template_integration');

  $modules_installed = (defined($module_key) && !empty(constant($module_key)) ? explode(';', constant($module_key)) : []);

  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('modules', 'preAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'save':
        foreach ($_POST['configuration'] as $key => $value) {
          if (is_array($value)) {
            $value = implode(';', $value);
          }

          $key = tep_db_prepare_input($key);
          $value = tep_db_prepare_input($value);
          tep_db_query("UPDATE configuration SET configuration_value = '" . tep_db_input($value) . "' WHERE configuration_key = '" . tep_db_input($key) . "'");
        }

        $OSCOM_Hooks->call('modules', 'saveAction');

        tep_redirect(tep_href_link('modules.php', 'set=' . $set . '&module=' . $_GET['module']));
        break;
      case 'remove':
      case 'install':
        $file_extension = pathinfo($PHP_SELF, PATHINFO_EXTENSION);
        $class = $_GET['module'];
        $basename = "$class.$file_extension";
        if (class_exists($class)) {
          if (isset($$class)) {
            $module = &$$class;
          } else {
            $module = new $class();
          }

          if (!cfg_modules::can($module, $action)) {
            if ('install' == $action) {
              $messageStack->add_session(ERROR_MODULE_UNMET_REQUIREMENT, 'error');
              foreach ($customer_data->get_last_missing_abilities() as $missing_ability) {
                $messageStack->add_session($missing_ability);
              }
            } elseif ('remove' == $action) {
              $messageStack->add_session(ERROR_MODULE_HAS_DEPENDENTS, 'error');
              foreach ($customer_data->get_last_matched_requirers() as $requirement => $requirers) {
                $messageStack->add_session($requirement . htmlspecialchars(' => ') . implode(', ', $requirers));
              }
            }
          } elseif ('install' == $action) {
            if ($module->check() > 0) {
              // remove module if already installed
              $module->remove();
            }

            $module->install();

            if (!in_array($basename, $modules_installed)) {
              $modules_installed[] = $basename;
            }

            tep_db_query("UPDATE configuration SET configuration_value = '" . implode(';', $modules_installed) . "' WHERE configuration_key = '" . $module_key . "'");

            $OSCOM_Hooks->call('modules', 'installAction');

          } elseif ('remove' == $action) {
            $module->remove();

            if (in_array($basename, $modules_installed)) {
              unset($modules_installed[array_search($basename, $modules_installed)]);
            }

            tep_db_query("UPDATE configuration SET configuration_value = '" . implode(';', $modules_installed) . "' WHERE configuration_key = '" . $module_key . "'");

            $OSCOM_Hooks->call('modules', 'removeAction');

            tep_redirect(tep_href_link('modules.php', 'set=' . $set));
          }
        }
        tep_redirect(tep_href_link('modules.php', 'set=' . $set . '&module=' . $class));
        break;
    }
  }

  $OSCOM_Hooks->call('modules', 'postAction');

  $new_modules_counter = 0;

  $file_extension = pathinfo($PHP_SELF, PATHINFO_EXTENSION);
  $module_files = [];
  if ($dir = @dir($module_directory)) {
    while ($file = $dir->read()) {
      if (!is_dir($module_directory . $file) && pathinfo($file, PATHINFO_EXTENSION) === $file_extension) {
        if (isset($_GET['list']) && ('new' === $_GET['list'])) {
          if (!in_array($file, $modules_installed)) {
            $module_files[] = $file;
          }
        } else {
          if (in_array($file, $modules_installed)) {
            $module_files[] = $file;
          } else {
            $new_modules_counter++;
          }
        }
      }
    }
    sort($module_files);
    $dir->close();
  }

  require 'includes/template_top.php';
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?= HEADING_TITLE; ?></h1>
    </div>
    <div class="col-sm-4 text-right align-self-center">
      <?php
      if (isset($_GET['list'])) {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('modules.php', 'set=' . $set), null, null, 'btn-light');
      } else {
        echo tep_draw_bootstrap_button(IMAGE_MODULE_INSTALL . ' (' . $new_modules_counter . ')', 'fas fa-plus', tep_href_link('modules.php', 'set=' . $set . '&list=new'), null, null, 'btn-danger');
      }
      ?>
    </div>
  </div>

  <div class="row no-gutters">
    <div class="col-12 col-sm-8">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_MODULES; ?></th>
              <th class="text-right"><?= TABLE_HEADING_SORT_ORDER; ?></th>
              <th class="text-right"><?= TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $installed_modules = [];
            foreach ($module_files as $file) {
              $class = pathinfo($file, PATHINFO_FILENAME);
              if (class_exists($class)) {
                $module = new $class();
                if ($module->check() > 0) {
                  if (($module->sort_order > 0) && !isset($installed_modules[$module->sort_order])) {
                    $installed_modules[$module->sort_order] = $file;
                  } else {
                    $installed_modules[] = $file;
                  }
                }

                if (!isset($mInfo) && (!isset($_GET['module']) || ($_GET['module'] == $class))) {
                  $module_info = [
                    'code' => $module->code,
                    'title' => $module->title,
                    'description' => $module->description,
                    'status' => $module->check(),
                    'signature' => ($module->signature ?? null),
                    'api_version' => ($module->api_version ?? null),
                  ];

                  $keys_extra = [];
                  foreach ($module->keys() as $key) {
                    $key_value_query = tep_db_query("SELECT configuration_title, configuration_value, configuration_description, use_function, set_function FROM configuration WHERE configuration_key = '" . $key . "'");
                    $key_value = tep_db_fetch_array($key_value_query);

                    if (!isset($keys_extra[$key])) {
                      $keys_extra[$key] = [];
                    }

                    if (is_null($key_value) && ($module->check() <= 0)) {
                      continue;
                    }

                    $keys_extra[$key]['title'] = $key_value['configuration_title'];
                    $keys_extra[$key]['value'] = $key_value['configuration_value'];
                    $keys_extra[$key]['description'] = $key_value['configuration_description'];
                    $keys_extra[$key]['use_function'] = $key_value['use_function'];
                    $keys_extra[$key]['set_function'] = $key_value['set_function'];
                  }

                  $module_info['keys'] = $keys_extra;

                  $mInfo = new objectInfo($module_info);
                }

                if (isset($mInfo->code) && ($class == $mInfo->code) ) {
                  if ($module->check() > 0) {
                    echo '<tr class="table-active onclick="document.location.href=\'' . tep_href_link('modules.php', 'set=' . $set . '&module=' . $class . '&action=edit') . '\'">' . PHP_EOL;
                  } else {
                    echo '<tr class="table-active">' . PHP_EOL;
                  }

                  $icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
                } else {
                  echo '<tr onclick="document.location.href=\'' . tep_href_link('modules.php', 'set=' . $set . (isset($_GET['list']) ? '&list=new' : '') . '&module=' . $class) . '\'">' . PHP_EOL;
                  $icon = '<a href="' . tep_href_link('modules.php', 'set=' . $set . (isset($_GET['list']) ? '&list=new' : '') . '&module=' . $class) . '"><i class="fas fa-info-circle text-muted"></i></a>';
                }
                ?>
                <td><?= $module->title; ?></td>
                <td class="text-right"><?php if (in_array($module->code . ".$file_extension", $modules_installed) && is_numeric($module->sort_order)) echo $module->sort_order; ?></td>
                <td class="text-right"><?= $icon; ?></td>
              </tr>
              <?php
              }
            }

            if (!isset($_GET['list'])) {
              ksort($installed_modules);
              $check_query = tep_db_query("SELECT configuration_value FROM configuration WHERE configuration_key = '" . $module_key . "'");
              if ($check = tep_db_fetch_array($check_query)) {
                if ($check['configuration_value'] != implode(';', $installed_modules)) {
                  tep_db_query("UPDATE configuration SET configuration_value = '" . implode(';', $installed_modules) . "', last_modified = NOW() WHERE configuration_key = '" . $module_key . "'");
                }
              } else {
                tep_db_query("INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Installed Modules', '" . $module_key . "', '" . implode(';', $installed_modules) . "', 'This is automatically updated. No need to edit.', '6', '0', NOW())");
              }

              if ($template_integration) {
                $check_query = tep_db_query("SELECT configuration_value FROM configuration WHERE configuration_key = 'TEMPLATE_BLOCK_GROUPS'");
                if ($check = tep_db_fetch_array($check_query)) {
                  $tbgroups = explode(';', $check['configuration_value']);
                  if (!in_array($module_type, $tbgroups)) {
                    $tbgroups[] = $module_type;
                    sort($tbgroups);
                    tep_db_query("UPDATE configuration SET configuration_value = '" . implode(';', $tbgroups) . "', last_modified = NOW() WHERE configuration_key = 'TEMPLATE_BLOCK_GROUPS'");
                  }
                } else {
                  tep_db_query("INSERT INTO configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Installed Template Block Groups', 'TEMPLATE_BLOCK_GROUPS', '" . $module_type . "', 'This is automatically updated. No need to edit.', '6', '0', NOW())");
                }
              }
            }
            ?>
          </tbody>
        </table>
      </div>
      <p><?= TEXT_MODULE_DIRECTORY . ' ' . $module_directory; ?></p>
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

      $contents = ['form' => tep_draw_form('modules', 'modules.php', tep_get_all_get_params(['action']) . 'action=save')];
      $contents[] = ['text' => $keys];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('modules.php', 'set=' . $set . '&module=' . $_GET['module']), null, null, 'btn-light')];
      break;
    default:
      if (isset($mInfo)) {
        $heading[] = ['text' => $mInfo->title];

        if (in_array($mInfo->code . ".$file_extension", $modules_installed) && ($mInfo->status > 0)) {
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
          $keys = substr($keys, 0, strrpos($keys, '<br><br>'));

          $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-plus', tep_href_link('modules.php', 'set=' . $set . '&module=' . $mInfo->code . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_MODULE_REMOVE, 'fas fa-minus', tep_href_link('modules.php', 'set=' . $set . '&module=' . $mInfo->code . '&action=remove'), null, null, 'btn-warning')];

          if (isset($mInfo->signature) && (list($scode, $smodule, $sversion, $soscversion) = explode('|', $mInfo->signature))) {
            $contents[] = ['text' => '<i class="fas fa-info-circle text-dark mr-2"></i><strong>' . TEXT_INFO_VERSION . '</strong> ' . $sversion . ' (<a href="http://sig.oscommerce.com/' . $mInfo->signature . '" target="_blank" rel="noreferrer">' . TEXT_INFO_ONLINE_STATUS . '</a>)'];
          }

          if (isset($mInfo->api_version)) {
            $contents[] = ['text' => '<i class="fas fa-info-circle text-dark mr-2"></i><strong>' . TEXT_INFO_API_VERSION . '</strong> ' . $mInfo->api_version];
          }

          $contents[] = ['text' => $mInfo->description];
          $contents[] = ['text' => $keys];
        } elseif (isset($_GET['list']) && ($_GET['list'] == 'new')) {
          if (isset($mInfo)) {
            $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_MODULE_INSTALL, 'fas fa-plus', tep_href_link('modules.php', 'set=' . $set . '&module=' . $mInfo->code . '&action=install'), null, null, 'btn-warning')];

            if (isset($mInfo->signature) && (list($scode, $smodule, $sversion, $soscversion) = explode('|', $mInfo->signature))) {
              $contents[] = ['text' => '<i class="fas fa-info-circle text-dark mr-2"></i><strong>' . TEXT_INFO_VERSION . '</strong> ' . $sversion . ' (<a href="http://sig.oscommerce.com/' . $mInfo->signature . '" target="_blank" rel="noreferrer">' . TEXT_INFO_ONLINE_STATUS . '</a>)'];
            }

            if (isset($mInfo->api_version)) {
              $contents[] = ['text' => '<i class="fas fa-info-circle text-dark mr-2"></i><strong>' . TEXT_INFO_API_VERSION . '</strong> ' . $mInfo->api_version];
            }

            $contents[] = ['text' => $mInfo->description];
          }

        }
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-4">';
    $box = new box();
    echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

  </div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
