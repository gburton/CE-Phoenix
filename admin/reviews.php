<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = $_GET['action'] ?? '';
  
  $OSCOM_Hooks->call('reviews', 'reviewPreAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'setflag':
        tep_set_review_status($_GET['rID'], $_GET['flag']);
        
        $OSCOM_Hooks->call('reviews', 'reviewActionStatus');

        tep_redirect(tep_href_link('reviews.php', 'page=' . $_GET['page'] . '&rID=' . $_GET['rID']));
        break;
      case 'update':
        $reviews_id = tep_db_prepare_input($_GET['rID']);
        $reviews_rating = tep_db_prepare_input($_POST['reviews_rating']);
        $reviews_text = tep_db_prepare_input($_POST['reviews_text']);
        $reviews_status = tep_db_prepare_input($_POST['reviews_status']);

        tep_db_query("update reviews set reviews_rating = '" . tep_db_input($reviews_rating) . "', reviews_status = '" . tep_db_input($reviews_status) . "', last_modified = now() where reviews_id = '" . (int)$reviews_id . "'");
        tep_db_query("update reviews_description set reviews_text = '" . tep_db_input($reviews_text) . "' where reviews_id = '" . (int)$reviews_id . "'");
        
        $OSCOM_Hooks->call('reviews', 'reviewActionUpdate');

        tep_redirect(tep_href_link('reviews.php', 'page=' . $_GET['page'] . '&rID=' . $reviews_id));
        break;
      case 'deleteconfirm':
        $reviews_id = tep_db_prepare_input($_GET['rID']);

        tep_db_query("delete from reviews where reviews_id = '" . (int)$reviews_id . "'");
        tep_db_query("delete from reviews_description where reviews_id = '" . (int)$reviews_id . "'");
        
        $OSCOM_Hooks->call('reviews', 'reviewActionDelete');

        tep_redirect(tep_href_link('reviews.php', 'page=' . $_GET['page']));
        break;
      case 'addnew':
        $products_id = tep_db_prepare_input($_POST['products_id']);
        $customers_id = tep_db_prepare_input($_POST['customer_id']);
        $review = tep_db_prepare_input($_POST['reviews_text']);
        $rating = tep_db_prepare_input($_POST['reviews_rating']);

        tep_db_query("insert into reviews (products_id, customers_id, customers_name, reviews_rating, date_added, reviews_status) values ('" . (int)$products_id . "', '" . (int)$customers_id . "', '" . tep_customers_name($customers_id) . "', '" . (int)$rating . "', now(), 1)");
        $insert_id = tep_db_insert_id();
        tep_db_query("insert into reviews_description (reviews_id, languages_id, reviews_text) values ('" . (int)$insert_id . "', '" . (int)$languages_id . "', '" . $review . "')");
        
        $OSCOM_Hooks->call('reviews', 'reviewActionSave');

        tep_redirect(tep_href_link('reviews.php', tep_get_all_get_params(['action'])));
        break;   
    }
  }
  
  $OSCOM_Hooks->call('reviews', 'reviewPostAction');

  require('includes/template_top.php');
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?php
      if (empty($action)) {
        echo tep_draw_bootstrap_button(IMAGE_BUTTON_ADD_REVIEW, 'fas fa-star', tep_href_link('reviews.php', 'action=new'), null, null, 'btn-danger xxx text-white');
      }
      else if ($action == "new") {
        echo tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('reviews.php'), null, null, 'btn-light mt-2');
      }
      ?>
    </div>
  </div>

  <?php
  if ( ($action == 'new') || ($action == 'edit') ) {
    $form_action = 'addnew';
    if ( ($action == 'edit') && isset($_GET['rID']) ) {
      $form_action = 'preview';

      $rID = tep_db_prepare_input($_GET['rID']);

      $reviews_query = tep_db_query("select r.*, rd.* from reviews r, reviews_description rd where r.reviews_id = '" . (int)$rID . "' and r.reviews_id = rd.reviews_id");
      $reviews = tep_db_fetch_array($reviews_query);

      $products_query = tep_db_query("select products_image from products where products_id = '" . (int)$reviews['products_id'] . "'");
      $products = tep_db_fetch_array($products_query);

      $products_name_query = tep_db_query("select products_name from products_description where products_id = '" . (int)$reviews['products_id'] . "' and language_id = '" . (int)$languages_id . "'");
      $products_name = tep_db_fetch_array($products_name_query);

      $rInfo_array = array_merge($reviews, $products, $products_name);
      $rInfo = new objectInfo($rInfo_array);

      if (!isset($rInfo->reviews_status)) $rInfo->reviews_status = '1';
      switch ($rInfo->reviews_status) {
        case '0': $in_status = false; $out_status = true; break;
        case '1':
        default: $in_status = true; $out_status = false;
      }
    } else {
      $rInfo = new objectInfo([]);
    }
    
    if ($form_action == 'preview') {
      echo tep_draw_form('review', 'reviews.php', 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=' . $form_action);
      echo tep_draw_hidden_field('reviews_id', $rInfo->reviews_id) . tep_draw_hidden_field('reviews_status', $rInfo->reviews_status) . tep_draw_hidden_field('products_id', $rInfo->products_id) . tep_draw_hidden_field('products_image', $rInfo->products_image) . tep_draw_hidden_field('date_added', $rInfo->date_added); 
    } else {
      echo tep_draw_form('review', 'reviews.php', 'action=' . $form_action);
    }
?>
    <div class="form-group row">
      <label for="reviewProduct" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_PRODUCT; ?></label>
      <div class="col-sm-9">
        <?php 
        if (isset($rInfo->products_name)) { 
          echo tep_draw_input_field('products_name', $rInfo->products_name, 'readonly class="form-control-plaintext"'); 
        } 
        else { 
          echo tep_draw_products('products_id', 'id="reviewProduct" class="form-control" required aria-required="true"'); 
        } 
        ?>     
      </div>
    </div>

    <div class="form-group row">
      <label for="reviewCustomer" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_FROM; ?></label>
      <div class="col-sm-9">
        <?php 
        if (isset($rInfo->customers_name)) { 
          echo tep_draw_input_field('customers_name', $rInfo->customers_name, 'readonly class="form-control-plaintext"'); 
        } 
        else { 
          echo tep_draw_customers('customer_id', 'id="reviewCustomer" class="form-control" required aria-required="true"'); 
        } 
        ?>     
      </div>
    </div>

    <div class="form-group row">
      <label for="reviewRating" class="col-sm-3 text-left text-sm-right"><?php echo ENTRY_RATING; ?></label>
      <div class="col-sm-9"><div class="form-check form-check-inline">
        <label class="form-check-label font-weight-bold text-danger mr-1" for="rating_1"><?php echo TEXT_BAD; ?></label>
        <?php 
        for ($i=1; $i<=5; $i++) { 
          echo tep_draw_selection_field('reviews_rating', 'radio', $i, (isset($rInfo->reviews_rating)?(($i == $rInfo->reviews_rating) ? true : false) : ($i == 5) ? true : false), 'class="form-check-input" id="rating_' . $i . '"'); 
        } 
        ?>
        <label class="form-check-label font-weight-bold text-danger" for="rating_5"><?php echo TEXT_GOOD; ?></label></div>   
      </div>
    </div>

    <div class="form-group row">
      <label for="reviewReview" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_REVIEW; ?></label>
      <div class="col-sm-9"><?php echo tep_draw_textarea_field('reviews_text', null, null, '5', $rInfo->reviews_text ?? '', 'class="form-control" required aria-required="true"') . ENTRY_REVIEW_TEXT; ?>     
      </div>
    </div>

    <?php
    echo $OSCOM_Hooks->call('reviews', 'reviewFormEdit');
    echo $OSCOM_Hooks->call('reviews', 'reviewFormNew');
    ?>
 
    <div class="text-right">
      <?php 
      if ($action != 'new') {
        echo tep_draw_bootstrap_button(IMAGE_PREVIEW, 'fas fa-eye', null, null, null, 'btn-info mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('reviews.php', 'page=' . $_GET['page'] . '&rID=' . $_GET['rID']), null, null, 'btn-light');
      } else {
        echo tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2');
      }
      ?>
    </div>
    
  </form>
<?php
  } elseif ($action == 'preview') {
    if (tep_not_null($_POST)) {
      $rInfo = new objectInfo($_POST);
      echo tep_draw_form('update', 'reviews.php', 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=update', 'post', 'enctype="multipart/form-data"');
    } else {
      $rID = tep_db_prepare_input($_GET['rID']);

      $reviews_query = tep_db_query("select r.*, rd.* from reviews r, reviews_description rd where r.reviews_id = '" . (int)$rID . "' and r.reviews_id = rd.reviews_id");
      $reviews = tep_db_fetch_array($reviews_query);

      $products_query = tep_db_query("select products_image from products where products_id = '" . (int)$reviews['products_id'] . "'");
      $products = tep_db_fetch_array($products_query);

      $products_name_query = tep_db_query("select products_name from products_description where products_id = '" . (int)$reviews['products_id'] . "' and language_id = '" . (int)$languages_id . "'");
      $products_name = tep_db_fetch_array($products_name_query);

      $rInfo_array = array_merge($reviews, $products, $products_name);
      $rInfo = new objectInfo($rInfo_array);
    }
?>
    <div class="row">
      <div class="col-sm-10">
        <div class="form-group row">
          <label for="reviewProduct" class="col-sm-3 text-left text-sm-right"><?php echo ENTRY_PRODUCT; ?></label>
          <div class="col-sm-9"><?php echo $rInfo->products_name; ?></div>
        </div>

        <div class="form-group row">
          <label for="reviewCustomer" class="col-sm-3 text-left text-sm-right"><?php echo ENTRY_FROM; ?></label>
          <div class="col-sm-9"><?php echo $rInfo->customers_name; ?></div>
        </div>

        <div class="form-group row">
          <label for="reviewDate" class="col-sm-3 text-left text-sm-right"><?php echo ENTRY_DATE; ?></label>
          <div class="col-sm-9"><?php echo tep_date_short($rInfo->date_added); ?></div>
        </div>

        <div class="form-group row">
          <label for="reviewRating" class="col-sm-3 text-left text-sm-right"><?php echo ENTRY_RATING; ?></label>
          <div class="col-sm-9"><?php echo tep_draw_stars($rInfo->reviews_rating); ?></div>   
        </div>

        <div class="form-group row">
          <label for="reviewReview" class="col-sm-3 text-left text-sm-right"><?php echo ENTRY_REVIEW; ?></label>
          <div class="col-sm-9"><?php echo $rInfo->reviews_text; ?></div>
        </div>
      </div>
      <div class="col-sm-2 text-right"><?php echo tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . $rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT); ?></div>
    </div>

<?php
    echo $OSCOM_Hooks->call('reviews', 'reviewFormPreview');
    
    if (tep_not_null($_POST)) {
/* Re-Post all POST'ed variables */
      foreach($_POST as $key => $value) {
        echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
      }
?>
    <div class="text-right">
        <?php echo tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('reviews.php', 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id), null, null, 'btn-light'); ?>
    </div>
  </form>
<?php
    } else {
      if (isset($_GET['origin'])) {
        $back_url = $_GET['origin'];
        $back_url_params = '';
      } else {
        $back_url = 'reviews.php';
        $back_url_params = 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id;
      }
?>
    <div class="text-right">
      <?php echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link($back_url, $back_url_params), null, null, 'btn-light'); ?>
    </div>
<?php
    }
  } else {
?>
  <div class="row no-gutters">
    <div class="col">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_RATING; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_DATE_ADDED; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_STATUS; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $reviews_query_raw = "select * from reviews order by date_added DESC";
            $reviews_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $reviews_query_raw, $reviews_query_numrows);
            $reviews_query = tep_db_query($reviews_query_raw);
            while ($reviews = tep_db_fetch_array($reviews_query)) {
              if ((!isset($_GET['rID']) || (isset($_GET['rID']) && ($_GET['rID'] == $reviews['reviews_id']))) && !isset($rInfo)) {
                $reviews_text_query = tep_db_query("select r.*, rd.*, length(rd.reviews_text) as reviews_text_size from reviews r, reviews_description rd where r.reviews_id = '" . (int)$reviews['reviews_id'] . "' and r.reviews_id = rd.reviews_id");
                $reviews_text = tep_db_fetch_array($reviews_text_query);

                $products_image_query = tep_db_query("select products_image from products where products_id = '" . (int)$reviews['products_id'] . "'");
                $products_image = tep_db_fetch_array($products_image_query);

                $products_name_query = tep_db_query("select products_name from products_description where products_id = '" . (int)$reviews['products_id'] . "' and language_id = '" . (int)$languages_id . "'");
                $products_name = tep_db_fetch_array($products_name_query);

                $reviews_average_query = tep_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from reviews where products_id = '" . (int)$reviews['products_id'] . "'");
                $reviews_average = tep_db_fetch_array($reviews_average_query);

                $review_info = array_merge($reviews_text, $reviews_average, $products_name);
                $rInfo_array = array_merge($reviews, $review_info, $products_image);
                $rInfo = new objectInfo($rInfo_array);
              }

              if (isset($rInfo) && is_object($rInfo) && ($reviews['reviews_id'] == $rInfo->reviews_id) ) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('reviews.php', 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=preview') . '\'">' . "\n";
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('reviews.php', 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id']) . '\'">' . "\n";
              }
              ?>
                <td><?php echo tep_get_products_name($reviews['products_id']); ?></td>
                <td class="text-center"><?php echo tep_draw_stars($reviews['reviews_rating']); ?></td>
                <td class="text-right"><?php echo tep_date_short($reviews['date_added']); ?></td>
                <td class="text-center"><?php if ($reviews['reviews_status'] == '1') { echo '<i class="fas fa-check-circle text-success"></i> <a href="' . tep_href_link('reviews.php', 'action=setflag&flag=0&rID=' . $reviews['reviews_id'] . '&page=' . $_GET['page']) . '"><i class="fas fa-times-circle text-muted"></i></a>'; } else { echo '<a href="' . tep_href_link('reviews.php', 'action=setflag&flag=1&rID=' . $reviews['reviews_id'] . '&page=' . $_GET['page']) . '"><i class="fas fa-check-circle text-muted"></i></a> <i class="fas fa-times-circle text-danger"></i>'; } ?></td>
                <td class="text-right"><?php if (isset($rInfo) && is_object($rInfo) && ($reviews['reviews_id'] == $rInfo->reviews_id)) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('reviews.php', 'page=' . $_GET['page'] . '&rID=' . $reviews['reviews_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
              <?php
            }
            ?>
          </tbody>
        </table>
      </div>
      
      <div class="row my-1">
        <div class="col"><?php echo $reviews_split->display_count($reviews_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></div>
        <div class="col text-right mr-2"><?php echo $reviews_split->display_links($reviews_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
    </div>
    
<?php
    $heading = [];
    $contents = [];

    switch ($action) {
      case 'delete':
        $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_REVIEW];

        $contents = ['form' => tep_draw_form('reviews', 'reviews.php', 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=deleteconfirm')];
        $contents[] = ['text' => TEXT_INFO_DELETE_REVIEW_INTRO];
        $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $rInfo->products_name];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('reviews.php', 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id), null, null, 'btn-light')];
        break;
      default:
      if (isset($rInfo) && is_object($rInfo)) {
        $heading[] = ['text' => $rInfo->products_name];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('reviews.php', 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('reviews.php', 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=delete'), null, null, 'btn-danger xxx text-white')];
        $contents[] = ['text' => sprintf(TEXT_INFO_DATE_ADDED, tep_date_short($rInfo->date_added))];
        if (tep_not_null($rInfo->last_modified)) $contents[] = ['text' => sprintf(TEXT_INFO_LAST_MODIFIED, tep_date_short($rInfo->last_modified))];
        $contents[] = ['text' => tep_info_image($rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT)];
        $contents[] = ['text' => sprintf(TEXT_INFO_REVIEW_AUTHOR, $rInfo->customers_name)];
        $contents[] = ['text' => sprintf(TEXT_INFO_REVIEW_RATING, tep_draw_stars($rInfo->reviews_rating))];
        $contents[] = ['text' => sprintf(TEXT_INFO_REVIEW_READ, $rInfo->reviews_read)];
        $contents[] = ['text' => sprintf(TEXT_INFO_REVIEW_SIZE, $rInfo->reviews_text_size)];
        $contents[] = ['text' => sprintf(TEXT_INFO_PRODUCTS_AVERAGE_RATING, number_format($rInfo->average_rating, 2))];
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

  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
