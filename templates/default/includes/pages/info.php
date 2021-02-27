<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/
  
  $page = info_pages::get_page(['pd.pages_id' => (int)$_GET['pages_id'],
                                'pd.languages_id' => (int)$_SESSION['languages_id'],
                                'p.pages_status' => 1]);
  
  if (sizeof($page) > 0) {
    $breadcrumb->add($page['pages_title'], tep_href_link('info.php', 'pages_id=' . (int)$page['pages_id']));
    
    require $oscTemplate->map_to_template('template_top.php', 'component');
    
    $page_content = $oscTemplate->getContent('info');
    ?>
    
    <div class="row">
      <?php echo $page_content; ?>
    </div>
    
    <?php
    require $oscTemplate->map_to_template('template_bottom.php', 'component');
  }
  else {
    tep_redirect(tep_href_link('index.php'));
  }
  ?>
  