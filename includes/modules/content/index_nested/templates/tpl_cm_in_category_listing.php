<div class="col-sm-<?php echo $content_width; ?> cm-in-category-listing">
  <div itemscope itemtype="http://schema.org/ItemList">
    <meta itemprop="itemListOrder" content="http://schema.org/ItemListUnordered" />
    <meta itemprop="name" content="<?php echo $category_name; ?>" />
    
    <?php
    foreach ($category_array as $k => $v) {
      echo '<div class="col-sm-' . $category_width . '">';
      echo   '<div class="text-center">';
      echo     '<a href="' . tep_href_link('index.php', 'cPath=' . $v['id']) . '">' . tep_image('images/' . $v['image'], htmlspecialchars($v['title']), SUBCATEGORY_IMAGE_WIDTH, SUBCATEGORY_IMAGE_HEIGHT) . '</a>';
      echo     '<div class="caption text-center">';
      echo       '<h5><a href="' . tep_href_link('index.php', 'cPath=' . $v['id']) . '"><span itemprop="itemListElement">' . $v['title'] . '</span></a></h5>';
      echo     '</div>';
      echo   '</div>';
      echo '</div>';
    }
    ?>
  </div>
</div>