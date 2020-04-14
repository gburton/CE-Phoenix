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
  
  $OSCOM_Hooks->call('testimonials', 'testimonialsPreAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'setflag':
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if (isset($_GET['tID'])) {
            tep_db_query("update testimonials set testimonials_status = '" . (int)$_GET['flag'] . "' where testimonials_id = '" . (int)$_GET['tID'] . "'");
          }
        }
        
        $OSCOM_Hooks->call('testimonials', 'testimonialsActionSetFlag');

        tep_redirect(tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $_GET['tID']));
        break;
      case 'update':
        $customers_id = (int)$_POST['customers_id'];
        $customers_name = tep_db_prepare_input($_POST['customer_name']);
        $testimonials_id = tep_db_prepare_input($_GET['tID']);
        $testimonials_text = tep_db_prepare_input($_POST['testimonials_text']);
        $testimonials_status = tep_db_prepare_input($_POST['testimonials_status']);

        tep_db_query("update testimonials set customers_id = '" . (int)$customers_id . "', customers_name  = '" . tep_db_input($customers_name) . "', testimonials_status = '" . tep_db_input($testimonials_status) . "', last_modified = now() where testimonials_id = '" . (int)$testimonials_id . "'");
        tep_db_query("update testimonials_description set testimonials_text = '" . tep_db_input($testimonials_text) . "' where testimonials_id = '" . (int)$testimonials_id . "'");

        $OSCOM_Hooks->call('testimonials', 'testimonialsActionUpdate');
        
        tep_redirect(tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $testimonials_id));
        break;
      case 'deleteconfirm':
        $testimonials_id = tep_db_prepare_input($_GET['tID']);

        tep_db_query("delete from testimonials where testimonials_id = '" . (int)$testimonials_id . "'");
        tep_db_query("delete from testimonials_description where testimonials_id = '" . (int)$testimonials_id . "'");
        
        $OSCOM_Hooks->call('testimonials', 'testimonialsActionDelete');

        tep_redirect(tep_href_link('testimonials.php', 'page=' . $_GET['page']));
        break;
        
      case 'addnew':
        $customers_id = (int)$_POST['customers_id'];
        $customers_name = tep_db_prepare_input($_POST['customer_name']);
        $testimonial = tep_db_prepare_input($_POST['testimonials_text']);

        tep_db_query("insert into testimonials (customers_id, customers_name, date_added, testimonials_status) values ('" . $customers_id . "', '" . tep_db_input($customers_name) . "', now(), 1)");
        $insert_id = tep_db_insert_id();
        tep_db_query("insert into testimonials_description (testimonials_id, languages_id, testimonials_text) values ('" . (int)$insert_id . "', '" . (int)$languages_id . "', '" . tep_db_input($testimonial) . "')");
        
        $OSCOM_Hooks->call('testimonials', 'testimonialsActionSave');

        tep_redirect(tep_href_link('testimonials.php', tep_get_all_get_params(array('action'))));
        break;
    }
  }
  
  $OSCOM_Hooks->call('testimonials', 'testimonialsPostAction');

  require('includes/template_top.php');
?>

  <div class="row">
    <div class="col"><h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1></div>
    <div class="col text-right align-self-center">
      <?php
      if (empty($action)) {
        echo tep_draw_bootstrap_button(IMAGE_BUTTON_ADD_TESTIMONIAL, 'fas fa-pen', tep_href_link('testimonials.php', 'action=new'), null, null, 'btn-danger xxx text-white');
      }
      else {
        echo tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('testimonials.php'), null, null, 'btn-light mt-2');
      }
      ?>
    </div>
  </div>

<?php
  if ($action == 'edit') {
    $tID = tep_db_prepare_input($_GET['tID']);

    $testimonials_query = tep_db_query("select t.*, td.* from testimonials t, testimonials_description td where t.testimonials_id = '" . (int)$tID . "' and t.testimonials_id = td.testimonials_id");
    $testimonials = tep_db_fetch_array($testimonials_query);

    $tInfo = new objectInfo($testimonials);

    if (!isset($tInfo->testimonials_status)) $tInfo->testimonials_status = '1';
    switch ($tInfo->testimonials_status) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
    }
