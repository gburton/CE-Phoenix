<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $currencies = new currencies();

  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('specials', 'preAction');

  if (!Text::is_empty($action)) {
    switch ($action) {
      case 'setflag':
        tep_db_query("UPDATE specials SET status = '" . $_GET['flag'] . "', expires_date = NULL, date_status_change = NULL WHERE specials_id = " . (int)$_GET['id']);

        $OSCOM_Hooks->call('specials', 'setFlagAction');

        tep_redirect(tep_href_link('specials.php', (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '&' : '') . 'sID=' . $_GET['id']));
        break;
      case 'insert':
        $products_id = Text::prepare($_POST['products_id']);
        $products_price = Text::prepare($_POST['products_price']);
        $specials_price = Text::prepare($_POST['specials_price']);
        $expires_date = Text::prepare($_POST['expdate']);

        if (substr($specials_price, -1) === '%') {
          $specials_price = substr($specials_price, 0, -1);
          $new_special_insert_query = tep_db_query("SELECT products_id, products_price FROM products WHERE products_id = " . (int)$products_id);
          $new_special_insert = $new_special_insert_query->fetch_assoc();

          $products_price = $new_special_insert['products_price'];
          $specials_price = ($products_price - (($specials_price / 100) * $products_price));
        }

        if (Text::is_empty($expires_date)) {
          $expires_date = '';
        } else {
          $expires_date = substr($expires_date, 0, 4) . substr($expires_date, 5, 2) . substr($expires_date, 8, 2);
        }

        tep_db_query("INSERT INTO specials (products_id, specials_new_products_price, specials_date_added, expires_date, status) VALUES (" . (int)$products_id . ", '" . tep_db_input($specials_price) . "', NOW(), " . (Text::is_empty($expires_date) ? 'NULL' : "'" . tep_db_input($expires_date) . "'") . ", 1)");

        $OSCOM_Hooks->call('specials', 'insertAction');

        tep_redirect(tep_href_link('specials.php', isset($_GET['page']) ? 'page=' . (int)$_GET['page'] : ''));

        break;
      case 'update':
        $specials_id = Text::prepare($_POST['specials_id']);
        $products_price = Text::prepare($_POST['products_price']);
        $specials_price = Text::prepare($_POST['specials_price']);
        $expires_date = Text::prepare($_POST['expdate']);

        if (substr($specials_price, -1) === '%') {
          $specials_price = ($products_price - (($specials_price / 100) * $products_price));
        }

        if (Text::is_empty($expires_date)) {
          $expires_date = '';
        } else {
          $expires_date = substr($expires_date, 0, 4) . substr($expires_date, 5, 2) . substr($expires_date, 8, 2);
        }

        tep_db_query("UPDATE specials SET specials_new_products_price = '" . tep_db_input($specials_price) . "', specials_last_modified = NOW(), expires_date = " . (Text::is_empty($expires_date) ? 'NULL' : "'" . tep_db_input($expires_date) . "'") . " where specials_id = " . (int)$specials_id);

        $OSCOM_Hooks->call('specials', 'updateAction');

        tep_redirect(tep_href_link('specials.php', 'sID=' . $specials_id . isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : ''));
        break;
      case 'deleteconfirm':
        $specials_id = Text::prepare($_GET['sID']);

        tep_db_query("DELETE FROM specials WHERE specials_id = " . (int)$specials_id);

        $OSCOM_Hooks->call('specials', 'deleteConfirmAction');

        tep_redirect(tep_href_link('specials.php', isset($_GET['page']) ? 'page=' . (int)$_GET['page'] : ''));
        break;
    }
  }

  $OSCOM_Hooks->call('specials', 'postAction');

  require 'includes/template_top.php';
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?= HEADING_TITLE ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?=
      empty($action)
      ? tep_draw_bootstrap_button(BUTTON_INSERT_SPECIAL, 'fas fa-funnel-dollar', tep_href_link('specials.php', 'action=new'), null, null, 'btn-danger')
      : tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('specials.php'), null, null, 'btn-light mt-2')
      ?>
    </div>
  </div>

