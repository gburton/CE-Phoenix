<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/
  global $cl_box_groups;
  //global $files;
  if (tep_session_is_registered('admin')) {
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
          include(DIR_FS_ADMIN . 'includes/languages/' . $language . '/modules/boxes/' . $file);
        }

        include($dir->path . '/' . $file);
      }
    }

    function tep_sort_admin_boxes($a, $b) {
      return strcasecmp(strip_tags($a['heading']), strip_tags($b['heading']));
    }

    usort($cl_box_groups, 'tep_sort_admin_boxes');

    function tep_sort_admin_boxes_links($a, $b) {
      return strcasecmp(strip_tags($a['title']), strip_tags($b['title']));
    }

    foreach ( $cl_box_groups as &$group ) {
      usort($group['apps'], 'tep_sort_admin_boxes_links');
    }
    
    //$aID = $admin['id'];
    $aID = $_SESSION['admin']['id'];
    
    $sql_selected = 'SELECT * FROM administrators_acl WHERE aID="'.$aID.'" group by menu_heading,page_name ';
    $result = tep_db_query( $sql_selected );
    $blocked_urls = array();
    if( tep_db_num_rows( $result ) > 0 )
    {
        $menu_heading = '';
        $h = 0;
        while( $row = tep_db_fetch_array( $result ) )
        {
            $path = explode('&',$row['blocked_url']);
            $menu_heading = $row['menu_heading'];
            $blocked_urls[$menu_heading][] = $path[0];
        }
    }
?>

<div class="col-md-3 col-lg-2 pt-2">
  <div id="adminAppMenu">

  <?php
  
     foreach ($cl_box_groups as $groups)
      {
         $title = explode('</i>',$groups['heading']);
          $heading = $title[1];	
          
          if(!isset($blocked_urls[$heading])) $blocked_urls[$heading] = array();
          
          if( sizeof($groups['apps']) != sizeof($blocked_urls[$heading]) )
          {
              echo '<h3><a href="#">' . $groups['heading'] . '</a></h3>' .
                  '<div><ul>';
              
              foreach ($groups['apps'] as $app)
              {
                  if(!(in_array($app['link'], $blocked_urls[$heading], true)) )
                      echo '<li><a href="' . $app['link'] . '">' . $app['title'] . '</a></li>';
              }
              
              echo '</ul></div>';
          }
      }
          
  ?>

  </div>
  <script>
  $('#adminAppMenu').accordion({
    heightStyle: 'content',
    collapsible: true,

  <?php
      $counter = 0;
      foreach ($cl_box_groups as $groups) {
        foreach ($groups['apps'] as $app) {
          if ($app['code'] == $PHP_SELF) {
            break 2;
          }
        }

        $counter++;
      }

      echo 'active: ' . (isset($app) && ($app['code'] == $PHP_SELF) ? $counter : 'false');
  ?>

  });
  </script>
</div>

<?php
  }
?>
