<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('reviews', 'preAction');

  if (!Text::is_empty($action)) {
    switch ($action) {
      case 'setflag':
        tep_db_query("UPDATE reviews SET reviews_status = " . (int)$_GET['flag'] . ", last_modified = NOW() WHERE reviews_id = " . (int)$_GET['rID']);

        $OSCOM_Hooks->call('reviews', 'setFlagAction');

        tep_redirect(tep_href_link('reviews.php', 'rID=' . $_GET['rID'] . (isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : '')));
        break;
      case 'update':
        $reviews_id = Text::prepare($_GET['rID']);
        $reviews_rating = Text::prepare($_POST['reviews_rating']);
        $reviews_text = Text::prepare($_POST['reviews_text']);
        $reviews_status = Text::prepare($_POST['reviews_status']);

        tep_db_query("UPDATE reviews SET reviews_rating = '" . tep_db_input($reviews_rating) . "', reviews_status = '" . tep_db_input($reviews_status) . "', last_modified = NOW() WHERE reviews_id = " . (int)$reviews_id);
        tep_db_query("UPDATE reviews_description SET reviews_text = '" . tep_db_input($reviews_text) . "' WHERE reviews_id = " . (int)$reviews_id);

        $OSCOM_Hooks->call('reviews', 'updateAction');

        tep_redirect(tep_href_link('reviews.php', 'rID=' . $reviews_id(isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : '')));
        break;
      case 'deleteconfirm':
        $reviews_id = Text::prepare($_GET['rID']);

        tep_db_query("DELETE FROM reviews WHERE reviews_id = " . (int)$reviews_id);
        tep_db_query("DELETE FROM reviews_description WHERE reviews_id = " . (int)$reviews_id);

        $OSCOM_Hooks->call('reviews', 'deleteConfirmAction');

        tep_redirect(tep_href_link('reviews.php', (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] : '')));
        break;
      case 'addnew':
        $products_id = Text::prepare($_POST['products_id']);
        $customer_id = Text::prepare($_POST['customer_id']);
        $review = Text::prepare($_POST['reviews_text']);
        $rating = Text::prepare($_POST['reviews_rating']);

        tep_db_query("INSERT INTO reviews (products_id, customers_id, customers_name, reviews_rating, date_added, reviews_status) VALUES (" . (int)$products_id . ", " . (int)$customer_id . ", '" . tep_customers_name($customer_id) . "', " . (int)$rating . ", NOW(), 1)");
        $insert_id = tep_db_insert_id();
        tep_db_query("INSERT INTO reviews_description (reviews_id, languages_id, reviews_text) VALUES (" . (int)$insert_id . ", " . (int)$_SESSION['languages_id'] . ", '" . tep_db_input($review) . "')");

        $OSCOM_Hooks->call('reviews', 'addNewAction');

        tep_redirect(tep_href_link('reviews.php', tep_get_all_get_params(['action'])));
        break;
    }
  }

  $OSCOM_Hooks->call('reviews', 'postAction');

  require 'includes/template_top.php';
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?= HEADING_TITLE ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?=
      empty($action)
      ? tep_draw_bootstrap_button(IMAGE_BUTTON_ADD_REVIEW, 'fas fa-star', tep_href_link('reviews.php', 'action=new'), null, null, 'btn-danger')
      : tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('reviews.php'), null, null, 'btn-light mt-2')
      ?>
    </div>
  </div>

  <?php
  if ( ('new' === $action) || ('edit' === $action) ) {
    $form_action = 'addnew';
    if ( ('edit' === $action) && isset($_GET['rID']) ) {
      $form_action = 'preview';

      $rID = Text::prepare($_GET['rID']);

      $reviews_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT r.*, rd.*, p.products_image, pd.products_name
 FROM reviews r
   INNER JOIN reviews_description rd ON r.reviews_id = rd.reviews_id
   LEFT JOIN products p ON p.products_id = r.products_id
   LEFT JOIN products_description pd ON r.products_id = pd.products_id AND pd.language_id = %d
 WHERE r.reviews_id = %d
 ORDER BY rd.languages_id = %d DESC
 LIMIT 1
EOSQL
        , (int)$_SESSION['languages_id'], (int)$rID, (int)$_SESSION['languages_id']));
      $rInfo = $reviews_query->fetch_object();

      if (!isset($rInfo->reviews_status)) {
        $rInfo->reviews_status = '1';
      }
    } else {
      $rInfo = new objectInfo([]);
    }

    if ('preview' === $form_action) {
      echo tep_draw_form('review', 'reviews.php', 'action=preview&rID=' . $_GET['rID'] . (isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : '')),
           tep_draw_hidden_field('reviews_id', $rInfo->reviews_id),
           tep_draw_hidden_field('reviews_status', $rInfo->reviews_status),
           tep_draw_hidden_field('products_id', $rInfo->products_id),
           tep_draw_hidden_field('products_image', $rInfo->products_image),
           tep_draw_hidden_field('date_added', $rInfo->date_added);
    } else {
      echo tep_draw_form('review', 'reviews.php', 'action=' . $form_action);
    }