?>
      <?php echo tep_draw_form('testimonial', 'testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $_GET['tID'] . '&action=update', 'post', 'enctype="multipart/form-data"'); ?>
        
        <div class="form-group row align-items-center">
          <label class="col-form-label col-sm-3 text-left text-sm-right"><?php echo TEXT_INFO_TESTIMONIAL_STATUS; ?></label>
          <div class="col-sm-9">
            <div class="custom-control custom-radio custom-control-inline">
              <?php echo tep_draw_selection_field('testimonials_status', 'radio', '1', $in_status, 'id="inStatus" class="custom-control-input"'); ?>
              <label class="custom-control-label" for="inStatus"><?php echo TEXT_TESTIMONIAL_PUBLISHED; ?></label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <?php echo tep_draw_selection_field('testimonials_status', 'radio', '0', $out_status, 'id="outStatus" class="custom-control-input"'); ?>
              <label class="custom-control-label" for="outStatus"><?php echo TEXT_TESTIMONIAL_NOT_PUBLISHED; ?></label>
            </div>    
          </div>
        </div>
          
        <div class="form-group row">
          <label for="inputFrom" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_FROM; ?></label>
          <div class="col-sm-9">
            <?php echo tep_draw_customers('customers_id', 'id="inputFrom"', $tInfo->customers_id); ?>
          </div>
        </div>
        
        <div class="form-group row">
          <label for="inputNick" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_FROM_NICKNAME; ?></label>
          <div class="col-sm-9">
            <?php echo tep_draw_input_field('customer_name', $tInfo->customers_name, 'required aria-required="true" id="inputNick"'); 
            ?>
          </div>
        </div>
        
        <div class="form-group row">
          <label for="inputText" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_TESTIMONIAL; ?></label>
          <div class="col-sm-9">
            <?php echo tep_draw_textarea_field('testimonials_text', 'soft', '60', '15', $tInfo->testimonials_text, 'required aria-required="true" id="inputText"'); ?>
          </div>
        </div>
        
        <?php
        echo $OSCOM_Hooks->call('testimonials', 'testimonialsFormEdit');

        echo tep_draw_hidden_field('testimonials_id', $tInfo->testimonials_id);
        echo tep_draw_hidden_field('customers_name', $tInfo->customers_name);
        echo tep_draw_hidden_field('date_added', $tInfo->date_added);
        
        echo tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success btn-block btn-lg');
        ?>

      </form>
<?php
  } elseif ($action == 'new') {
    
      echo tep_draw_form('review', 'testimonials.php', 'action=addnew', 'post', 'enctype="multipart/form-data"'); 
      ?>
      
        <div class="form-group row">
          <label for="inputFrom" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_FROM; ?></label>
          <div class="col-sm-9">
            <?php echo tep_draw_customers('customers_id', 'id="inputFrom"'); ?>
          </div>
        </div>
        
        <div class="form-group row">
          <label for="inputNick" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_FROM_NICKNAME; ?></label>
          <div class="col-sm-9">
            <?php echo tep_draw_input_field('customer_name', '', 'required aria-required="true" id="inputNick"'); 
            ?>
          </div>
        </div>
        
        <div class="form-group row">
          <label for="inputText" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_TESTIMONIAL; ?></label>
          <div class="col-sm-9">
            <?php echo tep_draw_textarea_field('testimonials_text', 'soft', '60', '15', '', 'required aria-required="true" id="inputText"'); ?>
          </div>
        </div>
        
        <?php
        echo $OSCOM_Hooks->call('testimonials', 'testimonialsFormNew');
        
        echo tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-pen', null, 'primary', null, 'btn-success btn-block btn-lg'); 
        ?>

      </form>
       <?php
     } else {
?>
      
  <div class="row no-gutters">
    <div class="col">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?php echo TABLE_HEADING_CUSTOMER_ID; ?></th>
              <th><?php echo TABLE_HEADING_CUSTOMER_NAME; ?></th>
              <th><?php echo TABLE_HEADING_DATE_ADDED; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_STATUS; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $testimonials_query_raw = "select * from testimonials order by testimonials_id DESC";
            $testimonials_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $testimonials_query_raw, $testimonials_query_numrows);
            $testimonials_query = tep_db_query($testimonials_query_raw);
            while ($testimonials = tep_db_fetch_array($testimonials_query)) {
              if ((!isset($_GET['tID']) || (isset($_GET['tID']) && ($_GET['tID'] == $testimonials['testimonials_id']))) && !isset($tInfo)) {
                $testimonials_text_query = tep_db_query("select t.customers_name, td.testimonials_text from testimonials t, testimonials_description td where t.testimonials_id = '" . (int)$testimonials['testimonials_id'] . "' and t.testimonials_id = td.testimonials_id");
                $testimonials_text = tep_db_fetch_array($testimonials_text_query);

                $tInfo_array = array_merge($testimonials, $testimonials_text);
                $tInfo = new objectInfo($tInfo_array);
              }

              if (isset($tInfo) && is_object($tInfo) && ($testimonials['testimonials_id'] == $tInfo->testimonials_id) ) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . (int)$tInfo->testimonials_id . '&action=edit') . '\'">';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . (int)$testimonials['testimonials_id']) . '\'">';
              }
              ?>
                <td><?php echo (int)$testimonials['customers_id']; ?></td>
                <td><?php echo $testimonials['customers_name']; ?></td>
                <td><?php echo tep_date_short($testimonials['date_added']); ?></td>
                <td class="text-center"><?php
                if ($testimonials['testimonials_status'] == '1') {
                  echo '<i class="fas fa-check-circle text-success"></i> <a href="' . tep_href_link('testimonials.php', 'action=setflag&flag=0&tID=' . $testimonials['testimonials_id'] . '&page=' . $_GET['page']) . '"><i class="fas fa-times-circle text-muted"></i></a>';
                } else {
                  echo '<a href="' . tep_href_link('testimonials.php', 'action=setflag&flag=1&tID=' . $testimonials['testimonials_id'] . '&page=' . $_GET['page']) . '"><i class="fas fa-check-circle text-muted"></i></a>  <i class="fas fa-times-circle text-danger"></i>';
                }
                ?></td>
                <td class="text-right"><?php if ( (is_object($tInfo)) && ($testimonials['testimonials_id'] == $tInfo->testimonials_id) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $testimonials['testimonials_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
<?php
    }
?>
          </tbody>
        </table>
      </div>
      
      <div class="row my-1">
        <div class="col"><?php echo $testimonials_split->display_count($testimonials_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TESTIMONIALS); ?></div>
        <div class="col text-right mr-2"><?php echo $testimonials_split->display_links($testimonials_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>
      
    </div>

<?php
    $heading = [];
    $contents = [];

    switch ($action) {
      case 'delete':
        $heading[] = ['text' => TEXT_INFO_HEADING_DELETE_TESTIMONIAL];

        $contents = ['form' => tep_draw_form('testimonials', 'testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $tInfo->testimonials_id . '&action=deleteconfirm')];
        $contents[] = ['text' => TEXT_INFO_DELETE_TESTIMONIAL_INTRO];
        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $tInfo->testimonials_id), null, null, 'btn-light')];
        break;
      default:
      if (isset($tInfo) && is_object($tInfo)) {
        $heading[] = ['text' => $tInfo->customers_name];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $tInfo->testimonials_id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $tInfo->testimonials_id . '&action=delete'), null, null, 'btn-danger xxx text-white')];
        $contents[] = ['text' => sprintf(TEXT_INFO_DATE_ADDED, tep_date_short($tInfo->date_added))];
        if (tep_not_null($tInfo->last_modified)) $contents[] = ['text' => sprintf(TEXT_INFO_LAST_MODIFIED, tep_date_short($tInfo->last_modified))];
        $contents[] = ['text' => sprintf(TEXT_INFO_TESTIMONIAL_AUTHOR, $tInfo->customers_name)];
        $contents[] = ['text' => sprintf(TEXT_INFO_TESTIMONIAL_SIZE, str_word_count($tInfo->testimonials_text))];
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
