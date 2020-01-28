<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2013 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $set = $_GET['set'] ?? '';

  $modules = $cfgModules->getAll();

  if (empty($set) || !$cfgModules->exists($set)) {
    $set = $modules[0]['code'];
  }

  $module_type = $cfgModules->get($set, 'code');
  $module_directory = $cfgModules->get($set, 'directory');
  $module_language_directory = $cfgModules->get($set, 'language_directory');
  $module_key = $cfgModules->get($set, 'key');;
  define('HEADING_TITLE', $cfgModules->get($set, 'title'));
  $template_integration = $cfgModules->get($set, 'template_integration');

  $OSCOM_Hooks->call('modules', 'preAction');

  $action = $_GET['action'] ?? '';

  if (tep_not_null($action)) {
    switch ($action) {
      case 'save':
        foreach ($_POST['configuration'] as $key => $value) {
          tep_db_query("update configuration set configuration_value = '" . $value . "' where configuration_key = '" . $key . "'");
        }
        tep_redirect(tep_href_link('modules.php', 'set=' . $set . '&module=' . $_GET['module']));
        break;
      case 'install':
      case 'remove':
        $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
        $class = basename($_GET['module']);
        if (file_exists($module_directory . $class . $file_extension)) {
          // include lang file
          include($module_language_directory . $language . '/modules/' . $module_type . '/' . $class . $file_extension);
          include($module_directory . $class . $file_extension);
          $module = new $class;
          if ($action == 'install') {
            if ($module->check() > 0) { // remove module if already installed
              $module->remove();
            }

            $module->install();

            $modules_installed = explode(';', constant($module_key));

            if (!in_array($class . $file_extension, $modules_installed)) {
              $modules_installed[] = $class . $file_extension;
            }

            tep_db_query("update configuration set configuration_value = '" . implode(';', $modules_installed) . "' where configuration_key = '" . $module_key . "'");
            tep_redirect(tep_href_link('modules.php', 'set=' . $set . '&module=' . $class));
          } elseif ($action == 'remove') {
            $module->remove();

            $modules_installed = explode(';', constant($module_key));

            if (in_array($class . $file_extension, $modules_installed)) {
              unset($modules_installed[array_search($class . $file_extension, $modules_installed)]);
            }

            tep_db_query("update configuration set configuration_value = '" . implode(';', $modules_installed) . "' where configuration_key = '" . $module_key . "'");
            tep_redirect(tep_href_link('modules.php', 'set=' . $set));
          }
        }
        tep_redirect(tep_href_link('modules.php', 'set=' . $set . '&module=' . $class));
        break;
    }
  }

  require('includes/template_top.php');

  $modules_installed = (defined($module_key) ? explode(';', constant($module_key)) : array());
  $new_modules_counter = 0;

  $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
  $directory_array = array();
  if ($dir = @dir($module_directory)) {
    while ($file = $dir->read()) {
      if (!is_dir($module_directory . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          if (isset($_GET['list']) && ($_GET['list'] = 'new')) {
            if (!in_array($file, $modules_installed)) {
              $directory_array[] = $file;
            }
          } else {
            if (in_array($file, $modules_installed)) {
              $directory_array[] = $file;
            } else {
              $new_modules_counter++;
            }
          }
        }
      }
    }
    sort($directory_array);
    $dir->close();
  }
