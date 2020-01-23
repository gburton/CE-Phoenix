<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/classes/currencies.php');
  $currencies = new currencies();

  $action = $_GET['action'] ?? '';

  if (tep_not_null($action)) {
    switch ($action) {
      case 'setflag':
        tep_set_specials_status($_GET['id'], $_GET['flag']);

        tep_redirect(tep_href_link('specials.php', (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'sID=' . $_GET['id']));
        break;
      case 'insert':
        $products_id = tep_db_prepare_input($_POST['products_id']);
        $products_price = tep_db_prepare_input($_POST['products_price']);
        $specials_price = tep_db_prepare_input($_POST['specials_price']);
        $expdate = tep_db_prepare_input($_POST['expdate']);

        if (substr($specials_price, -1) == '%') {
          $new_special_insert_query = tep_db_query("select products_id, products_price from products where products_id = '" . (int)$products_id . "'");
          $new_special_insert = tep_db_fetch_array($new_special_insert_query);

          $products_price = $new_special_insert['products_price'];
          $specials_price = ($products_price - (($specials_price / 100) * $products_price));
        }

        $expires_date = '';
        if (tep_not_null($expdate)) {
          $expires_date = substr($expdate, 0, 4) . substr($expdate, 5, 2) . substr($expdate, 8, 2);
        }

        tep_db_query("insert into specials (products_id, specials_new_products_price, specials_date_added, expires_date, status) values ('" . (int)$products_id . "', '" . tep_db_input($specials_price) . "', now(), " . (tep_not_null($expires_date) ? "'" . tep_db_input($expires_date) . "'" : 'null') . ", '1')");

        tep_redirect(tep_href_link('specials.php', 'page=' . $_GET['page']));
        break;
      case 'update':
        $specials_id = tep_db_prepare_input($_POST['specials_id']);
        $products_price = tep_db_prepare_input($_POST['products_price']);
        $specials_price = tep_db_prepare_input($_POST['specials_price']);
        $expdate = tep_db_prepare_input($_POST['expdate']);

        if (substr($specials_price, -1) == '%') $specials_price = ($products_price - (($specials_price / 100) * $products_price));

        $expires_date = '';
        if (tep_not_null($expdate)) {
          $expires_date = substr($expdate, 0, 4) . substr($expdate, 5, 2) . substr($expdate, 8, 2);
        }

        tep_db_query("update specials set specials_new_products_price = '" . tep_db_input($specials_price) . "', specials_last_modified = now(), expires_date = " . (tep_not_null($expires_date) ? "'" . tep_db_input($expires_date) . "'" : 'null') . " where specials_id = '" . (int)$specials_id . "'");

        tep_redirect(tep_href_link('specials.php', 'page=' . $_GET['page'] . '&sID=' . $specials_id));
        break;
      case 'deleteconfirm':
        $specials_id = tep_db_prepare_input($_GET['sID']);

        tep_db_query("delete from specials where specials_id = '" . (int)$specials_id . "'");

        tep_redirect(tep_href_link('specials.php', 'page=' . $_GET['page']));
        break;
    }
  }

  require('includes/template_top.php');
?>

  <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ( ($action == 'new') || ($action == 'edit') ) {
    $form_action = 'insert';
    if ( ($action == 'edit') && isset($_GET['sID']) ) {
      $form_action = 'update';

      $product_query = tep_db_query("select p.products_id, pd.products_name, p.products_price, s.specials_new_products_price, s.expires_date from products p, products_description pd, specials s where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = s.products_id and s.specials_id = '" . (int)$_GET['sID'] . "'");
      $product = tep_db_fetch_array($product_query);

      $sInfo = new objectInfo($product);
    } else {
      $sInfo = new objectInfo(array());

// create an array of products on special, which will be excluded from the pull down menu of products
// (when creating a new product on special)
      $specials_array = array();
      $specials_query = tep_db_query("select p.products_id from products p, specials s where s.products_id = p.products_id");
      while ($specials = tep_db_fetch_array($specials_query)) {
        $specials_array[] = $specials['products_id'];
      }
    }
?>

  <form name="new_special" <?php echo 'action="' . tep_href_link('specials.php', tep_get_all_get_params(array('action', 'info', 'sID')) . 'action=' . $form_action) . '"'; ?> method="post">
  <?php 
  if ($form_action == 'update') echo tep_draw_hidden_field('specials_id', $_GET['sID']); 
  ?>
  
    <div class="form-group row">
      <label for="specialProduct" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo TEXT_SPECIALS_PRODUCT; ?></label>
      <div class="col-sm-9"><?php if (isset($sInfo->products_name)) { echo tep_draw_input_field('n', $sInfo->products_name . ' (' . $currencies->format($sInfo->products_price) . ')', 'readonly class="form-control-plaintext"'); } else { echo tep_draw_products_pull_down('products_id', 'id="specialProduct" class="form-control" required aria-required="true"', $specials_array); } echo tep_draw_hidden_field('products_price', (isset($sInfo->products_price) ? $sInfo->products_price : '')); ?>     
      </div>
    </div>
    
    <div class="form-group row">
      <label for="specialPrice" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo TEXT_SPECIALS_SPECIAL_PRICE; ?></label>
      <div class="col-sm-9">
        <?php 
        echo tep_draw_input_field('specials_price', (isset($sInfo->specials_new_products_price) ? $sInfo->specials_new_products_price : ''), 'required aria-required="true" class="form-control" id="specialPrice"', null, 'tel'); 
        ?>    
      </div>
    </div>
    
    <div class="form-group row">
      <label for="expdate" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo TEXT_SPECIALS_EXPIRES_DATE; ?></label>
      <div class="col-sm-9">
        <?php 
        echo tep_draw_input_field('expdate', (tep_not_null($sInfo->expires_date) ? substr($sInfo->expires_date, 0, 4) . '-' . substr($sInfo->expires_date, 5, 2) . '-' . substr($sInfo->expires_date, 8, 2) : ''), 'class="form-control" id="expdate"'); 
        ?>    
      </div>
    </div>
    
    <div class="alert alert-info">
      <?php echo TEXT_SPECIALS_PRICE_TIP; ?>
    </div>
    
    <?php 
    echo tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success btn-block btn-lg');
    echo tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('specials.php'), null, null, 'btn-light mt-2'); 
    ?>

  </form>

  <script>$('#expdate').datepicker({ dateFormat: 'yy-mm-dd' });</script>

<?php
  } else {
?>

  <div class="row no-gutters">
    <div class="col">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
              <th><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></th>
              <th><?php echo TABLE_HEADING_SPECIAL_PRICE; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_STATUS; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $specials_query_raw = "select p.*, pd.*, s.* from products p, specials s, products_description pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = s.products_id order by pd.products_name";
            $specials_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $specials_query_raw, $specials_query_numrows);
            $specials_query = tep_db_query($specials_query_raw);
            while ($specials = tep_db_fetch_array($specials_query)) {
              if ((!isset($_GET['sID']) || (isset($_GET['sID']) && ($_GET['sID'] == $specials['specials_id']))) && !isset($sInfo)) {
                $products_query = tep_db_query("select products_image from products where products_id = '" . (int)$specials['products_id'] . "'");
                $products = tep_db_fetch_array($products_query);
                $sInfo_array = array_merge($specials, $products);
                $sInfo = new objectInfo($sInfo_array);
              }

              if (isset($sInfo) && is_object($sInfo) && ($specials['specials_id'] == $sInfo->specials_id)) {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('specials.php', 'page=' . (int)$_GET['page'] . '&sID=' . (int)$sInfo->specials_id . '&action=edit') . '\'">' . "\n";
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('specials.php', 'page=' . (int)$_GET['page'] . '&sID=' . (int)$specials['specials_id']) . '\'">' . "\n";
              }
              ?>
                <td><?php echo $specials['products_name']; ?></td>
                <td><?php echo $currencies->format($specials['products_price']); ?></td>
                <td class="text-danger"><?php echo $currencies->format($specials['specials_new_products_price']); ?></td>
                <td class="text-right"><?php if ($specials['status'] == '1') { echo '<i class="fas fa-check-circle text-success"></i> <a href="' . tep_href_link('specials.php', 'action=setflag&flag=0&id=' . (int)$specials['specials_id']) . '"><i class="fas fa-times-circle text-muted"></i></a>'; } else { echo '<a href="' . tep_href_link('specials.php', 'action=setflag&flag=1&id=' . (int)$specials['specials_id']) . '"><i class="fas fa-check-circle text-muted"></i></a> <i class="fas fa-times-circle text-danger"></i>'; } ?></td>
                <td class="text-right"><?php if (isset($sInfo) && is_object($sInfo) && ($specials['specials_id'] == $sInfo->specials_id)) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('specials.php', 'page=' . $_GET['page'] . '&sID=' . $specials['specials_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
<?php
    }
?>
          </tbody>
        </table>
      </div>
      
      <div class="row my-1">
        <div class="col"><?php echo $specials_split->display_count($specials_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SPECIALS); ?></div>
        <div class="col text-right mr-2"><?php echo $specials_split->display_links($specials_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
      
      <?php
      if (empty($action)) {
        echo '<div class="buttonSet mr-2 mt-2">';
          echo tep_draw_bootstrap_button(BUTTON_INSERT_SPECIAL, 'fas fa-funnel-dollar', tep_href_link('specials.php', 'page=' . $_GET['page'] . '&action=new'), null, null, 'btn-success btn-block btn-lg xxx text-white');
        echo '</div>';
      }
      ?>
      
    </div>

<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'delete':
      $heading[] = array('text' => TEXT_INFO_HEADING_DELETE_SPECIALS);

      $contents = array('form' => tep_draw_form('specials', 'specials.php', 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<strong>' . $sInfo->products_name . '</strong>');
      $contents[] = array('class' => 'text-center', 'text' => tep_draw_button(IMAGE_DELETE, 'trash', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('specials.php', 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id)));
      break;
    default:
      if (is_object($sInfo)) {
        $heading[] = array('text' => $sInfo->products_name);

        $contents[] = array('class' => 'text-center', 'text' => tep_draw_button(IMAGE_EDIT, 'document', tep_href_link('specials.php', 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=edit')) . tep_draw_button(IMAGE_DELETE, 'trash', tep_href_link('specials.php', 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=delete')));
        $contents[] = array('text' => TEXT_INFO_DATE_ADDED . ' ' . tep_date_short($sInfo->specials_date_added));
        $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . tep_date_short($sInfo->specials_last_modified));
        $contents[] = array('class' => 'text-center', 'text' => tep_info_image($sInfo->products_image, $sInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT));
        $contents[] = array('text' => TEXT_INFO_ORIGINAL_PRICE . ' ' . $currencies->format($sInfo->products_price));
        $contents[] = array('text' => TEXT_INFO_NEW_PRICE . ' ' . $currencies->format($sInfo->specials_new_products_price));
        $contents[] = array('text' => TEXT_INFO_PERCENTAGE . ' ' . number_format(100 - (($sInfo->specials_new_products_price / $sInfo->products_price) * 100)) . '%');

        $contents[] = array('text' => TEXT_INFO_EXPIRES_DATE . ' <strong>' . tep_date_short($sInfo->expires_date) . '</strong>');
        $contents[] = array('text' => TEXT_INFO_STATUS_CHANGE . ' ' . tep_date_short($sInfo->date_status_change));
      }
      break;
  }
  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-3">';
      $box = new box;
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
  
  echo '</div>';  
}
?>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
