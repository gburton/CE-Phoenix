<?php
/*
  $Id: create_order_details.php,v 1.2 2005/09/04 04:42:56 loic Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_CUSTOMERS_ID; ?></td>
            <td class="main">&nbsp;<?php echo $account['customers_id']; ?><?php echo tep_draw_hidden_field('customers_id', $account['customers_id'])/* tep_draw_input_field('customers_id', $account['customers_id'], "disabled") */ . '&nbsp;' . ENTRY_CUSTOMERS_ID_TEXT; ?> </td>
          </tr>
<?php if (ACCOUNT_GENDER == 'true') { ?>
		  <tr>
            <td class="main">&nbsp;<?php echo ENTRY_GENDER; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_radio_field('customers_gender', 'm', ($account['customers_gender']=='m'?true:false)) . '&nbsp;&nbsp;' . ENTRY_GENDER_MALE . '&nbsp;&nbsp;' . tep_draw_radio_field('customers_gender', 'f', ($account['customers_gender']=='f'?true:false)) . '&nbsp;&nbsp;' . ENTRY_GENDER_FEMALE . '&nbsp;' . ENTRY_GENDER_TEXT; ?> </td>
          </tr>
<?php } ?> 
		  <tr>
            <td class="main">&nbsp;<?php echo ENTRY_FIRST_NAME; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('customers_firstname', $account['customers_firstname']) . '&nbsp;' . ENTRY_FIRST_NAME_TEXT; ?> </td>
          </tr>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_LAST_NAME; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('customers_lastname', $account['customers_lastname']) . '&nbsp;' . ENTRY_LAST_NAME_TEXT; ?> </td>
          </tr>
<?php if (ACCOUNT_DOB == 'true') { ?>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_DATE_OF_BIRTH; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('customers_dob', (!empty($account['customers_dob'])?tep_date_short($account['customers_dob']):'')) . '&nbsp;' . ENTRY_DATE_OF_BIRTH_TEXT; ?> </td>
          </tr>
<?php } ?> 
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_EMAIL_ADDRESS; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('customers_email_address', $account['customers_email_address']) . '&nbsp;' . ENTRY_EMAIL_ADDRESS_TEXT; ?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  
<?php if (ACCOUNT_COMPANY == 'true') { ?>  
  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_COMPANY; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_COMPANY; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('entry_company', $address['entry_company']) . '&nbsp;' . ENTRY_COMPANY_TEXT;?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
<?php } ?>
  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_ADDRESS; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_STREET_ADDRESS; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('entry_street_address', $address['entry_street_address']) . '&nbsp;' . ENTRY_STREET_ADDRESS_TEXT; ?></td>
          </tr>
        <?php if (ACCOUNT_SUBURB == 'true') { ?>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_SUBURB; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('entry_suburb', $address['entry_suburb']) . '&nbsp;' . ENTRY_SUBURB_TEXT; ?></td>
          </tr>
        <?php } ?>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_POST_CODE; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('entry_postcode', $address['entry_postcode']) . '&nbsp;' . ENTRY_POST_CODE_TEXT; ?></td>
          </tr>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_CITY; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('entry_city', $address['entry_city']) . '&nbsp;' . ENTRY_CITY_TEXT;?></td>
          </tr>
        <?php if (ACCOUNT_STATE == 'true') { ?>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_STATE; ?></td>
            <td class="main">
            <?php
			  if (!empty($address['entry_zone_id'])) {
                $zone_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . $address['entry_country_id'] . "' and zone_id = '" . $address['entry_zone_id'] . "'");
                if (tep_db_num_rows($zone_query)) {
                  $zone = tep_db_fetch_array($zone_query);
                  $state = $zone['zone_name'];
                } else {
                  $state = $default_zone;
                }
			  } elseif (!empty($address['entry_state']))  {
			    $state = $address['entry_state'];
			  }
              echo tep_draw_input_field('entry_state', $state) . '&nbsp;' . ENTRY_STATE_TEXT;
            ?>
            </td>
          </tr>
        <?php } ?>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_COUNTRY; ?></td>
            <td class="main">
              <?php
                if ($address['entry_country_id']){
                  echo tep_draw_pull_down_menu('entry_country', tep_get_countries(), $address['entry_country_id']);
                }else{
                  echo tep_draw_pull_down_menu('entry_country', tep_get_countries(), STORE_COUNTRY);
                }
                tep_draw_hidden_field('step', '3');
              ?>
            </td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_CONTACT; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('customers_telephone', $account['customers_telephone']) . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_TEXT; ?> </td>
          </tr>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_FAX_NUMBER; ?></td>
            <td class="main"> <?php echo tep_draw_input_field('customers_fax', $account['customers_fax']) . '&nbsp;' . ENTRY_FAX_NUMBER_TEXT; ?></td>
          </tr>
        </table></td>
      </tr>
	  </table></td>
      </tr>
  <tr>
    <td class="formAreaTitle"><br><?php echo ACCOUNT_EXTRAS; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_ACCOUNT_PASSWORD; ?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('customers_password', '', 'id="customers_password"') . '&nbsp;' . ENTRY_ACCOUNT_PASSWORD_TEXT; ?> </td>
          </tr>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_NEWSLETTER_SUBSCRIBE; ?></td>
            <td class="main"> <?php echo tep_draw_input_field('customers_newsletter', $account['customers_newsletter'], 'id="customers_newsletter"') . '&nbsp;' . ENTRY_NEWSLETTER_SUBSCRIBE_TEXT; ?></td>
          </tr>
        </table></td>
      </tr>
	  </table></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><br> <?php echo TEXT_SELECT_CURRENCY_TITLE; ?></td>
      </tr>
      <tr>
        <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
            <tr>
              <td class="main"><table border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main">&nbsp;<?php echo ENTRY_CURRENCY; ?></td>
                    <td class="main"><?php echo $SelectCurrencyBox ?></td>
                  </tr>
                </table></td>
            </tr>
          </table></td>
      </tr>
            <tr>
        <td class="formAreaTitle"><br> <?php echo TEXT_CS; ?></td>
      </tr>
      <tr>
        <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
            <tr>
              <td class="main"><table border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main">&nbsp;<?php echo ENTRY_ADMIN; ?></td>
                    <?php 
                      if (isset($admin['id'])){
                        $cs_id=$admin['id'].'-'. $admin['username'];
                      }else{
                         $cs_id = $_SERVER['REMOTE_USER']; 
                      }
                    ?>
                    <td class="main">&nbsp;<?php echo tep_draw_input_field('cust_service', $cs_id) ?> </td>
                  </tr>
                </table></td>
            </tr>
          </table></td>
      </tr>
    </table>