<?php
  if ( ($action == 'new') || ($action == 'edit') ) {
    $form_action = 'insert';
    if ( ($action == 'edit') && isset($_GET['sID']) ) {
      $form_action = 'update';

      $product_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT p.products_id, pd.products_name, p.products_price, s.specials_new_products_price, s.expires_date
 FROM products p INNER JOIN products_description pd ON p.products_id = pd.products_id and pd.language_id = %d INNER JOIN specials s ON pd.products_id = s.products_id
 WHERE s.specials_id = %d
EOSQL
        , (int)$_SESSION['languages_id'], (int)$_GET['sID']));
      $product = $product_query->fetch_assoc();

      $sInfo = new objectInfo($product);
    } else {
      $sInfo = new objectInfo([]);

// create an array of products on special, which will be excluded from the pull down menu of products
// (when creating a new product on special)
      $specials = [];
      $specials_query = tep_db_query("SELECT p.products_id FROM products p INNER JOIN specials s ON s.products_id = p.products_id");
      while ($special = $specials_query->fetch_assoc()) {
        $specials[] = $special['products_id'];
      }
    }
?>

  <form name="new_special" action="<?= tep_href_link('specials.php', tep_get_all_get_params(['action', 'info', 'sID']) . 'action=' . $form_action) ?>" method="post">
  <?php
  if ('update' === $form_action) {
    echo tep_draw_hidden_field('specials_id', $_GET['sID']);
  }
  ?>

    <div class="form-group row" id="zProduct">
      <label for="specialProduct" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_SPECIALS_PRODUCT ?></label>
      <div class="col-sm-9"><?= (isset($sInfo->products_name) ? tep_draw_input_field('n', $sInfo->products_name . ' (' . $currencies->format($sInfo->products_price) . ')', 'readonly class="form-control-plaintext"') : tep_draw_products_pull_down('products_id', 'id="specialProduct" required aria-required="true"', $specials)) . tep_draw_hidden_field('products_price', ($sInfo->products_price ?? '')) ?>
      </div>
    </div>

    <div class="form-group row" id="zPrice">
      <label for="specialPrice" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_SPECIALS_SPECIAL_PRICE ?></label>
      <div class="col-sm-9">
        <?= tep_draw_input_field('specials_price', ($sInfo->specials_new_products_price ?? ''), 'required aria-required="true" class="form-control" id="specialPrice"') ?>
      </div>
    </div>

    <div class="form-group row" id="zDate">
      <label for="specialDate" class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_SPECIALS_EXPIRES_DATE ?></label>
      <div class="col-sm-9">
        <?= tep_draw_input_field('expdate', (isset($sInfo->expires_date) ? substr($sInfo->expires_date, 0, 4) . '-' . substr($sInfo->expires_date, 5, 2) . '-' . substr($sInfo->expires_date, 8, 2) : ''), 'class="form-control" id="specialDate"') ?>
      </div>
    </div>

    <div class="alert alert-info">
      <?= TEXT_SPECIALS_PRICE_TIP ?>
    </div>

    <?=
    $OSCOM_Hooks->call('specials', 'formNew'),

    tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success btn-block btn-lg')
    ?>

  </form>

  <script>$('#specialDate').datepicker({ dateFormat: 'yy-mm-dd' });</script>