?>
    <div class="form-group row" id="zProduct">
      <label for="reviewProduct" class="col-form-label col-sm-3 text-left text-sm-right"><?= ENTRY_PRODUCT ?></label>
      <div class="col-sm-9">
        <?=
        isset($rInfo->products_name)
        ? tep_draw_input_field('products_name', $rInfo->products_name, 'readonly class="form-control-plaintext"')
        : tep_draw_products('products_id', 'id="reviewProduct" class="form-control" required aria-required="true"')
        ?>
      </div>
    </div>

    <div class="form-group row" id="zCustomer">
      <label for="reviewCustomer" class="col-form-label col-sm-3 text-left text-sm-right"><?= ENTRY_FROM ?></label>
      <div class="col-sm-9">
        <?=
        isset($rInfo->customers_name)
        ? tep_draw_input_field('customers_name', $rInfo->customers_name, 'readonly class="form-control-plaintext"')
        : tep_draw_customers('customer_id', 'id="reviewCustomer" class="form-control" required aria-required="true"')
        ?>
      </div>
    </div>

    <div class="form-group row" id="zRating">
      <label for="reviewRating" class="col-sm-3 text-left text-sm-right"><?= ENTRY_RATING ?></label>
      <div class="col-sm-9"><div class="form-check form-check-inline">
        <label class="form-check-label font-weight-bold text-danger mr-1" for="rating_1"><?= TEXT_BAD ?></label>
        <?php
        for ($i = 1; $i <= 5; $i++) {
          echo tep_draw_selection_field('reviews_rating', 'radio', $i, ($i == ($rInfo->reviews_rating ?? 5)), 'class="form-check-input" id="rating_' . $i . '"');
        }
        ?>
        <label class="form-check-label font-weight-bold text-danger" for="rating_5"><?= TEXT_GOOD ?></label></div>
      </div>
    </div>

    <div class="form-group row" id="zReview">
      <label for="reviewReview" class="col-form-label col-sm-3 text-left text-sm-right"><?= ENTRY_REVIEW ?></label>
      <div class="col-sm-9"><?= tep_draw_textarea_field('reviews_text', null, '', '5', $rInfo->reviews_text ?? '', 'class="form-control" required aria-required="true"') . ENTRY_REVIEW_TEXT ?>
      </div>
    </div>

    <?= $OSCOM_Hooks->call('reviews', ('new' === $action) ? 'formNew' : 'formEdit') ?>

    <div class="text-right">
      <?=
      ('new' === $action)
      ? tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success mr-2')
      : tep_draw_bootstrap_button(IMAGE_PREVIEW, 'fas fa-eye', null, null, null, 'btn-info mr-2')
        . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('reviews.php', 'rID=' . $_GET['rID'] . (isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : '')), null, null, 'btn-light')
      ?>
    </div>

  </form>
<?php
  } elseif ('preview' === $action) {
    if ([] === $_POST) {
      $rID = Text::prepare($_GET['rID']);

      $reviews_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT r.*, rd.*, p.products_image, pd.products_name
 FROM reviews r
   INNER JOIN reviews_description rd ON r.reviews_id = rd.reviews_id
   LEFT JOIN products p ON p.products_id = r.products_id
   LEFT JOIN products_description pd ON r.products_id = pd.products_id AND pd.language_id = %d
 WHERE r.reviews_id = %d
 ORDER BY rd.languages_id = %d DESC
 LIMIT 1
EOSQL
        , (int)$_SESSION['languages_id'], (int)$rID, (int)$_SESSION['languages_id']));
      $rInfo = $reviews_query->fetch_object();
    } else {
      $rInfo = new objectInfo($_POST);
      echo tep_draw_form('update', 'reviews.php', 'action=update&rID=' . $_GET['rID'] . (isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : ''), 'post', 'enctype="multipart/form-data"');
    }
