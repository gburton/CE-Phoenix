<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

class hook_admin_siteWide_hMenu {

  public $version = '1.0.0';

  function listen_injectBodyStart() {
    global $PHP_SELF, $language, $cfgModules, $admin;

    $output = '';

    if (basename($GLOBALS['PHP_SELF']) != 'login.php') {
      $cl_box_groups = [];

      if ($dir = @dir(DIR_FS_ADMIN . 'includes/boxes')) {
        $files = [];

        while ($file = $dir->read()) {
          if (!is_dir($dir->path . '/' . $file)) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
              $files[] = $file;
            }
          }
        }

        $dir->close();

        natcasesort($files);

        foreach ( $files as $file ) {
          if ( file_exists(DIR_FS_ADMIN . 'includes/languages/' . $_SESSION['language'] . '/modules/boxes/' . $file) ) {
            include_once DIR_FS_ADMIN . 'includes/languages/' . $_SESSION['language'] . '/modules/boxes/' . $file;
          }

          include_once $dir->path . '/' . $file;
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

      $n = 1;
      $mr = '';
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

      $output .= '<nav class="navbar navbar-expand-md sticky-top navbar-dark bg-dark">';
        $output .= '<a class="navbar-brand" href="' . tep_href_link('index.php') . '">' . tep_image('images/CE-Phoenix-30-30.png', 'OSCOM CE Phoenix v' . tep_get_version(), 30, 30, null, false) . '</a>';
        $output .= '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarAdmin" aria-controls="navbarAdmin" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>';
        $output .= '<div class="collapse navbar-collapse" id="navbarAdmin">';
          $output .= '<ul class="navbar-nav mr-auto">' . $mr . '</ul>';
        $output .= '</div>';
      $output .= '</nav>';

      $output .= '<div class="col bg-light mb-1 border-bottom d-print-none">';
        $output .= '<ul class="nav justify-content-end">';
          $output .= '<li class="nav-item"><a class="nav-link" target="_blank" rel="noreferrer" href="https://forums.oscommerce.com/clubs/1-phoenix/">' .tep_image('images/icon_phoenix.png', 'Phoenix') . ' ' . HEADER_TITLE_PHOENIX_CLUB . '</a></li>';
          $output .= '<li class="nav-item"><a class="nav-link" href="' . tep_href_link('certified_addons.php') . '">' . tep_image('images/icon_phoenix.png', 'Phoenix') . ' ' . HEADER_TITLE_CERTIFIED_ADDONS . '</a></li>';
          $output .= '<li class="nav-item"><a class="nav-link" href="' . tep_catalog_href_link() . '">' . HEADER_TITLE_ONLINE_CATALOG . '</a></li>';
          $output .= '<li class="nav-item"><a class="nav-link text-danger" href="' . tep_href_link('login.php', 'action=logoff') . '">' . sprintf(HEADER_TITLE_LOGOFF, $admin['username']) . '</a></li>';
        $output .= '</ul>';
      $output .= '</div>';

      return $output;
    }
  }

}
