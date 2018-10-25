<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// needs to be included earlier to set the success message in the messageStack
  require('includes/languages/' . $language . '/create_account.php');

  $process = false;
  if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $sessiontoken)) {
    $process = true;

    if (ACCOUNT_GENDER == 'true') {
      if (isset($_POST['gender'])) {
        $gender = tep_db_prepare_input($_POST['gender']);
      } else {
        $gender = false;
      }
    }
    $firstname = tep_db_prepare_input($_POST['firstname']);
    $lastname = tep_db_prepare_input($_POST['lastname']);
    if (ACCOUNT_DOB == 'true') $dob = tep_db_prepare_input($_POST['dob']);
    $email_address = tep_db_prepare_input($_POST['email_address']);
    if (ACCOUNT_COMPANY == 'true') $company = tep_db_prepare_input($_POST['company']);
    $street_address = tep_db_prepare_input($_POST['street_address']);
    if (ACCOUNT_SUBURB == 'true') $suburb = tep_db_prepare_input($_POST['suburb']);
    $postcode = tep_db_prepare_input($_POST['postcode']);
    $city = tep_db_prepare_input($_POST['city']);
    if (ACCOUNT_STATE == 'true') {
      $state = tep_db_prepare_input($_POST['state']);
      if (isset($_POST['zone_id'])) {
        $zone_id = tep_db_prepare_input($_POST['zone_id']);
      } else {
        $zone_id = false;
      }
    }
    $country = tep_db_prepare_input($_POST['country']);
    $telephone = tep_db_prepare_input($_POST['telephone']);
    $fax = tep_db_prepare_input($_POST['fax']);
    if (isset($_POST['newsletter'])) {
      $newsletter = tep_db_prepare_input($_POST['newsletter']);
    } else {
      $newsletter = false;
    }
    $password = tep_db_prepare_input($_POST['password']);
    $confirmation = tep_db_prepare_input($_POST['confirmation']);

    $error = false;

    if (ACCOUNT_GENDER == 'true') {
      if ( ($gender != 'm') && ($gender != 'f') ) {
        $error = true;

        $messageStack->add('create_account', ENTRY_GENDER_ERROR);
      }
    }

    if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_FIRST_NAME_ERROR);
    }

    if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_LAST_NAME_ERROR);
    }

    if (ACCOUNT_DOB == 'true') {
      if ((strlen($dob) < ENTRY_DOB_MIN_LENGTH) || (!empty($dob) && (!is_numeric(tep_date_raw($dob)) || !@checkdate(substr(tep_date_raw($dob), 4, 2), substr(tep_date_raw($dob), 6, 2), substr(tep_date_raw($dob), 0, 4))))) {
        $error = true;

        $messageStack->add('create_account', ENTRY_DATE_OF_BIRTH_ERROR);
      }
    }

    if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR);
    } elseif (tep_validate_email($email_address) == false) {
      $error = true;

      $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
    } else {
      $check_email_query = tep_db_query("select count(*) as total from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($email_address) . "'");
      $check_email = tep_db_fetch_array($check_email_query);
      if ($check_email['total'] > 0) {
        $error = true;

        $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);
      }
    }

    if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_STREET_ADDRESS_ERROR);
    }

    if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_POST_CODE_ERROR);
    }

    if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_CITY_ERROR);
    }

    if (is_numeric($country) == false) {
      $error = true;

      $messageStack->add('create_account', ENTRY_COUNTRY_ERROR);
    }

    if (ACCOUNT_STATE == 'true') {
      $zone_id = 0;
      $check_query = tep_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "'");
      $check = tep_db_fetch_array($check_query);
      $entry_state_has_zones = ($check['total'] > 0);
      if ($entry_state_has_zones == true) {
        $zone_query = tep_db_query("select distinct zone_id from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' and (zone_name = '" . tep_db_input($state) . "' or zone_code = '" . tep_db_input($state) . "')");
        if (tep_db_num_rows($zone_query) == 1) {
          $zone = tep_db_fetch_array($zone_query);
          $zone_id = $zone['zone_id'];
        } else {
          $error = true;

          $messageStack->add('create_account', ENTRY_STATE_ERROR_SELECT);
        }
      } else {
        if (strlen($state) < ENTRY_STATE_MIN_LENGTH) {
          $error = true;

          $messageStack->add('create_account', ENTRY_STATE_ERROR);
        }
      }
    }

    if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_TELEPHONE_NUMBER_ERROR);
    }


    if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = true;

      $messageStack->add('create_account', ENTRY_PASSWORD_ERROR);
    } elseif ($password != $confirmation) {
      $error = true;

      $messageStack->add('create_account', ENTRY_PASSWORD_ERROR_NOT_MATCHING);
    }

    if ($error == false) {
      $sql_data_array = array('customers_firstname' => $firstname,
                              'customers_lastname' => $lastname,
                              'customers_email_address' => $email_address,
                              'customers_telephone' => $telephone,
                              'customers_fax' => $fax,
                              'customers_newsletter' => $newsletter,
                              'customers_password' => tep_encrypt_password($password));

      if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
      if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

      tep_db_perform(TABLE_CUSTOMERS, $sql_data_array);

      $customer_id = tep_db_insert_id();

      $sql_data_array = array('customers_id' => $customer_id,
                              'entry_firstname' => $firstname,
                              'entry_lastname' => $lastname,
                              'entry_street_address' => $street_address,
                              'entry_postcode' => $postcode,
                              'entry_city' => $city,
                              'entry_country_id' => $country);

      if (ACCOUNT_GENDER == 'true') $sql_data_array['entry_gender'] = $gender;
      if (ACCOUNT_COMPANY == 'true') $sql_data_array['entry_company'] = $company;
      if (ACCOUNT_SUBURB == 'true') $sql_data_array['entry_suburb'] = $suburb;
      if (ACCOUNT_STATE == 'true') {
        if ($zone_id > 0) {
          $sql_data_array['entry_zone_id'] = $zone_id;
          $sql_data_array['entry_state'] = '';
        } else {
          $sql_data_array['entry_zone_id'] = '0';
          $sql_data_array['entry_state'] = $state;
        }
      }

      tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

      $address_id = tep_db_insert_id();

      tep_db_query("update " . TABLE_CUSTOMERS . " set customers_default_address_id = '" . (int)$address_id . "' where customers_id = '" . (int)$customer_id . "'");

      tep_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . (int)$customer_id . "', '0', now())");

      if (SESSION_RECREATE == 'True') {
        tep_session_recreate();
      }

      $customer_first_name = $firstname;
      $customer_default_address_id = $address_id;
      $customer_country_id = $country;
      $customer_zone_id = $zone_id;
      tep_session_register('customer_id');
      tep_session_register('customer_first_name');
      tep_session_register('customer_default_address_id');
      tep_session_register('customer_country_id');
      tep_session_register('customer_zone_id');

