<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  $check_query = tep_db_query("select configuration_value from configuration where configuration_key = 'MODULE_CONTENT_PI_INSTALLED' limit 1");
  if (tep_db_num_rows($check_query) < 1) {
    tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Installed Modules', 'MODULE_CONTENT_PI_INSTALLED', '', 'This is automatically updated. No need to edit.', '6', '0', now())");
  }
  
  $modules_installed = (tep_not_null(MODULE_CONTENT_PI_INSTALLED) ? explode(';', MODULE_CONTENT_PI_INSTALLED) : []);
  $modules = ['installed' => [], 'new' => []];

  $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));

  if ($dir = @dir(DIR_FS_CATALOG_MODULES . 'pi/product_info/')) {
    while ($file = $dir->read()) {
      if (!is_dir(DIR_FS_CATALOG_MODULES . 'pi/product_info/' . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          $class = substr($file, 0, strrpos($file, '.'));

          if (!class_exists($class)) {
            if ( file_exists(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/pi/product_info/' . $file) ) {
              include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/pi/product_info/' . $file);
            }

            include(DIR_FS_CATALOG_MODULES . 'pi/product_info/' . $file);
          }

          if (class_exists($class)) {
            $module = new $class();

            if (in_array($file, $modules_installed)) {
              $modules['installed'][] = ['code' => $class, 'title' => $module->title, 'group' => $module->group, 'sort_order' => (int)$module->sort_order];
            } else {
              $modules['new'][] = ['code' => $class, 'title' => $module->title];
            }
          }
        }
      }
    }

    $dir->close();
  }
  
  function _sortContentModulesInstalled($a, $b) {
    return strnatcmp($a['group'] . '-' . (int)$a['sort_order'] . '-' . $a['title'], $b['group'] . '-' . (int)$b['sort_order'] . '-' . $b['title']);
  }

  function _sortContentModuleFiles($a, $b) {
    return strnatcmp($a['title'], $b['title']);
  }

  usort($modules['installed'], '_sortContentModulesInstalled');
  usort($modules['new'], '_sortContentModuleFiles');

  $_installed = [];

  foreach ( $modules['installed'] as $m ) {
    $module_installed = $m['code'] . $file_extension;
    $_installed[] = $module_installed;
  }
  
  if ( implode(';', $_installed) != MODULE_CONTENT_PI_INSTALLED ) {
    tep_db_query("update configuration set configuration_value = '" . implode(';', $_installed) . "' where configuration_key = 'MODULE_CONTENT_PI_INSTALLED'");
  }

  $action = $_GET['action'] ?? '';
  
  $OSCOM_Hooks->call('modules_pi', 'preAction');

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
        
        $OSCOM_Hooks->call('modules_pi', 'saveAction');

        tep_redirect(tep_href_link('modules_pi.php', 'module=' . $class));

        break;

      case 'install':
        $class = basename($_GET['module']);

        foreach ( $modules['new'] as $m ) {
          if ( $m['code'] == $class ) {
            $module = new $class();

            $module->install();
            
            $module_to_add = $m['code'] . $file_extension;
            $modules_installed[] = $module_to_add;
            
            tep_db_query("update configuration set configuration_value = '" . implode(';', $modules_installed) . "' where configuration_key = 'MODULE_CONTENT_PI_INSTALLED'");

            tep_redirect(tep_href_link('modules_pi.php', 'module=' . $class . '&action=edit'));
          }
        }
        
        $OSCOM_Hooks->call('modules_pi', 'installAction');

        tep_redirect(tep_href_link('modules_pi.php', 'action=list_new&module=' . $class));

        break;

      case 'remove':
        $class = basename($_GET['module']);

        foreach ( $modules['installed'] as $m ) {
          if ( $m['code'] == $class ) {
            $module = new $class();

            $module->remove();

            $modules_installed = explode(';', MODULE_CONTENT_PI_INSTALLED);
            
            $module_to_remove = $m['code'] . $file_extension; 
            if (in_array($module_to_remove, $modules_installed)) {
              unset($modules_installed[array_search($module_to_remove, $modules_installed)]);
            }

            tep_db_query("update configuration set configuration_value = '" . implode(';', $modules_installed) . "' where configuration_key = 'MODULE_CONTENT_PI_INSTALLED'");

            tep_redirect(tep_href_link('modules_pi.php'));
          }
        }
        
        $OSCOM_Hooks->call('modules_pi', 'removeAction');

        tep_redirect(tep_href_link('modules_pi.php', 'module=' . $class));

        break;
    }
  }
  
  $OSCOM_Hooks->call('modules_pi', 'postAction');

  require('includes/template_top.php');
