<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class hook_admin_contentModuleTemplateUpdate_contentmoduletemplateupdate {

    function listen_saveModuleFile() {
      global $_POST;
      
      if ( isset( $_POST['template_file_name'] ) && isset( $_POST['template_file_contents'] ) ) {
          $file = $_POST['template_file_name'];

          if (file_exists($file) && tep_is_writable($file)) {
            $new_file = fopen($file, 'w');
            $file_contents = stripslashes($_POST['template_file_contents']);
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
      
      $template_file = explode( '_', $_GET['module'] );
      $template_file = array_diff( $template_file, array( $template_file[0], $template_file[1] ) );
      $template_file = implode( '_', $template_file );
      
      $module_file_name = DIR_FS_CATALOG_MODULES . 'content/' . $module_group . '/templates/' . $template_file . '.php';
      
      if (file_exists($module_file_name)) { 
        $file_array = file($module_file_name);
        $contents   = implode('', $file_array);
        if (tep_is_writable($module_file_name)) {
            $output = '<br><br><strong>Edit Template File</strong><br>Edit the content module template file here<br><br>' . tep_draw_textarea_field('template_file_contents', 'soft', '80', '25', $contents, ' style="width: 99%;min-width:600px;" id="template"');
            $output .= tep_draw_hidden_field('template_file_name', $module_file_name);
            //EditArea found at http://www.cdolivet.com/editarea/
            $output .= '<script>if (typeof editAreaLoader == \'undefined\') {';
            $output .= 'document.write( unescape(\'%3Cscript src="includes/javascript/edit_area/edit_area_full.js" type="text/javascript"%3E%3C/script%3E\') );';
            $output .= '}</script>';
            $output .= '<script>';
            $output .= 'editAreaLoader.init({
			             id: "template"	// id of the textarea to transform		
			             ,start_highlight: true	// if start with highlight
			             ,allow_resize: "both"
			             ,allow_toggle: true
			             ,word_wrap: true
			             ,language: "en"
			             ,syntax: "php"	
                         ,min_width: 600
                        });';
            $output .= '</script>';
        }
      }
      
      return $output;
    }
    
  }
?>