?>
    <div class="row">
      <div class="col-sm-10">
        <div class="form-group row" id="zProduct">
          <label for="reviewProduct" class="col-sm-3 text-left text-sm-right"><?= ENTRY_PRODUCT ?></label>
          <div class="col-sm-9"><?= $rInfo->products_name ?? '' ?></div>
        </div>

        <div class="form-group row" id="zCustomer">
          <label for="reviewCustomer" class="col-sm-3 text-left text-sm-right"><?= ENTRY_FROM ?></label>
          <div class="col-sm-9"><?= $rInfo->customers_name ?></div>
        </div>

        <div class="form-group row" id="zDate">
          <label for="reviewDate" class="col-sm-3 text-left text-sm-right"><?= ENTRY_DATE ?></label>
          <div class="col-sm-9"><?= tep_date_short($rInfo->date_added) ?></div>
        </div>

        <div class="form-group row" id="zRating">
          <label for="reviewRating" class="col-sm-3 text-left text-sm-right"><?= ENTRY_RATING ?></label>
          <div class="col-sm-9"><?= tep_draw_stars($rInfo->reviews_rating) ?></div>
        </div>

        <div class="form-group row" id="zReview">
          <label for="reviewReview" class="col-sm-3 text-left text-sm-right"><?= ENTRY_REVIEW ?></label>
          <div class="col-sm-9"><?= $rInfo->reviews_text ?></div>
        </div>

        <?= $OSCOM_Hooks->call('reviews', 'formPreview'); ?>
      </div>
      <div class="col-sm-2 text-right"><?= tep_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'images/' . $rInfo->products_image ?? '', $rInfo->products_name ?? '', SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) ?></div>
    </div>

<?php
    if (([] !== $_POST)) {
/* Re-Post all POST'ed variables */
      foreach($_POST as $key => $value) {
        echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
      }
?>
    <div class="text-right">
        <?= tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success mr-2'),
            tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('reviews.php', 'rID=' . $rInfo->reviews_id . (isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : '')), null, null, 'btn-light') ?>
    </div>
  </form>
<?php
    } else {
      if (isset($_GET['origin'])) {
        $back_url = $_GET['origin'];
        $back_url_params = '';
      } else {
        $back_url = 'reviews.php';
        $back_url_params = 'page=' . (int)$_GET['page'] . '&rID=' . $rInfo->reviews_id;
      }
?>
    <div class="text-right">
      <?= tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link($back_url, $back_url_params), null, null, 'btn-light') ?>
    </div>