<?php
  } else {
?>

  <div class="row no-gutters">
    <div class="col-12 col-sm-8">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_PRODUCTS ?></th>
              <th><?= TABLE_HEADING_PRODUCTS_PRICE ?></th>
              <th><?= TABLE_HEADING_SPECIAL_PRICE ?></th>
              <th class="text-right"><?= TABLE_HEADING_STATUS ?></th>
              <th class="text-right"><?= TABLE_HEADING_ACTION ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $specials_query_raw = "SELECT p.*, pd.*, s.* FROM specials s INNER JOIN products p ON p.products_id = s.products_id INNER JOIN products_description pd ON p.products_id = pd.products_id AND pd.language_id = " . (int)$_SESSION['languages_id'] . " ORDER BY pd.products_name";
            $specials_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $specials_query_raw, $specials_query_numrows);
            $specials_query = tep_db_query($specials_query_raw);
            while ($special = $specials_query->fetch_assoc()) {
              if (!isset($sInfo) && (!isset($_GET['sID']) || ($_GET['sID'] == $special['specials_id']))) {
                $sInfo = new objectInfo($special);
              }

              if (isset($sInfo->specials_id) && ($special['specials_id'] == $sInfo->specials_id)) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('specials.php', 'page=' . (int)$_GET['page'] . '&sID=' . (int)$sInfo->specials_id . '&action=edit') . '\'">' . "\n";
                $icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('specials.php', 'page=' . (int)$_GET['page'] . '&sID=' . (int)$special['specials_id']) . '\'">' . "\n";
                $icon = '<a href="' . tep_href_link('specials.php', 'page=' . (int)$_GET['page'] . '&sID=' . $special['specials_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>';
              }
              ?>
                <td><?= $special['products_name'] ?></td>
                <td><?= $currencies->format($special['products_price']) ?></td>
                <td class="text-danger"><?= $currencies->format($special['specials_new_products_price']) ?></td>
                <td class="text-right"><?= ($special['status'] == '1') ? '<i class="fas fa-check-circle text-success"></i> <a href="' . tep_href_link('specials.php', 'action=setflag&flag=0&id=' . (int)$special['specials_id']) . '"><i class="fas fa-times-circle text-muted"></i></a>' : '<a href="' . tep_href_link('specials.php', 'action=setflag&flag=1&id=' . (int)$special['specials_id']) . '"><i class="fas fa-check-circle text-muted"></i></a> <i class="fas fa-times-circle text-danger"></i>' ?></td>
                <td class="text-right"><?= $icon ?></td>
              </tr>
<?php
    }
?>
          </tbody>
        </table>
      </div>

      <div class="row my-1">
        <div class="col"><?= $specials_split->display_count($specials_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SPECIALS) ?></div>
        <div class="col text-right mr-2"><?= $specials_split->display_links($specials_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']) ?></div>
      </div>

    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'delete':
      $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_SPECIALS];

      $contents = ['form' => tep_draw_form('specials', 'specials.php', 'page=' . (int)$_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=deleteconfirm')];
      $contents[] = ['text' => TEXT_INFO_DELETE_INTRO];
      $contents[] = ['text' => '<strong>' . $sInfo->products_name . '</strong>'];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('specials.php', 'page=' . (int)$_GET['page'] . '&sID=' . $sInfo->specials_id), null, null, 'btn-light')];
      break;
    default:
      if (is_object($sInfo ?? null)) {
        $heading[] = ['text' => $sInfo->products_name];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('specials.php', 'page=' . (int)$_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('specials.php', 'page=' . (int)$_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=delete'), null, null, 'btn-danger mr-2')];
        $contents[] = ['text' => TEXT_INFO_DATE_ADDED . ' ' . tep_date_short($sInfo->specials_date_added)];
        $contents[] = ['text' => TEXT_INFO_LAST_MODIFIED . ' ' . tep_date_short($sInfo->specials_last_modified)];
        $contents[] = ['class' => 'text-center', 'text' => tep_info_image($sInfo->products_image, $sInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT)];
        $contents[] = ['text' => TEXT_INFO_ORIGINAL_PRICE . ' ' . $currencies->format($sInfo->products_price)];
        $contents[] = ['text' => TEXT_INFO_NEW_PRICE . ' ' . $currencies->format($sInfo->specials_new_products_price)];
        $contents[] = ['text' => TEXT_INFO_PERCENTAGE . ' ' . number_format(100 - (($sInfo->specials_new_products_price / $sInfo->products_price) * 100)) . '%'];

        $contents[] = ['text' => TEXT_INFO_EXPIRES_DATE . ' <strong>' . tep_date_short($sInfo->expires_date) . '</strong>'];
        $contents[] = ['text' => TEXT_INFO_STATUS_CHANGE . ' ' . tep_date_short($sInfo->date_status_change)];
      }
      break;
  }
  if ( ([] !== $heading) && ([] !== $contents) ) {
    echo '<div class="col-12 col-sm-4">';
      $box = new box();
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }

  echo '</div>';
}
?>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
