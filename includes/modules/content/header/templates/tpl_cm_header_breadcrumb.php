<div class="col-sm-<?php echo $content_width; ?> cm-header-breadcrumb">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <?php
  foreach ($GLOBALS['breadcrumb']->trail() as $v) {
    if (isset($v['link']) && tep_not_null($v['link'])) {
      echo '<li class="breadcrumb-item"><a href="' . $v['link'] . '">' . $v['title'] . '</a></li>';
    } else {
      echo '<li class="breadcrumb-item">' . $v['title'] . '</li>';
    }
  }
?>

    </ol>
  </nav>
</div>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>
