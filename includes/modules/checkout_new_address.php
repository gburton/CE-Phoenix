<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  if (!isset($process)) $process = false;
?>

  <div class="contentText">

<?php
  if (ACCOUNT_GENDER == 'true') {
    if (isset($gender)) {
      $male = ($gender == 'm') ? true : false;
      $female = ($gender == 'f') ? true : false;
    } else {
      $male = false;
      $female = false;
    }
?>
    
    <div class="form-group row">
      <label class="col-form-label col-sm-3 text-sm-right"><?php echo ENTRY_GENDER; ?></label>
      <div class="col-sm-9">
        <div class="form-check form-check-inline">
          <?php echo tep_draw_radio_field('gender', 'm', $male, 'id="genderM" aria-describedby="atGender"'); ?>
          &nbsp;<label class="form-check-label" for="genderM"><?php echo MALE; ?></label>
        </div>
        <div class="form-check form-check-inline">
          <?php echo tep_draw_radio_field('gender', 'f', $female, 'id="genderF" aria-describedby="atGender"'); ?>
          &nbsp;<label class="form-check-label" for="genderF"><?php echo FEMALE; ?></label>
        </div>    
        <?php if (tep_not_null(ENTRY_GENDER_TEXT)) echo '<span id="atGender" class="form-text">' . ENTRY_GENDER_TEXT . '</span>'; ?>
      </div>
    </div>

<?php
  }
?>

    <div class="form-group row">
      <label for="inputFirstName" class="col-form-label col-sm-3 text-sm-right"><?php echo ENTRY_FIRST_NAME; ?></label>
      <div class="col-sm-9">
        <?php
        echo tep_draw_input_field('firstname', NULL, 'id="inputFirstName" placeholder="' . ENTRY_FIRST_NAME_TEXT . '"');
        ?>
      </div>
    </div>
    <div class="form-group row">
      <label for="inputLastName" class="col-form-label col-sm-3 text-sm-right"><?php echo ENTRY_LAST_NAME; ?></label>
      <div class="col-sm-9">
        <?php
        echo tep_draw_input_field('lastname', NULL, 'id="inputLastName" placeholder="' . ENTRY_LAST_NAME_TEXT . '"');
        ?>
      </div>
    </div>

<?php
  if (ACCOUNT_COMPANY == 'true') {
?>

    <div class="form-group row">
      <label for="inputCompany" class="col-form-label col-sm-3 text-sm-right"><?php echo ENTRY_COMPANY; ?></label>
      <div class="col-sm-9">
        <?php
        echo tep_draw_input_field('company', NULL, 'id="inputCompany" placeholder="' . ENTRY_COMPANY_TEXT . '"');
        ?>
      </div>
    </div>

<?php
  }
?>

    <div class="form-group row">
      <label for="inputStreet" class="col-form-label col-sm-3 text-sm-right"><?php echo ENTRY_STREET_ADDRESS; ?></label>
      <div class="col-sm-9">
        <?php
        echo tep_draw_input_field('street_address', NULL, 'id="inputStreet" placeholder="' . ENTRY_STREET_ADDRESS_TEXT . '"');
        ?>
      </div>
    </div>

<?php
  if (ACCOUNT_SUBURB == 'true') {
?>

    <div class="form-group row">
      <label for="inputSuburb" class="col-form-label col-sm-3 text-sm-right"><?php echo ENTRY_SUBURB; ?></label>
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
      <label for="inputCity" class="col-form-label col-sm-3 text-sm-right"><?php echo ENTRY_CITY; ?></label>
      <div class="col-sm-9">        
        <?php
        echo tep_draw_input_field('city', NULL, 'id="inputCity" placeholder="' . ENTRY_CITY_TEXT . '"');
        ?>
      </div>
    </div>
    <div class="form-group row">
      <label for="inputZip" class="col-form-label col-sm-3 text-sm-right"><?php echo ENTRY_POST_CODE; ?></label>
      <div class="col-sm-9">
        <?php
        echo tep_draw_input_field('postcode', NULL, 'id="inputZip" placeholder="' . ENTRY_POST_CODE_TEXT . '"');
        ?>
      </div>
    </div>

<?php
  if (ACCOUNT_STATE == 'true') {
?>

    <div class="form-group row">
      <label for="inputState" class="col-form-label col-sm-3 text-sm-right"><?php echo ENTRY_STATE; ?></label>
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
            if (tep_not_null(ENTRY_STATE_TEXT)) echo '<span id="atState" class="form-text">' . ENTRY_STATE_TEXT . '</span>';
          } else {
            echo tep_draw_input_field('state', NULL, 'id="inputState" placeholder="' . ENTRY_STATE_TEXT . '"');
          }
        } else {
          echo tep_draw_input_field('state', NULL, 'id="inputState" placeholder="' . ENTRY_STATE_TEXT . '"');
        }
        ?>
      </div>
    </div>

<?php
  }
?>
    <div class="form-group row">
      <label for="inputCountry" class="col-form-label col-sm-3 text-sm-right"><?php echo ENTRY_COUNTRY; ?></label>
      <div class="col-sm-9">
        <?php
        echo tep_get_country_list('country', NULL, 'aria-describedby="atCountry" id="inputCountry"');
        if (tep_not_null(ENTRY_COUNTRY_TEXT)) echo '<span id="atCountry" class="form-text">' . ENTRY_COUNTRY_TEXT . '</span>';
        ?>
      </div>
    </div>
</div>