?>
  
  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
    </div>
    <div class="col-sm-4 text-right align-self-center">
      <?php
      if (isset($_GET['list'])) {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('modules.php', 'set=' . $set), null, null, 'btn-light');
      } else {
        echo tep_draw_bootstrap_button(IMAGE_MODULE_INSTALL . ' (' . $new_modules_counter . ')', 'fas fa-cogs', tep_href_link('modules.php', 'set=' . $set . '&list=new'), null, null, 'btn-danger xxx text-white');
      }
      ?>
    </div>
  </div>    
  
  <div class="row no-gutters">
    <div class="col">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?php echo TABLE_HEADING_MODULES; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_SORT_ORDER; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ENABLED; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $installed_modules = array();
            for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
              $file = $directory_array[$i];

              include($module_language_directory . $language . '/modules/' . $module_type . '/' . $file);
              include($module_directory . $file);

              $class = substr($file, 0, strrpos($file, '.'));
              if (class_exists($class)) {
                $module = new $class;
                if ($module->check() > 0) {
                  if (($module->sort_order > 0) && !isset($installed_modules[$module->sort_order])) {
                    $installed_modules[$module->sort_order] = $file;
                  } else {
                    $installed_modules[] = $file;
                  }
                }
                
                

                if ((!isset($_GET['module']) || (isset($_GET['module']) && ($_GET['module'] == $class))) && !isset($mInfo)) {
                  $module_info = array('code' => $module->code,
                                       'title' => $module->title,
                                       'description' => $module->description,
                                       'status' => $module->check(),
                                       'signature' => (isset($module->signature) ? $module->signature : null),
                                       'api_version' => (isset($module->api_version) ? $module->api_version : null));

                  $module_keys = $module->keys();

                  $keys_extra = array();
                  for ($j=0, $k=sizeof($module_keys); $j<$k; $j++) {
                    $key_value_query = tep_db_query("select configuration_title, configuration_value, configuration_description, use_function, set_function from configuration where configuration_key = '" . $module_keys[$j] . "'");
                    $key_value = tep_db_fetch_array($key_value_query);

                    $keys_extra[$module_keys[$j]]['title'] = $key_value['configuration_title'];
                    $keys_extra[$module_keys[$j]]['value'] = $key_value['configuration_value'];
                    $keys_extra[$module_keys[$j]]['description'] = $key_value['configuration_description'];
                    $keys_extra[$module_keys[$j]]['use_function'] = $key_value['use_function'];
                    $keys_extra[$module_keys[$j]]['set_function'] = $key_value['set_function'];
                  }

                  $module_info['keys'] = $keys_extra;

                  $mInfo = new objectInfo($module_info);
                }

                if (isset($mInfo) && is_object($mInfo) && ($class == $mInfo->code) ) {
                  if ($module->check() > 0) {
                    echo '<tr onclick="document.location.href=\'' . tep_href_link('modules.php', 'set=' . $set . '&module=' . $class . '&action=edit') . '\'">';
                  } else {
                    echo '<tr>';
                  }
                } else {
                  echo '<tr onclick="document.location.href=\'' . tep_href_link('modules.php', 'set=' . $set . (isset($_GET['list']) ? '&list=new' : '') . '&module=' . $class) . '\'">';
                }
                ?>
                <td><?php echo $module->title; ?></td>
                <td class="text-right"><?php if (in_array($module->code . $file_extension, $modules_installed) && is_numeric($module->sort_order)) echo $module->sort_order; ?></td>
                <td class="text-right"><?php if ( array_key_exists('enabled', $module) ) { echo ($module->enabled == 1) ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'; } else { echo '<i class="fas fa-check-circle text-success"></i>'; } ?></td>
                <td class="text-right"><?php if (isset($mInfo) && is_object($mInfo) && ($class == $mInfo->code) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('modules.php', 'set=' . $set . (isset($_GET['list']) ? '&list=new' : '') . '&module=' . $class) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
              <?php
              }
            }
            
            if (!isset($_GET['list'])) {
              ksort($installed_modules);
              $check_query = tep_db_query("select configuration_value from configuration where configuration_key = '" . $module_key . "'");
              if (tep_db_num_rows($check_query)) {
                $check = tep_db_fetch_array($check_query);
                if ($check['configuration_value'] != implode(';', $installed_modules)) {
                  tep_db_query("update configuration set configuration_value = '" . implode(';', $installed_modules) . "', last_modified = now() where configuration_key = '" . $module_key . "'");
                }
              } else {
                tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Installed Modules', '" . $module_key . "', '" . implode(';', $installed_modules) . "', 'This is automatically updated. No need to edit.', '6', '0', now())");
              }

              if ($template_integration == true) {
                $check_query = tep_db_query("select configuration_value from configuration where configuration_key = 'TEMPLATE_BLOCK_GROUPS'");
                if (tep_db_num_rows($check_query)) {
                  $check = tep_db_fetch_array($check_query);
                  $tbgroups_array = explode(';', $check['configuration_value']);
                  if (!in_array($module_type, $tbgroups_array)) {
                    $tbgroups_array[] = $module_type;
                    sort($tbgroups_array);
                    tep_db_query("update configuration set configuration_value = '" . implode(';', $tbgroups_array) . "', last_modified = now() where configuration_key = 'TEMPLATE_BLOCK_GROUPS'");
                  }
                } else {
                  tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Installed Template Block Groups', 'TEMPLATE_BLOCK_GROUPS', '" . $module_type . "', 'This is automatically updated. No need to edit.', '6', '0', now())");
                }
              }
            }
            ?>
          </tbody>
        </table>
      </div>
      
      <p><?php echo TEXT_MODULE_DIRECTORY . ' ' . $module_directory; ?></p>
              
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
            eval('$keys .= ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");
          } else {
            $keys .= tep_draw_input_field('configuration[' . $key . ']', $value['value']);
          }
          $keys .= '<br /><br />';
        }
        $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));

        $heading[] = array('text' => $mInfo->title);

        $contents = array('form' => tep_draw_form('modules', 'modules.php', 'set=' . $set . '&module=' . $_GET['module'] . '&action=save'));
        $contents[] = array('text' => $keys);
        $contents[] = array('class' => 'text-center', 'text' => tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('modules.php', 'set=' . $set . '&module=' . $_GET['module'])));
        break;
      default:
        if (isset($mInfo)) {
          $heading[] = array('text' => $mInfo->title);

          if (in_array($mInfo->code . $file_extension, $modules_installed) && ($mInfo->status > 0)) {
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
                  $keys .= tep_call_function($class_method[1], $value['value'], ${$class_method[0]});
                } else {
                  $keys .= tep_call_function($use_function, $value['value']);
                }
              } else {
                $keys .= $value['value'];
              }
              $keys .= '<br /><br />';
            }
            $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));

            $contents[] = array('class' => 'text-center', 'text' => tep_draw_button(IMAGE_EDIT, 'document', tep_href_link('modules.php', 'set=' . $set . '&module=' . $mInfo->code . '&action=edit')) . tep_draw_button(IMAGE_MODULE_REMOVE, 'minus', tep_href_link('modules.php', 'set=' . $set . '&module=' . $mInfo->code . '&action=remove')));

            if (isset($mInfo->signature) && (list($scode, $smodule, $sversion, $soscversion) = explode('|', $mInfo->signature))) {
              $contents[] = array('text' => tep_image('images/icon_info.gif', IMAGE_ICON_INFO) . '&nbsp;<strong>' . TEXT_INFO_VERSION . '</strong> ' . $sversion . ' (<a href="http://sig.oscommerce.com/' . $mInfo->signature . '" target="_blank">' . TEXT_INFO_ONLINE_STATUS . '</a>)');
            }

            if (isset($mInfo->api_version)) {
              $contents[] = array('text' => tep_image('images/icon_info.gif', IMAGE_ICON_INFO) . '&nbsp;<strong>' . TEXT_INFO_API_VERSION . '</strong> ' . $mInfo->api_version);
            }

            $contents[] = array('text' => $mInfo->description);
            $contents[] = array('text' => $keys);
          } elseif (isset($_GET['list']) && ($_GET['list'] == 'new')) {
            if (isset($mInfo)) {
              $contents[] = array('class' => 'text-center', 'text' => tep_draw_button(IMAGE_MODULE_INSTALL, 'plus', tep_href_link('modules.php', 'set=' . $set . '&module=' . $mInfo->code . '&action=install')));

              if (isset($mInfo->signature) && (list($scode, $smodule, $sversion, $soscversion) = explode('|', $mInfo->signature))) {
                $contents[] = array('text' => tep_image('images/icon_info.gif', IMAGE_ICON_INFO) . '&nbsp;<strong>' . TEXT_INFO_VERSION . '</strong> ' . $sversion . ' (<a href="http://sig.oscommerce.com/' . $mInfo->signature . '" target="_blank">' . TEXT_INFO_ONLINE_STATUS . '</a>)');
              }

              if (isset($mInfo->api_version)) {
                $contents[] = array('text' => tep_image('images/icon_info.gif', IMAGE_ICON_INFO) . '&nbsp;<strong>' . TEXT_INFO_API_VERSION . '</strong> ' . $mInfo->api_version);
              }

              $contents[] = array('text' => $mInfo->description);
            }
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
