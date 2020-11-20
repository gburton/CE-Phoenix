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

  $OSCOM_Hooks->call('info_pages', 'preAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'setflag':
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if (isset($_GET['pID'])) {
            tep_db_query("update pages set pages_status = '" . (int)$_GET['flag'] . "' where pages_id = '" . (int)$_GET['pID'] . "'");
          }
        }

        $OSCOM_Hooks->call('info_pages', 'setFlagAction');

        tep_redirect(tep_href_link('info_pages.php', 'page=' . (int)$_GET['page'] . '&pID=' . (int)$_GET['pID']));
        break;
      case 'update':
        $pages_id = tep_db_prepare_input($_GET['pID']);

        $page_status = (int)$_POST['page_status'];
        $slug        = tep_db_prepare_input($_POST['slug']);
        $sort_order  = (int)$_POST['sort_order'];

        $sql_data_array = ['pages_status'  => $page_status,
                           'sort_order'    => $sort_order,
                           'slug'          => $slug,
                           'last_modified' => 'now()'];

        tep_db_perform('pages', $sql_data_array, 'update', "pages_id = '" . (int)$pages_id . "'");

        $languages = tep_get_languages();
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          $navbar_title_array = $_POST['navbar_title'];
          $page_title_array   = $_POST['page_title'];
          $page_text_array    = $_POST['page_text'];
          $language_id        = $languages[$i]['id'];

          $sql_data_array = ['navbar_title' => tep_db_prepare_input($navbar_title_array[$language_id]),
                             'pages_title'  => tep_db_prepare_input($page_title_array[$language_id]),
                             'pages_text'   => tep_db_prepare_input($page_text_array[$language_id])];

          $insert_sql_data = ['pages_id'     => $pages_id,
                              'languages_id' => $language_id];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          tep_db_perform('pages_description', $sql_data_array, 'update', "pages_id = '" . (int)$pages_id . "' and languages_id = '" . (int)$language_id . "'");
        }

        $OSCOM_Hooks->call('info_pages', 'updateAction');

        tep_redirect(tep_href_link('info_pages.php', 'page=' . (int)$_GET['page'] . '&pID=' . (int)$pages_id));
        break;
      case 'deleteconfirm':
        $pages_id = tep_db_prepare_input($_GET['pID']);

        tep_db_query("delete from pages where pages_id = '" . (int)$pages_id . "'");
        tep_db_query("delete from pages_description where pages_id = '" . (int)$pages_id . "'");

        $OSCOM_Hooks->call('info_pages', 'deleteConfirmAction');

        tep_redirect(tep_href_link('info_pages.php', 'page=' . (int)$_GET['page']));
        break;
      case 'addnew':
        $page_status = (int)$_POST['page_status'];
        $slug        = tep_db_prepare_input($_POST['slug']);
        $sort_order  = (int)$_POST['sort_order'];

        $sql_data_array = ['pages_status' => $page_status,
                           'slug'         => $slug,
                           'sort_order'   => $sort_order,
                           'date_added'   => 'now()'];

        tep_db_perform('pages', $sql_data_array);
        $pages_id = tep_db_insert_id();

        $languages = tep_get_languages();
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          $navbar_title_array = $_POST['navbar_title'];
          $page_title_array   = $_POST['page_title'];
          $page_text_array    = $_POST['page_text'];
          $language_id        = $languages[$i]['id'];

          $sql_data_array = ['navbar_title' => tep_db_prepare_input($navbar_title_array[$language_id]),
                             'pages_title'  => tep_db_prepare_input($page_title_array[$language_id]),
                             'pages_text'   => tep_db_prepare_input($page_text_array[$language_id])];

          $insert_sql_data = ['pages_id'     => $pages_id,
                              'languages_id' => $language_id];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          tep_db_perform('pages_description', $sql_data_array);
        }

        $OSCOM_Hooks->call('info_pages', 'addNewAction');

        tep_redirect(tep_href_link('info_pages.php', tep_get_all_get_params(['action'])));
        break;
    }
  }

  $OSCOM_Hooks->call('info_pages', 'postAction');

  require('includes/template_top.php');
  ?>

  <div class="row">
    <div class="col"><h1 class="display-4 mb-2"><?= HEADING_TITLE; ?></h1></div>
    <div class="col text-right align-self-center">
      <?php
      if (empty($action)) {
        echo tep_draw_bootstrap_button(IMAGE_BUTTON_ADD_PAGE, 'fas fa-pen', tep_href_link('info_pages.php', 'action=new'), null, null, 'btn-danger');
      }
      else {
        echo tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('info_pages.php'), null, null, 'btn-light mt-2');
      }
      ?>
    </div>
  </div>

  <?php
  if ($action == 'edit') {
    $page = info_pages::get_page(['pd.pages_id' => (int)$_GET['pID']]);

    $pInfo = new objectInfo($page);

    if (!isset($pInfo->pages_status)) $pInfo->pages_status = '1';
    switch ($pInfo->pages_status) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
    }

    echo tep_draw_form('pages', 'info_pages.php', 'page=' . (int)$_GET['page'] . '&pID=' . $_GET['pID'] . '&action=update', 'post', 'enctype="multipart/form-data"'); ?>

      <div class="form-group row align-items-center" id="zStatus">
        <label class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PAGE_STATUS; ?></label>
        <div class="col-sm-9">
          <div class="custom-control custom-radio custom-control-inline">
            <?= tep_draw_selection_field('page_status', 'radio', '1', $in_status, 'id="inStatus" class="custom-control-input"'); ?>
            <label class="custom-control-label" for="inStatus"><?= TEXT_PAGE_PUBLISHED; ?></label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <?= tep_draw_selection_field('page_status', 'radio', '0', $out_status, 'id="outStatus" class="custom-control-input"'); ?>
            <label class="custom-control-label" for="outStatus"><?= TEXT_PAGE_NOT_PUBLISHED; ?></label>
          </div>
        </div>
      </div>

      <?php
      $page_title = $page_text = $navbar_title = null;

      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $navbar_title .= '<div class="input-group mb-1">';
          $navbar_title .= '<div class="input-group-prepend">';
            $navbar_title .= '<span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
          $navbar_title .= '</div>';
          $navbar_title .= tep_draw_input_field('navbar_title[' . $languages[$i]['id'] . ']', info_pages::getElement(['pd.pages_id' => $pInfo->pages_id, 'pd.languages_id' => $languages[$i]['id']], 'navbar_title'), 'required aria-required="true"');
        $navbar_title .= '</div>';

        $page_title .= '<div class="input-group mb-1">';
          $page_title .= '<div class="input-group-prepend">';
            $page_title .= '<span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
          $page_title .= '</div>';
          $page_title .= tep_draw_input_field('page_title[' . $languages[$i]['id'] . ']', info_pages::getElement(['pd.pages_id' => $pInfo->pages_id, 'pd.languages_id' => $languages[$i]['id']], 'pages_title'), 'required aria-required="true"');
        $page_title .= '</div>';

        $page_text .= '<div class="input-group mb-1">';
          $page_text .= '<div class="input-group-prepend">';
            $page_text .= '<span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
          $page_text .= '</div>';
          $page_text .= tep_draw_textarea_field('page_text[' . $languages[$i]['id'] . ']', 'soft', '80', '10', info_pages::getElement(['pd.pages_id' => $pInfo->pages_id, 'pd.languages_id' => $languages[$i]['id']], 'pages_text'), 'required aria-required="true" class="form-control editor"');
        $page_text .= '</div>';
      }
      ?>

      <div class="form-group row" id="zNavbarTitle">
        <label class="col-form-label col-sm-3 text-left text-sm-right"><?= NAVBAR_TITLE; ?></label>
        <div class="col-sm-9">
          <?= $navbar_title; ?>
        </div>
      </div>

      <hr>

      <div class="form-group row" id="zPageTitle">
        <label class="col-form-label col-sm-3 text-left text-sm-right"><?= PAGE_TITLE; ?></label>
        <div class="col-sm-9">
          <?= $page_title; ?>
        </div>
      </div>

      <div class="form-group row" id="zPageText">
        <label class="col-form-label col-sm-3 text-left text-sm-right"><?= PAGE_TEXT; ?></label>
        <div class="col-sm-9">
          <?= $page_text; ?>
        </div>
      </div>

      <hr>

      <div class="form-group row" id="zInputSlug">
        <label for="inputSlug" class="col-form-label col-sm-3 text-left text-sm-right"><?= PAGE_SLUG; ?></label>
        <div class="col-sm-9">
          <?= tep_draw_input_field('slug', $pInfo->slug, 'required aria-required="true" id="inputSlug" class="form-control w-50"'); ?>
        </div>
      </div>

      <div class="form-group row" id="zInputSort">
        <label for="inputSort" class="col-form-label col-sm-3 text-left text-sm-right"><?= SORT_ORDER; ?></label>
        <div class="col-sm-9">
          <?= tep_draw_input_field('sort_order', $pInfo->sort_order, 'required aria-required="true" id="inputSort" class="form-control w-50"'); ?>
        </div>
      </div>

      <?php
      echo $OSCOM_Hooks->call('info_pages', 'formEdit');

      echo tep_draw_hidden_field('pages_id', $pInfo->pages_id);
      echo tep_draw_hidden_field('date_added', $pInfo->date_added);

      echo tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success btn-block btn-lg');
      ?>

    </form>
    <?php
  } elseif ($action == 'new') {
    echo tep_draw_form('pages', 'info_pages.php', 'action=addnew', 'post', 'enctype="multipart/form-data"');
      ?>

      <div class="form-group row align-items-center" id="zStatus">
        <label class="col-form-label col-sm-3 text-left text-sm-right"><?= TEXT_PAGE_STATUS; ?></label>
        <div class="col-sm-9">
          <div class="custom-control custom-radio custom-control-inline">
            <?= tep_draw_selection_field('page_status', 'radio', '1', 0, 'id="inStatus" class="custom-control-input"'); ?>
            <label class="custom-control-label" for="inStatus"><?= TEXT_PAGE_PUBLISHED; ?></label>
          </div>
          <div class="custom-control custom-radio custom-control-inline">
            <?= tep_draw_selection_field('page_status', 'radio', '0', 1, 'id="outStatus" class="custom-control-input"'); ?>
            <label class="custom-control-label" for="outStatus"><?= TEXT_PAGE_NOT_PUBLISHED; ?></label>
          </div>
        </div>
      </div>

      <?php
      $page_title = $page_text = $navbar_title = null;

      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $navbar_title .= '<div class="input-group mb-1">';
          $navbar_title .= '<div class="input-group-prepend">';
            $navbar_title .= '<span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
          $navbar_title .= '</div>';
          $navbar_title .= tep_draw_input_field('navbar_title[' . $languages[$i]['id'] . ']', null, 'required aria-required="true"');
        $navbar_title .= '</div>';

        $page_title .= '<div class="input-group mb-1">';
          $page_title .= '<div class="input-group-prepend">';
            $page_title .= '<span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
          $page_title .= '</div>';
          $page_title .= tep_draw_input_field('page_title[' . $languages[$i]['id'] . ']', null, 'required aria-required="true"');
        $page_title .= '</div>';

        $page_text .= '<div class="input-group mb-1">';
          $page_text .= '<div class="input-group-prepend">';
            $page_text .= '<span class="input-group-text">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
          $page_text .= '</div>';
          $page_text .= tep_draw_textarea_field('page_text[' . $languages[$i]['id'] . ']', 'soft', '80', '10', null, 'required aria-required="true" class="form-control editor"');
        $page_text .= '</div>';
      }
      ?>

      <div class="form-group row" id="zNavbarTitle">
        <label class="col-form-label col-sm-3 text-left text-sm-right"><?= NAVBAR_TITLE; ?></label>
        <div class="col-sm-9">
          <?= $navbar_title; ?>
        </div>
      </div>

      <hr>

      <div class="form-group row" id="zPageTitle">
        <label class="col-form-label col-sm-3 text-left text-sm-right"><?= PAGE_TITLE; ?></label>
        <div class="col-sm-9">
          <?= $page_title; ?>
        </div>
      </div>

      <div class="form-group row" id="zPageText">
        <label class="col-form-label col-sm-3 text-left text-sm-right"><?= PAGE_TEXT; ?></label>
        <div class="col-sm-9">
          <?= $page_text; ?>
        </div>
      </div>

      <hr>

      <div class="form-group row" id="zInputSlug">
        <label for="inputSlug" class="col-form-label col-sm-3 text-left text-sm-right"><?= PAGE_SLUG; ?></label>
        <div class="col-sm-9">
          <?= tep_draw_input_field('slug', null, 'required aria-required="true" id="inputSlug" class="form-control w-50"'); ?>
        </div>
      </div>

      <div class="form-group row" id="zSortOrder">
        <label for="inputSort" class="col-form-label col-sm-3 text-left text-sm-right"><?= SORT_ORDER; ?></label>
        <div class="col-sm-9">
          <?= tep_draw_input_field('sort_order', null, 'required aria-required="true" id="inputSort" class="form-control w-50"'); ?>
        </div>
      </div>

      <?php
      echo $OSCOM_Hooks->call('info_pages', 'formNew');

      echo tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-pen', null, 'primary', null, 'btn-success btn-block btn-lg');
      ?>

    </form>
    <?php
  } else {
    $required_slugs = info_pages::requirements();

    if ( sizeof($required_slugs) > 0 )   {
      $missing_slugs = implode(', ', $required_slugs);
      echo '<div class="alert alert-danger">' . sprintf(MISSING_SLUGS_ERROR, $missing_slugs)  . '</div>';
    }
    ?>

    <div class="row no-gutters">
      <div class="col-12 col-sm-8">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="thead-dark">
              <tr>
                <th><?= TABLE_HEADING_PAGE_ID; ?></th>
                <th><?= TABLE_HEADING_PAGE_TITLE; ?></th>
                <th><?= TABLE_HEADING_SLUG; ?></th>
                <th><?= TABLE_HEADING_DATE_ADDED; ?></th>
                <th class="text-center"><?= TABLE_HEADING_SORT_ORDER; ?></th>
                <th class="text-center"><?= TABLE_HEADING_STATUS; ?></th>
                <th class="text-right"><?= TABLE_HEADING_ACTION; ?></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $pages = info_pages::get_pages($OSCOM_Hooks->call('info_pages', 'order_by'));
              $pages_split = info_pages::split_page_results();

              foreach ($pages as $k => $v) {
                if ((!isset($_GET['pID']) || (isset($_GET['pID']) && ($_GET['pID'] == $v['pages_id']))) && !isset($pInfo)) {
                  $pInfo = new objectInfo($v);
                }

                if (isset($pInfo) && is_object($pInfo) && ($v['pages_id'] == $pInfo->pages_id) ) {
                  echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('info_pages.php', 'page=' . (int)$_GET['page'] . '&pID=' . (int)$pInfo->pages_id . '&action=edit') . '\'">';
                } else {
                  echo '<tr onclick="document.location.href=\'' . tep_href_link('info_pages.php', 'page=' . (int)$_GET['page'] . '&pID=' . (int)$v['pages_id']) . '\'">';
                }
                ?>
                  <td><?= $v['pages_id']; ?></td>
                  <td><?= $v['pages_title']; ?></td>
                  <td><?= $v['slug']; ?></td>
                  <td><?= $v['date_added']; ?></td>
                  <td class="text-center"><?= $v['sort_order']; ?>&nbsp;</td>
                  <td class="text-center"><?php
                  if ($v['pages_status'] == '1') {
                    echo '<i class="fas fa-check-circle text-success"></i> <a href="' . tep_href_link('info_pages.php', 'action=setflag&flag=0&pID=' . $v['pages_id'] . '&page=' . (int)$_GET['page']) . '"><i class="fas fa-times-circle text-muted"></i></a>';
                  } else {
                    echo '<a href="' . tep_href_link('info_pages.php', 'action=setflag&flag=1&pID=' . $v['pages_id'] . '&page=' . (int)$_GET['page']) . '"><i class="fas fa-check-circle text-muted"></i></a>  <i class="fas fa-times-circle text-danger"></i>';
                  }
                  ?></td>
                  <td class="text-right"><?php if ( (isset($pInfo->pages_id)) && ($v['pages_id'] == $pInfo->pages_id) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('info_pages.php', 'page=' . (int)$_GET['page'] . '&pID=' . $v['pages_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
                </tr>
                <?php
              }
              ?>
            </tbody>
          </table>
        </div>

        <div class="row my-1">
          <div class="col"><?= $pages_split->display_count($pages_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PAGES); ?></div>
          <div class="col text-right mr-2"><?= $pages_split->display_links($pages_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
        </div>

      </div>

    <?php
    $heading = [];
    $contents = [];

    switch ($action) {
      case 'delete':
        $heading[] = ['text' => sprintf(TEXT_HEADING_DELETE_PAGE, $pInfo->pages_title)];

        $contents = ['form' => tep_draw_form('pages', 'info_pages.php', 'page=' . (int)$_GET['page'] . '&pID=' . $pInfo->pages_id . '&action=deleteconfirm')];
        $contents[] = ['text' => TEXT_DELETE_PAGE_INTRO];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('info_pages.php', 'page=' . (int)$_GET['page'] . '&pID=' . $pInfo->pages_id), null, null, 'btn-light')];
        break;
      default:
      if (isset($pInfo) && is_object($pInfo)) {
        $heading[] = ['text' => $pInfo->pages_title];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('info_pages.php', 'page=' . (int)$_GET['page'] . '&pID=' . $pInfo->pages_id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('info_pages.php', 'page=' . (int)$_GET['page'] . '&pID=' . $pInfo->pages_id . '&action=delete'), null, null, 'btn-danger')];
        $contents[] = ['text' => sprintf(TEXT_INFO_DATE_ADDED, tep_date_short($pInfo->date_added))];
        if (tep_not_null($pInfo->last_modified)) $contents[] = ['text' => sprintf(TEXT_INFO_LAST_MODIFIED, tep_date_short($pInfo->last_modified))];
        $contents[] = ['text' => sprintf(TEXT_INFO_PAGE_SIZE, str_word_count($pInfo->pages_text))];
      }
        break;
    }

    if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
      echo '<div class="col-12 col-sm-4">';
        $box = new box;
        echo $box->infoBox($heading, $contents);
      echo '</div>';
    }

    echo '</div>';

  }

  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>