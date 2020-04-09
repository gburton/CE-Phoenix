<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  if ($messageStack->size > 0) {
    echo $messageStack->output();
  }
  
  $sql_selected = 'SELECT * FROM administrators_acl WHERE aID="'.$admin['id'].'" group by menu_heading,page_name ';
  $result = tep_db_query( $sql_selected );
  if( tep_db_num_rows( $result ) > 0 )
  {
      while( $row = tep_db_fetch_array( $result ) )
      {
          $path = explode('&',$row['blocked_url']);
          $blocked_file = substr($path[0],strrpos($path[0],"/")+1);
          if ($PHP_SELF == $blocked_file) {
              die("<div style='width: 100%;'><center class='messageStackError'>You are not authorized to view this page.\n\n</center></div>");
          }
      }
  }
?>


  <div class="col-sm-4">
    <?php echo '<a href="' . tep_href_link('index.php') . '">' . tep_image('images/oscommerce.png', 'OSCOM CE Phoenix v' . tep_get_version()) . '</a>'; ?>
  </div>

  <div class="col-sm-8">
    <ul class="nav justify-content-end">
      <?php 
      echo '<li class="nav-item"><a class="nav-link" target="_blank" href="https://forums.oscommerce.com/clubs/1-phoenix/">' .tep_image('images/icon_phoenix.png', 'Phoenix') . ' ' . HEADER_TITLE_PHOENIX_CLUB . '</a></li>';
      echo '<li class="nav-item"><a class="nav-link" href="' . tep_href_link('certified_addons.php') . '">' . tep_image('images/icon_phoenix.png', 'Phoenix') . ' ' . HEADER_TITLE_CERTIFIED_ADDONS . '</a></li>';
      echo '<li class="nav-item"><a class="nav-link" href="' . tep_catalog_href_link() . '">' . HEADER_TITLE_ONLINE_CATALOG . '</a></li>';
      echo '<li class="nav-item"><a class="nav-link text-danger" href="' . tep_href_link('login.php', 'action=logoff') . '">' . sprintf(HEADER_TITLE_LOGOFF, $admin['username']) . '</a></li>'; 
      ?>
    </ul>
  </div>
  
  <div class="w-100"><hr></div>

