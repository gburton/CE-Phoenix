<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  if ($messageStack->size > 0) {
    echo $messageStack->output();
  }
?>


  <div class="col-sm-4">
    <?php echo '<a href="' . tep_href_link('index.php') . '">' . tep_image('images/oscommerce.png', 'OSCOM CE Phoenix v' . tep_get_version()) . '</a>'; ?>
  </div>

  <div class="col-sm-8">
    <ul class="nav justify-content-end">
      <?php 
      echo '<li class="nav-item"><a class="nav-link" target="_blank" href="https://forums.oscommerce.com/clubs/1-phoenix/">' .tep_image('images/icon_phoenix.png', 'Phoenix') . ' ' . HEADER_TITLE_PHOENIX_CLUB . '</a></li>';      
      echo '<li class="nav-item"><a class="nav-link" href="' . tep_href_link('index.php') . '">' . HEADER_TITLE_ADMINISTRATION . '</a></li>';
      echo '<li class="nav-item"><a class="nav-link" href="' . tep_catalog_href_link() . '">' . HEADER_TITLE_ONLINE_CATALOG . '</a></li>';
      echo '<li class="nav-item"><a class="nav-link" target="_blank" href="http://www.oscommerce.com">' . HEADER_TITLE_SUPPORT_SITE . '</a></li>'; 
      echo '<li class="nav-item"><a class="nav-link text-danger" href="' . tep_href_link('login.php', 'action=logoff') . '" class="headerLink">' . sprintf(HEADER_TITLE_LOGOFF, $admin['username']) . '</a></li>'; 
      ?>
    </ul>
  </div>
  
  <div class="w-100"><hr></div>