// reset session token
      $sessiontoken = md5(tep_rand() . tep_rand() . tep_rand() . tep_rand());

// restore cart contents
      $cart->restore_contents();

// build the message content
      $name = $firstname . ' ' . $lastname;

      if (ACCOUNT_GENDER == 'true') {
         if ($gender == 'm') {
           $email_text = sprintf(EMAIL_GREET_MR, $lastname);
         } else {
           $email_text = sprintf(EMAIL_GREET_MS, $lastname);
         }
      } else {
        $email_text = sprintf(EMAIL_GREET_NONE, $firstname);
      }

      $email_text .= EMAIL_WELCOME . EMAIL_TEXT . EMAIL_CONTACT . EMAIL_WARNING;
      tep_mail($name, $email_address, EMAIL_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

      tep_redirect(tep_href_link('create_account_success.php', '', 'SSL'));
    }
  }

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('create_account.php', '', 'SSL'));

  require('includes/template_top.php');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($messageStack->size('create_account') > 0) {
    echo $messageStack->output('create_account');
  }
?>

<div class="alert alert-warning">
  <div class="row">
    <div class="col-sm-9"><?php echo sprintf(TEXT_ORIGIN_LOGIN, tep_href_link('login.php', tep_get_all_get_params(), 'SSL')); ?></div>
    <div class="col-sm-3 text-left text-sm-right"><span class="text-danger"><?php echo FORM_REQUIRED_INFORMATION; ?></span></div>
  </div>  
</div>

<?php echo tep_draw_form('create_account', tep_href_link('create_account.php', '', 'SSL'), 'post', '', true) . tep_draw_hidden_field('action', 'process'); ?>

<div class="contentContainer">

  <h4><?php echo CATEGORY_PERSONAL; ?></h4>