<?php
    }
  } else {
?>
  <div class="row no-gutters">
    <div class="col-12 col-sm-8">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_PRODUCTS ?></th>
              <th class="text-center"><?= TABLE_HEADING_RATING ?></th>
              <th class="text-right"><?= TABLE_HEADING_DATE_ADDED ?></th>
              <th class="text-center"><?= TABLE_HEADING_STATUS ?></th>
              <th class="text-right"><?= TABLE_HEADING_ACTION ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $reviews_query_raw = "SELECT r.*, pd.products_name FROM reviews r LEFT JOIN products_description pd ON r.products_id = pd.products_id AND pd.language_id = " . (int)$_SESSION['languages_id'] . " ORDER BY r.date_added DESC";
            $reviews_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $reviews_query_raw, $reviews_query_numrows);
            $reviews_query = tep_db_query($reviews_query_raw);
            while ($reviews = $reviews_query->fetch_assoc()) {
              if (!isset($rInfo) && (!isset($_GET['rID']) || ($_GET['rID'] == $reviews['reviews_id']))) {
                $reviews_text_query = tep_db_query("SELECT r.*, rd.*, LENGTH(rd.reviews_text) AS reviews_text_size FROM reviews r INNER JOIN reviews_description rd ON r.reviews_id = rd.reviews_id WHERE r.reviews_id = " . (int)$reviews['reviews_id']);
                $reviews_text = $reviews_text_query->fetch_assoc();

                $products_image_query = tep_db_query("SELECT products_image FROM products WHERE products_id = " . (int)$reviews['products_id']);
                $products_image = $products_image_query->fetch_assoc();

                $reviews_average_query = tep_db_query("SELECT (AVG(reviews_rating) / 5 * 100) AS average_rating FROM reviews WHERE products_id = " . (int)$reviews['products_id']);
                $reviews_average = $reviews_average_query->fetch_assoc();

                $rInfo_array = array_merge($reviews, $reviews_text, $reviews_average, $products_image);
                $rInfo = new objectInfo($rInfo_array);
              }

              if (isset($rInfo->reviews_id) && ($reviews['reviews_id'] == $rInfo->reviews_id) ) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('reviews.php', 'page=' . (int)$_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=preview') . '\'">' . "\n";
                $icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('reviews.php', 'page=' . (int)$_GET['page'] . '&rID=' . $reviews['reviews_id']) . '\'">' . "\n";
                $icon = '<a href="' . tep_href_link('reviews.php', 'page=' . (int)$_GET['page'] . '&rID=' . $reviews['reviews_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>';
              }
              ?>
                <td><?= $reviews['products_name'] ?? '' ?></td>
                <td class="text-center"><?= tep_draw_stars($reviews['reviews_rating']) ?></td>
                <td class="text-right"><?= tep_date_short($reviews['date_added']) ?></td>
                <td class="text-center"><?=
                ($reviews['reviews_status'] == '1')
                ? '<i class="fas fa-check-circle text-success"></i> <a href="' . tep_href_link('reviews.php', 'action=setflag&flag=0&rID=' . $reviews['reviews_id'] . (isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : '')) . '"><i class="fas fa-times-circle text-muted"></i></a>'
                : '<a href="' . tep_href_link('reviews.php', 'action=setflag&flag=1&rID=' . $reviews['reviews_id'] . (isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : '')) . '"><i class="fas fa-check-circle text-muted"></i></a> <i class="fas fa-times-circle text-danger"></i>'
              ?></td>
                <td class="text-right"><?= $icon ?></td>
              </tr>
              <?php
            }
            ?>
          </tbody>
        </table>
      </div>

      <div class="row my-1">
        <div class="col"><?= $reviews_split->display_count($reviews_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS) ?></div>
        <div class="col text-right mr-2"><?= $reviews_split->display_links($reviews_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']) ?></div>
      </div>
    </div>

<?php
    $heading = [];
    $contents = [];

    switch ($action) {
      case 'delete':
        $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_REVIEW];

        $contents = ['form' => tep_draw_form('reviews', 'reviews.php', 'action=deleteconfirm&rID=' . $rInfo->reviews_id . (isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : ''))];
        $contents[] = ['text' => TEXT_INFO_DELETE_REVIEW_INTRO];
        $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $rInfo->products_name];
        $contents[] = [
          'class' => 'text-center',
          'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger mr-2')
                  . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('reviews.php', 'page=' . (int)$_GET['page'] . '&rID=' . $rInfo->reviews_id), null, null, 'btn-light'),
        ];
        break;
      default:
      if (isset($rInfo) && is_object($rInfo)) {
        $heading[] = ['text' => $rInfo->products_name];

        $contents[] = [
          'class' => 'text-center',
          'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('reviews.php', 'action=edit&rID=' . (int)$rInfo->reviews_id . (isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : '')), null, null, 'btn-warning mr-2')
                  . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('reviews.php', 'action=delete&rID=' . (int)$rInfo->reviews_id . (isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : '')), null, null, 'btn-danger'),
        ];
        $contents[] = ['text' => sprintf(TEXT_INFO_DATE_ADDED, tep_date_short($rInfo->date_added))];
        if (!Text::is_empty($rInfo->last_modified)) {
          $contents[] = ['text' => sprintf(TEXT_INFO_LAST_MODIFIED, tep_date_short($rInfo->last_modified))];
        }
        $contents[] = ['text' => tep_info_image($rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT)];
        $contents[] = ['text' => sprintf(TEXT_INFO_REVIEW_AUTHOR, $rInfo->customers_name)];
        $contents[] = ['text' => sprintf(TEXT_INFO_REVIEW_RATING, tep_draw_stars($rInfo->reviews_rating))];
        $contents[] = ['text' => sprintf(TEXT_INFO_REVIEW_READ, $rInfo->reviews_read)];
        $contents[] = ['text' => sprintf(TEXT_INFO_REVIEW_SIZE, $rInfo->reviews_text_size)];
        $contents[] = ['text' => sprintf(TEXT_INFO_PRODUCTS_AVERAGE_RATING, number_format($rInfo->average_rating, 2))];
      }
    }

    if ( ([] !== $heading) && ([] !== $contents) ) {
      echo '<div class="col-12 col-sm-4">';
        $box = new box();
        echo $box->infoBox($heading, $contents);
      echo '</div>';
    }

    echo '</div>';
  }

  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