?>

  <div class="row">
    <div class="col"><h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1></div>
    <div class="col text-right align-self-center">
      <?php
      if ($action == 'list_new') {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('modules_pi.php'), null, null, 'btn-light');
      } else {
        echo tep_draw_bootstrap_button(IMAGE_MODULE_INSTALL . ' (' . count($modules['new']) . ')', 'fas fa-plus', tep_href_link('modules_pi.php', 'action=list_new'), null, null, 'btn-danger');
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
            <?php
            if ( $action == 'list_new' ) {
              ?>
              <th><?php echo TABLE_HEADING_MODULES; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
              <?php
            } else {
              ?>
              <th><?php echo TABLE_HEADING_MODULES; ?></th>
              <th><?php echo TABLE_HEADING_GROUP; ?></th>
              <th><?php echo TABLE_HEADING_WIDTH; ?></th>
              <th><?php echo TABLE_HEADING_SORT_ORDER; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ENABLED; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
              <?php
            }
            ?>
            </tr>
          </thead>
          <tbody>
          <?php
          if ( $action == 'list_new' ) {
            foreach ( $modules['new'] as $m ) {
              $module = new $m['code']();

              if ((!isset($_GET['module']) || (isset($_GET['module']) && ($_GET['module'] == $module->code))) && !isset($mInfo)) {
                $module_info = ['code'        => $module->code,
                                'title'       => $module->title,
                                'description' => $module->description,
                                'signature'   => $module->signature ?? null,
                                'api_version' => $module->api_version ?? null];

                $mInfo = new objectInfo($module_info);
              }

              if (isset($mInfo) && is_object($mInfo) && ($module->code == $mInfo->code) ) {
                echo '<tr class="table-active">';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('modules_pi.php', 'action=list_new&module=' . $module->code) . '\'">';
              }
              ?>
                <td><?php echo $module->title; ?></td>
                <td class="text-right"><?php if (isset($mInfo) && is_object($mInfo) && ($module->code == $mInfo->code) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('modules_pi.php', 'action=list_new&module=' . $module->code) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
              <?php
            }
          }
          else {
            foreach ( $modules['installed'] as $m ) {
              $module = new $m['code']();

              if ((!isset($_GET['module']) || (isset($_GET['module']) && ($_GET['module'] == $module->code))) && !isset($mInfo)) {
                $module_info = ['code'        => $module->code,
                                'title'       => $module->title,
                                'description' => $module->description,
                                'signature'   => $module->signature ?? null,
                                'api_version' => $module->api_version ?? null,
                                'sort_order'  => (int)$module->sort_order,
                                'keys'        => []];

                foreach ($module->keys() as $key) {
                  $key = tep_db_prepare_input($key);

                  $key_value_query = tep_db_query("select configuration_title, configuration_value, configuration_description, use_function, set_function from configuration where configuration_key = '" . tep_db_input($key) . "'");
                  $key_value = tep_db_fetch_array($key_value_query);

                  $module_info['keys'][$key] = ['title'        => $key_value['configuration_title'],
                                                'value'        => $key_value['configuration_value'],
                                                'description'  => $key_value['configuration_description'],
                                                'use_function' => $key_value['use_function'],
                                                'set_function' => $key_value['set_function']];
                }

                $mInfo = new objectInfo($module_info);
              }

              if (isset($mInfo) && is_object($mInfo) && ($module->code == $mInfo->code) ) {
                echo '<tr class="table-active">';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('modules_pi.php', 'module=' . $module->code) . '\'">';
              }
              ?>
                <td><?php echo $module->title; ?></td>
                <td><?php echo ucwords(substr($module->group, -1)); ?></td>
                <td><?php echo $module->content_width; ?></td>
                <td><?php echo $module->sort_order; ?></td>
                <td class="text-right"><?php echo ($module->enabled == 1) ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'; ?></td>   
                <td class="text-right"><?php if (isset($mInfo) && is_object($mInfo) && ($module->code == $mInfo->code) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('modules_pi.php', 'module=' . $module->code) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
            <?php
            }
          }
          ?>
          </tbody>
        </table>
      </div>
      
      <p><?php echo TEXT_MODULE_DIRECTORY . ' ' . DIR_FS_CATALOG_MODULES . 'pi/product_info/'; ?></p>
      
      <p class="alert alert-danger mr-2"><?php echo SORT_ORDER_WARNING; ?></p>
      
    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'edit':
      $keys = '';

      foreach ($mInfo->keys as $key => $value) {
        $keys .= '<strong>' . $value['title'] . '</strong><br>' . $value['description'] . '<br>';
          
        if ( substr($key, -5) == 'GROUP' ) {
          include_once(DIR_FS_CATALOG . 'includes/modules/content/product_info/cm_pi_modular.php');
          $layout = call_user_func(['cm_pi_modular', 'display_layout']);
          $keys .= '<div class="alert alert-info">' . $layout . '</div>';
        }        

        if ($value['set_function']) {
          eval('$keys .= ' . $value['set_function'] . "'" . tep_db_input($value['value']) . "', '" . $key . "');");
        } else {
          $keys .= tep_draw_input_field('configuration[' . $key . ']', $value['value']);
        }        

        $keys .= '<br><br>';
      }

      $keys = html_entity_decode(stripslashes(substr($keys, 0, strrpos($keys, '<br><br>'))));

      $heading[] = ['text' => $mInfo->title];

      $contents = ['form' => tep_draw_form('modules', 'modules_pi.php', 'module=' . $mInfo->code . '&action=save')];
      $contents[] = ['text' => $keys];
      
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('modules_pi.php', 'module=' . $mInfo->code), null, null, 'btn-light')];

      break;

    default:
      if ( isset($mInfo) ) {
        $heading[] = ['text' => $mInfo->title];

        if ($action == 'list_new') {
          $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_MODULE_INSTALL, 'fas fa-plus', tep_href_link('modules_pi.php', 'module=' . $mInfo->code . '&action=install'), null, null, 'btn-warning')];

          if (isset($mInfo->api_version)) {
            $contents[] = ['text' => '<i class="fas fa-info-circle text-primary"></i> <strong>' . TEXT_INFO_API_VERSION . '</strong> ' . $mInfo->api_version];
          }

          $contents[] = ['text' => $mInfo->description];
        } else {
          $keys = '';

          foreach ($mInfo->keys as $value) {
            $keys .= '<strong>' . $value['title'] . '</strong><br>';

            if ($value['use_function']) {
              $use_function = $value['use_function'];

              if (preg_match('/->/', $use_function)) {
                $class_method = explode('->', $use_function);

                if (!isset(${$class_method[0]}) || !is_object(${$class_method[0]})) {
                  include('includes/classes/' . $class_method[0] . $file_extension);
                  ${$class_method[0]} = new $class_method[0]();
                }

                $keys .= tep_call_function($class_method[1], $value['value'], ${$class_method[0]});
              } else {
                $keys .= tep_call_function($use_function, $value['value']);
              }
            } else {
              $keys .= tep_break_string($value['value'], 40, '<br>');
            }

            $keys .= '<br><br>';
          }

          $keys = html_entity_decode(stripslashes(substr($keys, 0, strrpos($keys, '<br><br>'))));
          
          $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-plus', tep_href_link('modules_pi.php', 'module=' . $mInfo->code . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_MODULE_REMOVE, 'fas fa-minus', tep_href_link('modules_pi.php', 'module=' . $mInfo->code . '&action=remove'), null, null, 'btn-warning')];

          if (isset($mInfo->api_version)) {
            $contents[] = ['text' => '<i class="fas fa-info-circle text-primary"></i> <strong>' . TEXT_INFO_API_VERSION . '</strong> ' . $mInfo->api_version];
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
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
