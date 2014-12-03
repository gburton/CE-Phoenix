<?php
/*
  $Id: form_check.js.php,v 1.1.1.1 2002/11/28 23:22:03 wilt Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>

<script language="javascript"><!--

var submitted = false;

function check_form(form) {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  if(submitted){ 
    alert( "<?php echo JS_ERROR_SUBMITTED; ?>"); 
    return false; 
  }
   
<?php if (ACCOUNT_GENDER == 'true') { ?>
  if (form.customers_gender.type != "hidden" && form.customers_gender[0].disabled != true) {
    if (form.customers_gender[0].checked || form.customers_gender[1].checked) {
    } else {
      error_message = error_message + "<?php echo JS_GENDER; ?>";
      error = 1;
    }
  }
<?php } ?>
 
  if (form.elements['customers_firstname'].type != "hidden") {
    if (form.customers_firstname.value == '' || form.customers_firstname.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_FIRST_NAME; ?>";
      error = 1;
    }
  }

  if (form.elements['customers_lastname'].type != "hidden") {
    if (form.customers_lastname.value == '' || form.customers_lastname.length < <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_LAST_NAME; ?>";
      error = 1;
    }
  }

<?php if (ACCOUNT_DOB == 'true') { ?>
  if (form.elements['customers_dob'].type != "hidden" && form.elements['customers_dob'].disabled != true) {
    if (form.customers_dob.value == '' || form.customers_dob.length < <?php echo ENTRY_DOB_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_DOB; ?>";
      error = 1;
    }
  }
<?php } ?>

  if (form.elements['customers_email_address'].type != "hidden") {
    if (form.customers_email_address.value == '' || form.customers_email_address.length < <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_EMAIL_ADDRESS; ?>";
      error = 1;
    }
  }

  if (form.elements['entry_street_address'].type != "hidden") {
    if (form.entry_street_address.value == '' || form.entry_street_address.length < <?php echo ENTRY_STREET_ADDRESS_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_ADDRESS; ?>";
      error = 1;
    }
  }

  if (form.elements['entry_postcode'].type != "hidden") {
    if (form.entry_postcode.value == '' || form.entry_postcode.length < <?php echo ENTRY_POSTCODE_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_POST_CODE; ?>";
      error = 1;
    }
  }

  if (form.elements['entry_city'].type != "hidden") {
    if (form.entry_city.value == '' || form.entry_city.length < <?php echo ENTRY_CITY_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_CITY; ?>";
      error = 1;
    }
  }

<?php if (ACCOUNT_STATE == 'true') { ?>
  if (form.elements['entry_state'].type != "hidden") {
    if (form.entry_state.value == '' || form.entry_state.value.length < <?php echo ENTRY_STATE_MIN_LENGTH; ?> ) {
       error_message = error_message + "<?php echo JS_STATE; ?>";
       error = 1;
    }
  }
<?php } ?>

  if (form.elements['entry_country'].type != "hidden") {
    if (form.entry_country.value == 0) {
      error_message = error_message + "<?php echo JS_COUNTRY; ?>";
      error = 1;
    }
  }

  if (form.elements['customers_telephone'].type != "hidden") {
    if (form.customers_telephone.value == '' || form.customers_telephone.length < <?php echo ENTRY_TELEPHONE_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_TELEPHONE; ?>";
      error = 1;
    }
  }

  if (form.elements['customers_password'].type != "hidden") {
    if ( (form.customers_create_type.value == 'new' && form.customers_password.value == '') || (form.customers_create_type.value == 'new' && form.customers_password.value != '' && (form.customers_password.length < <?php echo ENTRY_PASSWORD_MIN_LENGTH; ?>))) {
      error_message = error_message + "<?php echo JS_PASSWORD; ?>";
      error = 1;
    }
  }

  if (error == 1) { 
    alert(error_message); 
    return false; 
  } else { 
    submitted = true; 
    return true; 
  } 
}
//--></script>