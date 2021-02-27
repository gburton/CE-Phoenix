<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('address_book.php', '', 'SSL'));
  $breadcrumb->add($navbar_title_3, $navbar_link_3);

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo $page_heading; ?></h1>

<?php
  if ($messageStack->size($message_stack_area) > 0) {
    echo $messageStack->output($message_stack_area);
  }

  if (isset($_GET['delete'])) {
?>

  <div class="row">
    <div class="col-sm-8">
      <div class="alert alert-danger" role="alert"><?php echo DELETE_ADDRESS_DESCRIPTION; ?></div>
    </div>
    <div class="col-sm-4">
      <div class="card mb-2">
        <div class="card-header"><?php echo DELETE_ADDRESS_TITLE; ?></div>

        <div class="card-body">
          <?php echo $customer->make_address_label((int)$_GET['delete'], true, ' ', '<br>'); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_DELETE, 'fas fa-trash-alt', tep_href_link('address_book_process.php', 'delete=' . $_GET['delete'] . '&action=deleteconfirm&formid=' . md5($_SESSION['sessiontoken']), 'SSL'), 'primary', NULL, 'btn-danger btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', tep_href_link('address_book.php', '', 'SSL')); ?></p>
  </div>

<?php
  } else {
    echo tep_draw_form('addressbook', tep_href_link('address_book_process.php', (isset($_GET['edit']) ? 'edit=' . $_GET['edit'] : ''), 'SSL'), 'post', '', true);
    if (is_numeric($_GET['edit'] ?? null)) {
      echo tep_draw_hidden_field('action', 'update') . tep_draw_hidden_field('edit', $_GET['edit']);
      $action_button = tep_draw_button(IMAGE_BUTTON_UPDATE, 'fas fa-sync', null, 'primary', null, 'btn-success btn-lg btn-block');
    } else {
      echo tep_draw_hidden_field('action', 'process');
      $action_button = tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block');
    }

  include $oscTemplate->map_to_template('address_book_details.php', 'component');
?>

  <div class="buttonSet">
    <div class="text-right"><?php echo $action_button; ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', $back_link); ?></p>
  </div>

</form>

<?php
  }
?>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
