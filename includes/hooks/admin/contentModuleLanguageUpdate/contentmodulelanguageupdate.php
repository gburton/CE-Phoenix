<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class hook_admin_contentModuleLanguageUpdate_contentmodulelanguageupdate {

    function listen_saveModuleFile() {
      global $_POST;
      
      if ( isset( $_POST['file_name'] ) && isset( $_POST['file_contents'] ) ) {
          $file = $_POST['file_name'];

          if (file_exists($file) && tep_is_writable($file)) {
            $new_file = fopen($file, 'w');
            $file_contents = stripslashes($_POST['file_contents']);
            fwrite($new_file, $file_contents, strlen($file_contents));
            fclose($new_file);
          }
      }
      
    }
    
    function listen_outputModuleFileContents() {
        global $_GET, $language, $modules;
        
        $output = '';
        
        foreach ( $modules['installed'] as $array_key => $array ) {
        foreach ( $array as $key => $value ) {
            if ( $key == 'code' && $value == $_GET['module'] ){
                $module_group = $modules['installed'][$array_key]['group'];
            }
        }
      }
      
      $module_file_name = DIR_FS_CATALOG_LANGUAGES . $language . '/modules/content/' . $module_group . '/' . $_GET['module'] . '.php';
      
      if (file_exists($module_file_name)) { 
        $file_array = file($module_file_name);
        $contents   = implode('', $file_array);
        if (tep_is_writable($module_file_name)) {
            require_once DIR_WS_LANGUAGES . $language . '/define_language.php'; //helper text TEXT_EDIT_NOTE
            $output = '<br><br><strong>Edit Language File</strong><br>Edit the content module language file here<br><br>' . TEXT_EDIT_NOTE . tep_draw_textarea_field('file_contents', 'soft', '80', '25', $contents, ' style="width: 99%;min-width:600px;"');
            $output .= tep_draw_hidden_field('file_name', $module_file_name);
        }
      }
      
      return $output;
    }
    
  }
?>