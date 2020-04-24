<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

class hook_admin_siteWide_hMenu {
  var $version = '1.0.0';
  
  function listen_injectBodyStart() {
    global $PHP_SELF, $language, $cfgModules;
    
    $output = null;

    if (basename($PHP_SELF) != 'login.php') {
      $cl_box_groups = array();

      if ($dir = @dir(DIR_FS_ADMIN . 'includes/boxes')) {
        $files = array();

        while ($file = $dir->read()) {
          if (!is_dir($dir->path . '/' . $file)) {
            if (substr($file, strrpos($file, '.')) == '.php') {
              $files[] = $file;
            }
          }
        }

        $dir->close();
        
        natcasesort($files);

        foreach ( $files as $file ) {
          if ( file_exists(DIR_FS_ADMIN . 'includes/languages/' . $language . '/modules/boxes/' . $file) ) {
            include_once(DIR_FS_ADMIN . 'includes/languages/' . $language . '/modules/boxes/' . $file);
          }

          include_once($dir->path . '/' . $file);
        }
      }

      function tep_sort_h_boxes($a, $b) {
        return strcasecmp(strip_tags($a['heading']), strip_tags($b['heading']));
      }

      usort($cl_box_groups, 'tep_sort_h_boxes');

      function tep_sort_h_boxes_links($a, $b) {
        return strcasecmp($a['title'], $b['title']);
      }

      foreach ( $cl_box_groups as &$group ) {
        usort($group['apps'], 'tep_sort_h_boxes_links');
      }
  
      $n = 1; $mr = null;
      foreach ($cl_box_groups as $groups) {
        $mr .= '<li class="nav-item dropdown">';
          $mr .= '<a class="nav-link dropdown-toggle" href="#" id="navbar_' . $n . '" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . $groups['heading'] . '</a>';
          $al = ($n > 6) ? ' dropdown-menu-right' : '';
          $mr .= '<div class="dropdown-menu' . $al . '" aria-labelledby="navbar_' . $n . '">';
          foreach ($groups['apps'] as $app) {        
            $mr .= '<a class="dropdown-item" href="' . $app['link'] . '">' . $app['title'] . '</a>';
          }
          $mr .= '</div>';
        $mr .= '</li>' . PHP_EOL;
        
        $n++;
      }
      
      $output .= '<nav class="navbar navbar-expand-sm sticky-top navbar-dark bg-dark">';
        $output .= '<a class="navbar-brand" href="' . tep_href_link('index.php') . '">' . tep_image('images/CE-Phoenix-30-30.png', 'OSCOM CE Phoenix v' . tep_get_version(), 30, 30) . '</a>';
        $output .= '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarAdmin" aria-controls="navbarAdmin" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>';
        $output .= '<div class="collapse navbar-collapse" id="navbarAdmin">';
          $output .= '<ul class="navbar-nav mr-auto">' . $mr . '</ul>';
        $output .= '</div>';
      $output .= '</nav>';

      return $output;
    }
  }
  
}
