<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

// needs to be included earlier to set the success message in the messageStack
  require('includes/languages/' . $language . '/account_edit.php');

  if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $sessiontoken)) {
    if (ACCOUNT_GENDER == 'true') $gender = tep_db_prepare_input($_POST['gender']);
    $firstname = tep_db_prepare_input($_POST['firstname']);
    $lastname = tep_db_prepare_input($_POST['lastname']);
    if (ACCOUNT_DOB == 'true') $dob = tep_db_prepare_input($_POST['dob']);
    $email_address = tep_db_prepare_input($_POST['email_address']);
    $telephone = tep_db_prepare_input($_POST['telephone']);
    $fax = tep_db_prepare_input($_POST['fax']);

    $error = false;

    if (ACCOUNT_GENDER == 'true') {
      if ( ($gender != 'm') && ($gender != 'f') ) {
        $error = true;

        $messageStack->add('account_edit', ENTRY_GENDER_ERROR);
      }
    }

    if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_edit', ENTRY_FIRST_NAME_ERROR);
    }

    if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_edit', ENTRY_LAST_NAME_ERROR);
    }

    if (ACCOUNT_DOB == 'true') {
      if ((strlen($dob) < ENTRY_DOB_MIN_LENGTH) || (!empty($dob) && (!is_numeric(tep_date_raw($dob)) || !@checkdate(substr(tep_date_raw($dob), 4, 2), substr(tep_date_raw($dob), 6, 2), substr(tep_date_raw($dob), 0, 4))))) {
        $error = true;

        $messageStack->add('account_edit', ENTRY_DATE_OF_BIRTH_ERROR);
      }
    }

    if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_ERROR);
    }

    if (!tep_validate_email($email_address)) {
      $error = true;

      $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
    }

    $check_email_query = tep_db_query("select count(*) as total from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($email_address) . "' and customers_id != '" . (int)$customer_id . "'");
    $check_email = tep_db_fetch_array($check_email_query);
    if ($check_email['total'] > 0) {
      $error = true;

      $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);
    }

    if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_edit', ENTRY_TELEPHONE_NUMBER_ERROR);
    }

    if ($error == false) {
      $sql_data_array = array('customers_firstname' => $firstname,
                              'customers_lastname' => $lastname,
                              'customers_email_address' => $email_address,
                              'customers_telephone' => $telephone,
                              'customers_fax' => $fax);

      if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
      if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

      tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" . (int)$customer_id . "'");

      tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . (int)$customer_id . "'");

      $sql_data_array = array('entry_firstname' => $firstname,
                              'entry_lastname' => $lastname);

      tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "customers_id = '" . (int)$customer_id . "' and address_book_id = '" . (int)$customer_default_address_id . "'");

// reset the session variables
      $customer_first_name = $firstname;

      $messageStack->add_session('account', SUCCESS_ACCOUNT_UPDATED, 'success');

      tep_redirect(tep_href_link('account.php', '', 'SSL'));
    }
  }

  $account_query = tep_db_query("select customers_gender, customers_firstname, customers_lastname, customers_dob, customers_email_address, customers_telephone, customers_fax from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customer_id . "'");
  $account = tep_db_fetch_array($account_query);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link('account_edit.php', '', 'SSL'));

  require('includes/template_top.php');
?>


<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>


<?php
  if ($messageStack->size('account_edit') > 0) {
    echo $messageStack->output('account_edit');
  }
?>

<?php echo tep_draw_form('account_edit', tep_href_link('account_edit.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process'); ?>

<div class="contentContainer">
  <div class="text-danger text-right"><?php echo FORM_REQUIRED_INFORMATION; ?></div>

  <?php
  if (ACCOUNT_GENDER == 'true') {
    if (isset($gender)) {
      $male = ($gender == 'm') ? true : false;
    } else {
      $male = ($account['customers_gender'] == 'm') ? true : false;
    }
    $female = !$male;
  ?>
  <div class="form-group row">  
    <label class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_GENDER; ?></label>
    <div class="col-sm-9">
      <div class="form-check form-check-inline">
        <?php echo tep_draw_radio_field('gender', 'm', $male, 'required aria-required="true" id="genderM" aria-describedby="atGender"'); ?>
        &nbsp;<label class="form-check-label" for="genderM"><?php echo MALE; ?></label>
      </div>
      <div class="form-check form-check-inline">
        <?php echo tep_draw_radio_field('gender', 'f', $female, 'id="genderF" aria-describedby="atGender"'); ?>
        &nbsp;<label class="form-check-label" for="genderF"><?php echo FEMALE; ?></label>
      </div>    
      <?php if (tep_not_null(ENTRY_GENDER_TEXT)) echo '<span id="atGender" class="form-text">' . ENTRY_GENDER_TEXT . '</span>'; ?>
      <div class="float-right">
        <?php echo FORM_REQUIRED_INPUT; ?>
      </div>
    </div> 
  </div>
  <?php
  }
  ?>
  <div class="form-group row">
    <label for="inputFirstName" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_FIRST_NAME; ?></label>
    <div class="col-sm-9">
      <?php echo tep_draw_input_field('firstname', $account['customers_firstname'], 'required aria-required="true" id="inputFirstName" placeholder="' . ENTRY_FIRST_NAME_TEXT . '"'); ?>
      <?php echo FORM_REQUIRED_INPUT; ?>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputLastName" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_LAST_NAME; ?></label>
    <div class="col-sm-9">
      <?php echo tep_draw_input_field('lastname', $account['customers_lastname'], 'required aria-required="true" id="inputLastName" placeholder="' . ENTRY_LAST_NAME_TEXT . '"'); ?>
      <?php echo FORM_REQUIRED_INPUT; ?>
    </div>
  </div>

  <?php
  if (ACCOUNT_DOB == 'true') {
?>
  <div class="form-group row">
    <label for="inputName" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_DATE_OF_BIRTH; ?></label>
    <div class="col-sm-9">
      <?php echo tep_draw_input_field('dob', tep_date_short($account['customers_dob']), 'required aria-required="true" id="dob" placeholder="' . ENTRY_DATE_OF_BIRTH_TEXT . '"'); ?>
      <?php echo FORM_REQUIRED_INPUT; ?>
    </div>
  </div>
<?php
  }
?>

  <div class="form-group row">
    <label for="inputEmail" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_EMAIL_ADDRESS; ?></label>
    <div class="col-sm-9">
      <?php echo tep_draw_input_field('email_address', $account['customers_email_address'], 'required aria-required="true" id="inputEmail" placeholder="' . ENTRY_EMAIL_ADDRESS_TEXT . '"', 'email'); ?>
      <?php echo FORM_REQUIRED_INPUT; ?>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputTelephone" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_TELEPHONE_NUMBER; ?></label>
    <div class="col-sm-9">
      <?php echo tep_draw_input_field('telephone', $account['customers_telephone'], 'required aria-required="true" id="inputTelephone" placeholder="' . ENTRY_TELEPHONE_NUMBER_TEXT . '"', 'tel'); ?>
      <?php echo FORM_REQUIRED_INPUT; ?>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputFax" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_FAX_NUMBER; ?></label>
    <div class="col-sm-9">
      <?php echo tep_draw_input_field('fax', $account['customers_fax'], 'id="inputFax" placeholder="' . ENTRY_FAX_NUMBER_TEXT . '"'); ?>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-angle-right', null, 'primary', null, 'btn-success btn-lg btn-block'); ?></div>
    <p><?php echo tep_draw_button(IMAGE_BUTTON_BACK, 'fa fa-angle-left', tep_href_link('account.php', '', 'SSL')); ?></p>
  </div>
  
</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