<?php
  if (ACCOUNT_GENDER == 'true') {
?>
    
  <div class="form-group row">
    <label class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_GENDER; ?></label>
    <div class="col-sm-9">
      <div class="form-check form-check-inline">
        <?php echo tep_draw_radio_field('gender', 'm', NULL, 'required aria-required="true" id="genderM" aria-describedby="atGender"'); ?>
        &nbsp;<label class="form-check-label" for="genderM"><?php echo MALE; ?></label>
      </div>
      <div class="form-check form-check-inline">
        <?php echo tep_draw_radio_field('gender', 'f', NULL, 'id="genderF" aria-describedby="atGender"'); ?>
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
      <?php
      echo tep_draw_input_field('firstname', NULL, 'required aria-required="true" id="inputFirstName" placeholder="' . ENTRY_FIRST_NAME_TEXT . '"');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputLastName" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_LAST_NAME; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('lastname', NULL, 'required aria-required="true" id="inputLastName" placeholder="' . ENTRY_LAST_NAME_TEXT . '"');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
<?php
  if (ACCOUNT_DOB == 'true') {
?>
  <div class="form-group row">
    <label for="dob" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_DATE_OF_BIRTH; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('dob', '', 'required aria-required="true" id="dob" placeholder="' . ENTRY_DATE_OF_BIRTH_TEXT . '"');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
<?php
  }
?>
  <div class="form-group row">
    <label for="inputEmail" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_EMAIL_ADDRESS; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('email_address', NULL, 'required aria-required="true" autocomplete="username email" id="inputEmail" placeholder="' . ENTRY_EMAIL_ADDRESS_TEXT . '"', 'email');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
<?php
  if (ACCOUNT_COMPANY == 'true') {
?>

  <h4><?php echo CATEGORY_COMPANY; ?></h4>
  
  <div class="form-group row">
    <label for="inputCompany" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_COMPANY; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('company', NULL, 'id="inputCompany" placeholder="' . ENTRY_COMPANY_TEXT . '"');
      ?>
    </div>
  </div>

<?php
  }
?>

  <h4><?php echo CATEGORY_ADDRESS; ?></h4>

  <div class="form-group row">
    <label for="inputStreet" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_STREET_ADDRESS; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('street_address', NULL, 'required aria-required="true" id="inputStreet" placeholder="' . ENTRY_STREET_ADDRESS_TEXT . '"');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>

<?php
  if (ACCOUNT_SUBURB == 'true') {
?>
  <div class="form-group row">
  <label for="inputSuburb" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_SUBURB; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('suburb', NULL, 'id="inputSuburb" placeholder="' . ENTRY_SUBURB_TEXT . '"');
      ?>
    </div>
  </div>
<?php
  }
?>
  <div class="form-group row">
    <label for="inputCity" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_CITY; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('city', NULL, 'required aria-required="true" id="inputCity" placeholder="' . ENTRY_CITY_TEXT. '"');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputZip" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_POST_CODE; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('postcode', NULL, 'required aria-required="true" id="inputZip" placeholder="' . ENTRY_POST_CODE_TEXT . '"');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
<?php
  if (ACCOUNT_STATE == 'true') {
?>
  <div class="form-group row">
    <label for="inputState" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_STATE; ?></label>
    <div class="col-sm-9">
      <?php
      if ($process == true) {
        if ($entry_state_has_zones == true) {
          $zones_array = array();
          $zones_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' order by zone_name");
          while ($zones_values = tep_db_fetch_array($zones_query)) {
            $zones_array[] = array('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
          }
          echo tep_draw_pull_down_menu('state', $zones_array, 0, 'id="inputState" aria-describedby="atState"');
          echo FORM_REQUIRED_INPUT;
          if (tep_not_null(ENTRY_STATE_TEXT)) echo '<span id="atState" class="form-text">' . ENTRY_STATE_TEXT . '</span>';
        } else {
          echo tep_draw_input_field('state', NULL, 'id="inputState" placeholder="' . ENTRY_STATE_TEXT . '"');
          echo FORM_REQUIRED_INPUT;
        }
      } else {
        echo tep_draw_input_field('state', NULL, 'id="inputState" placeholder="' . ENTRY_STATE_TEXT . '"');
        echo FORM_REQUIRED_INPUT;
      }
      ?>
    </div>
  </div>
<?php
  }
?>
  <div class="form-group row">
    <label for="inputCountry" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_COUNTRY; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_get_country_list('country', NULL, 'required aria-required="true" aria-describedby="atCountry" id="inputCountry"');
      echo FORM_REQUIRED_INPUT;
      if (tep_not_null(ENTRY_COUNTRY_TEXT)) echo '<span id="atCountry" class="form-text">' . ENTRY_COUNTRY_TEXT . '</span>';
      ?>
    </div>
  </div>

  <h4><?php echo CATEGORY_CONTACT; ?></h4>
  
  <div class="form-group row">
    <label for="inputTelephone" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_TELEPHONE_NUMBER; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('telephone', NULL, 'required aria-required="true" id="inputTelephone" placeholder="' . ENTRY_TELEPHONE_NUMBER_TEXT . '"', 'tel');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputFax" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_FAX_NUMBER; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('fax', '', 'id="inputFax" placeholder="' . ENTRY_FAX_NUMBER_TEXT . '"', 'tel');
      ?>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputNewsletter" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_NEWSLETTER; ?></label>
    <div class="col-sm-9">
      <div class="form-check">
        <label>
          <?php
          echo tep_draw_checkbox_field('newsletter', '1', NULL, 'id="inputNewsletter"');
          if (tep_not_null(ENTRY_NEWSLETTER_TEXT)) echo ENTRY_NEWSLETTER_TEXT;
          ?>
        </label>
      </div>
    </div>
  </div>

  <h4><?php echo CATEGORY_PASSWORD; ?></h4>
  
  <div class="form-group row">
    <label for="inputPassword" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_PASSWORD; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('password', NULL, 'required aria-required="true" id="inputPassword" autocomplete="new-password" placeholder="' . ENTRY_PASSWORD_TEXT . '"', 'password');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputConfirmation" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo ENTRY_PASSWORD_CONFIRMATION; ?></label>
    <div class="col-sm-9">
      <?php
      echo tep_draw_input_field('confirmation', NULL, 'required aria-required="true" id="inputConfirmation" autocomplete="new-password" placeholder="' . ENTRY_PASSWORD_CONFIRMATION_TEXT . '"', 'password');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fa fa-user', null, 'primary', null, 'btn-success btn-block btn-lg'); ?></div>
  </div>

</div>

</form>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
