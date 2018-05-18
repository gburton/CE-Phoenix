<div class="col-sm-<?php echo $content_width; ?> cm-i-upcoming-products">
  
  <table class="table table-striped table-condensed">
    <tbody>
      <tr>
        <th><?php echo MODULE_CONTENT_UPCOMING_PRODUCTS_TABLE_HEADING_PRODUCTS; ?></th>
        <th class="text-right"><?php echo MODULE_CONTENT_UPCOMING_PRODUCTS_TABLE_HEADING_DATE_EXPECTED; ?></th>
      </tr>
      <?php
      while ($expected = tep_db_fetch_array($expected_query)) {
        echo '<tr>';
        echo '  <td><a href="' . tep_href_link('product_info.php', 'products_id=' . (int)$expected['products_id']) . '">' . $expected['products_name'] . '</a></td>';
        echo '  <td class="text-right">' . tep_date_short($expected['date_expected']) . '</td>';
        echo '</tr>';        
      }
      ?>
    </tbody>
  </table>
  
</div>
         