<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  $languages = tep_get_languages();

  $action = $_GET['action'] ?? '';

  $option_page = $_GET['option_page'] ?? 1;
  $value_page = $_GET['value_page'] ?? 1;
  $attribute_page = $_GET['attribute_page'] ?? 1;

  $page_info = 'option_page=' . $option_page . '&value_page=' . $value_page . '&attribute_page=' . $attribute_page;

  $OSCOM_Hooks->call('products_attributes', 'preAction');
  
  if (tep_not_null($action)) {
    switch ($action) {
      case 'add_product_options':
        $products_options_id = tep_db_prepare_input($_POST['products_options_id']);
        $option_name_array = $_POST['option_name'];
        $sort_order_array = $_POST['sort_order'];

        for ($i=0, $n=sizeof($languages); $i<$n; $i ++) {
          $option_name = tep_db_prepare_input($option_name_array[$languages[$i]['id']]);
          $sort_order = tep_db_prepare_input($sort_order_array[$languages[$i]['id']]);

          tep_db_query("insert into products_options (products_options_id, products_options_name, language_id, sort_order) values ('" . (int)$products_options_id . "', '" . tep_db_input($option_name) . "', '" . (int)$languages[$i]['id'] . "', '" . tep_db_input($sort_order) . "')");
        }
        
        $OSCOM_Hooks->call('products_attributes', 'addProductOptionsAction');
        
        tep_redirect(tep_href_link('products_attributes.php', $page_info));
        break;
      case 'add_product_option_values':
        $value_name_array = $_POST['value_name'];
        $sort_order_array = $_POST['sort_order'];
        $value_id = tep_db_prepare_input($_POST['value_id']);
        $option_id = tep_db_prepare_input($_POST['option_id']);

        for ($i=0, $n=sizeof($languages); $i<$n; $i ++) {
          $value_name = tep_db_prepare_input($value_name_array[$languages[$i]['id']]);
          $sort_order = tep_db_prepare_input($sort_order_array[$languages[$i]['id']]);

          tep_db_query("insert into products_options_values (products_options_values_id, language_id, products_options_values_name, sort_order) values ('" . (int)$value_id . "', '" . (int)$languages[$i]['id'] . "', '" . tep_db_input($value_name) . "', '" . tep_db_input($sort_order) . "')");
        }

        tep_db_query("insert into products_options_values_to_products_options (products_options_id, products_options_values_id) values ('" . (int)$option_id . "', '" . (int)$value_id . "')");
        
        $OSCOM_Hooks->call('products_attributes', 'addProductOptionValuesAction');

        tep_redirect(tep_href_link('products_attributes.php', $page_info));
        break;
      case 'add_product_attributes':
        $products_id = tep_db_prepare_input($_POST['products_id']);
        $options_id = tep_db_prepare_input($_POST['options_id']);
        $values_id = tep_db_prepare_input($_POST['values_id']);
        $value_price = tep_db_prepare_input($_POST['value_price']);
        $price_prefix = tep_db_prepare_input($_POST['price_prefix']);

        tep_db_query("insert into products_attributes (products_id, options_id, options_values_id, options_values_price, price_prefix) values ('" . (int)$products_id . "', '" . (int)$options_id . "', '" . (int)$values_id . "', '" . (float)tep_db_input($value_price) . "', '" . tep_db_input($price_prefix) . "')");

        $products_attributes_id = tep_db_insert_id();

        if (DOWNLOAD_ENABLED == 'true') {
          $products_attributes_filename = tep_db_prepare_input($_POST['products_attributes_filename']);
          $products_attributes_maxdays = tep_db_prepare_input($_POST['products_attributes_maxdays']);
          $products_attributes_maxcount = tep_db_prepare_input($_POST['products_attributes_maxcount']);

          if (tep_not_null($products_attributes_filename)) {
            tep_db_query("insert into products_attributes_download (products_attributes_id, products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount) values (" . (int)$products_attributes_id . ", '" . tep_db_input($products_attributes_filename) . "', '" . tep_db_input($products_attributes_maxdays) . "', '" . tep_db_input($products_attributes_maxcount) . "')");
          }
        }
        
        $OSCOM_Hooks->call('products_attributes', 'addProductAttributesAction');

        tep_redirect(tep_href_link('products_attributes.php', $page_info));
        break;
      case 'update_option_name':
        $option_name_array = $_POST['option_name'];
        $sort_order_array = $_POST['sort_order'];
        $option_id = tep_db_prepare_input($_POST['option_id']);

        for ($i=0, $n=sizeof($languages); $i<$n; $i ++) {
          $option_name = tep_db_prepare_input($option_name_array[$languages[$i]['id']]);
          $sort_order = tep_db_prepare_input($sort_order_array[$languages[$i]['id']]);

          tep_db_query("update products_options set products_options_name = '" . tep_db_input($option_name) . "', sort_order = '" . tep_db_input($sort_order) . "' where products_options_id = '" . (int)$option_id . "' and language_id = '" . (int)$languages[$i]['id'] . "'");
        }
        
        $OSCOM_Hooks->call('products_attributes', 'updateOptionNameAction');

        tep_redirect(tep_href_link('products_attributes.php', $page_info));
        break;
      case 'update_value':
        $value_name_array = $_POST['value_name'];
        $sort_order_array = $_POST['sort_order'];
        $value_id = tep_db_prepare_input($_POST['value_id']);
        $option_id = tep_db_prepare_input($_POST['option_id']);

        for ($i=0, $n=sizeof($languages); $i<$n; $i ++) {
          $value_name = tep_db_prepare_input($value_name_array[$languages[$i]['id']]);
          $sort_order = tep_db_prepare_input($sort_order_array[$languages[$i]['id']]);

          tep_db_query("update products_options_values set products_options_values_name = '" . tep_db_input($value_name) . "', sort_order = '" . tep_db_input($sort_order) . "' where products_options_values_id = '" . tep_db_input($value_id) . "' and language_id = '" . (int)$languages[$i]['id'] . "'");
        }

        tep_db_query("update products_options_values_to_products_options set products_options_id = '" . (int)$option_id . "'  where products_options_values_id = '" . (int)$value_id . "'");

        $OSCOM_Hooks->call('products_attributes', 'updateValueAction');

        tep_redirect(tep_href_link('products_attributes.php', $page_info));
        break;
      case 'update_product_attribute':
        $products_id = tep_db_prepare_input($_POST['products_id']);
        $options_id = tep_db_prepare_input($_POST['options_id']);
        $values_id = tep_db_prepare_input($_POST['values_id']);
        $value_price = tep_db_prepare_input($_POST['value_price']);
        $price_prefix = tep_db_prepare_input($_POST['price_prefix']);
        $attribute_id = tep_db_prepare_input($_POST['attribute_id']);

        tep_db_query("update products_attributes set products_id = '" . (int)$products_id . "', options_id = '" . (int)$options_id . "', options_values_id = '" . (int)$values_id . "', options_values_price = '" . (float)tep_db_input($value_price) . "', price_prefix = '" . tep_db_input($price_prefix) . "' where products_attributes_id = '" . (int)$attribute_id . "'");

        if (DOWNLOAD_ENABLED == 'true') {
          $products_attributes_filename = tep_db_prepare_input($_POST['products_attributes_filename']);
          $products_attributes_maxdays = tep_db_prepare_input($_POST['products_attributes_maxdays']);
          $products_attributes_maxcount = tep_db_prepare_input($_POST['products_attributes_maxcount']);

          if (tep_not_null($products_attributes_filename)) {
            tep_db_query("replace into products_attributes_download set products_attributes_id = '" . (int)$attribute_id . "', products_attributes_filename = '" . tep_db_input($products_attributes_filename) . "', products_attributes_maxdays = '" . tep_db_input($products_attributes_maxdays) . "', products_attributes_maxcount = '" . tep_db_input($products_attributes_maxcount) . "'");
          }
        }
        
        $OSCOM_Hooks->call('products_attributes', 'updateProductAttributeAction');

        tep_redirect(tep_href_link('products_attributes.php', $page_info));
        break;
      case 'delete_option':
        $option_id = tep_db_prepare_input($_GET['option_id']);

        tep_db_query("delete from products_options where products_options_id = '" . (int)$option_id . "'");
        
        $OSCOM_Hooks->call('products_attributes', 'deleteOptionAction');

        tep_redirect(tep_href_link('products_attributes.php', $page_info));
        break;
      case 'delete_value':
        $value_id = tep_db_prepare_input($_GET['value_id']);

        tep_db_query("delete from products_options_values where products_options_values_id = '" . (int)$value_id . "'");
        tep_db_query("delete from products_options_values_to_products_options where products_options_values_id = '" . (int)$value_id . "'");
        
        $OSCOM_Hooks->call('products_attributes', 'deleteValueAction');

        tep_redirect(tep_href_link('products_attributes.php', $page_info));
        break;
      case 'delete_attribute':
        $attribute_id = tep_db_prepare_input($_GET['attribute_id']);

        tep_db_query("delete from products_attributes where products_attributes_id = '" . (int)$attribute_id . "'");

// added for DOWNLOAD_ENABLED. Always try to remove attributes, even if downloads are no longer enabled
        tep_db_query("delete from products_attributes_download where products_attributes_id = '" . (int)$attribute_id . "'");

        $OSCOM_Hooks->call('products_attributes', 'deleteAttributeAction');
        
        tep_redirect(tep_href_link('products_attributes.php', $page_info));
        break;
    }
  }
  
  $OSCOM_Hooks->call('products_attributes', 'postAction');

  require('includes/template_top.php');
  
  ?>
  
    <h1 class="display-4"><?php echo HEADING_TITLE_ATRIB; ?></h1>
  
  <?php
  $attributes = sprintf(<<<'EOSQL'
select pa.*
 from products_attributes pa
   left join products_options po on po.products_options_id = pa.options_id and po.language_id = %1$d
   left join products_options_values pov on pov.products_options_values_id = pa.options_values_id and pov.language_id = %1$d
   left join products_description pd on pa.products_id = pd.products_id and pd.language_id = %1$d
 order by pd.products_name, po.sort_order, pov.sort_order
EOSQL
    , (int)$languages_id);
 
  $attributes_split = new splitPageResults($attribute_page, MAX_ROW_LISTS_OPTIONS, $attributes, $attributes_query_numrows);
  ?>
  
  <p class="my-2 text-right mr-2"><?php echo $attributes_split->display_links($attributes_query_numrows, MAX_ROW_LISTS_OPTIONS, MAX_DISPLAY_PAGE_LINKS, $attribute_page, 'option_page=' . $option_page . '&value_page=' . $value_page, 'attribute_page'); ?></p>

  <div class="table-responsive">
    <?php
    if ($action == 'update_attribute') {
      $form_action = 'update_product_attribute';
    } else {
      $form_action = 'add_product_attributes';
    }
    ?>
    <form name="attributes" action="<?php echo tep_href_link('products_attributes.php', 'action=' . $form_action . '&' . $page_info); ?>" method="post">
      <table class="table table-striped">
        <thead class="thead-dark">
          <tr>
            <th><?php echo TABLE_HEADING_PRODUCT; ?></th>
            <th><?php echo TABLE_HEADING_OPT_NAME; ?></th>
            <th><?php echo TABLE_HEADING_OPT_VALUE; ?></th>
            <th class="text-right" style="width: 120px;"><?php echo TABLE_HEADING_OPT_PRICE; ?></th>
            <th class="text-center" style="width: 120px;"><?php echo TABLE_HEADING_OPT_PRICE_PREFIX; ?></th>
            <th class="text-right" style="width: 120px;"><?php echo TABLE_HEADING_ACTION; ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $next_id = 1;
          $attributes = tep_db_query($attributes);
          while ($attributes_values = tep_db_fetch_array($attributes)) {
            $products_name_only = tep_get_products_name($attributes_values['products_id']);
            $options_name = tep_options_name($attributes_values['options_id']);
            $values_name = tep_values_name($attributes_values['options_values_id']);

            if (($action == 'update_attribute') && ($_GET['attribute_id'] == $attributes_values['products_attributes_id'])) {
              ?>
              <tr class="table-success">
                <td>
                  <input type="hidden" name="attribute_id" value="<?php echo $attributes_values['products_attributes_id']; ?>">
                  <select name="products_id" class="form-control">
                  <?php
                  $products = tep_db_query("select p.products_id, pd.products_name from products p, products_description pd where pd.products_id = p.products_id and pd.language_id = '" . $languages_id . "' order by pd.products_name");
                  while($products_values = tep_db_fetch_array($products)) {
                    if ($attributes_values['products_id'] == $products_values['products_id']) {
                      echo '<option name="' . $products_values['products_name'] . '" value="' . $products_values['products_id'] . '" SELECTED>' . $products_values['products_name'] . '</option>';
                    } else {
                      echo '<option name="' . $products_values['products_name'] . '" value="' . $products_values['products_id'] . '">' . $products_values['products_name'] . '</option>';
                    }
                  } 
                  ?>
                  </select>
                </td>
                <td>
                  <select name="options_id" class="form-control">
                  <?php
                  $options = tep_db_query("select * from products_options where language_id = '" . $languages_id . "' order by products_options_name");
                  while($options_values = tep_db_fetch_array($options)) {
                    if ($attributes_values['options_id'] == $options_values['products_options_id']) {
                      echo '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '" SELECTED>' . $options_values['products_options_name'] . '</option>';
                    } else {
                      echo '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '">' . $options_values['products_options_name'] . '</option>';
                    }
                  } 
                  ?>
                  </select>
                </td>
                <td>
                  <select name="values_id" class="form-control">
                  <?php
                  $values = tep_db_query("select * from products_options_values where language_id ='" . $languages_id . "' order by products_options_values_name");
                  while($values_values = tep_db_fetch_array($values)) {
                    if ($attributes_values['options_values_id'] == $values_values['products_options_values_id']) {
                      echo "\n" . '<option name="' . $values_values['products_options_values_name'] . '" value="' . $values_values['products_options_values_id'] . '" SELECTED>' . $values_values['products_options_values_name'] . '</option>';
                    } else {
                      echo "\n" . '<option name="' . $values_values['products_options_values_name'] . '" value="' . $values_values['products_options_values_id'] . '">' . $values_values['products_options_values_name'] . '</option>';
                    }
                  } 
                  ?>        
                  </select>
                </td>
                <td class="text-right"><input class="form-control" type="text" name="value_price" value="<?php echo $attributes_values['options_values_price']; ?>"></td>
                <td class="text-right"><input class="form-control" type="text" name="price_prefix" value="<?php echo $attributes_values['price_prefix']; ?>" size="2"></td>
                <td class="text-right"><?php echo tep_draw_bootstrap_button(null, 'fas fa-save text-success', null, 'primary', null, 'btn-link') . tep_draw_bootstrap_button(null, 'fas fa-times text-dark', tep_href_link('products_attributes.php', $page_info), null, null, 'btn-link'); ?></td>
              </tr>
              <?php
              if (DOWNLOAD_ENABLED == 'true') {
                $download_query_raw ="select products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount from products_attributes_download where products_attributes_id='" . $attributes_values['products_attributes_id'] . "'";
                $download_query = tep_db_query($download_query_raw);
                if (tep_db_num_rows($download_query) > 0) {
                  $download = tep_db_fetch_array($download_query);
                  $products_attributes_filename = $download['products_attributes_filename'];
                  $products_attributes_maxdays  = $download['products_attributes_maxdays'];
                  $products_attributes_maxcount = $download['products_attributes_maxcount'];
                }
                ?>
                <tr>
                  <td colspan="6">
                    <table>
                      <tr>
                        <td><?php echo TABLE_HEADING_DOWNLOAD; ?></td>
                        <td><?php echo TABLE_TEXT_FILENAME; ?></td>
                        <td><?php echo tep_draw_input_field('products_attributes_filename', $products_attributes_filename, 'class="form-control"'); ?></td>
                        <td><?php echo TABLE_TEXT_MAX_DAYS; ?></td>
                        <td><?php echo tep_draw_input_field('products_attributes_maxdays', $products_attributes_maxdays, 'class="form-control"'); ?></td>
                        <td><?php echo TABLE_TEXT_MAX_COUNT; ?></td>
                        <td><?php echo tep_draw_input_field('products_attributes_maxcount', $products_attributes_maxcount, 'class="form-control"'); ?></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              <?php
              }
            } elseif (($action == 'delete_product_attribute') && ($_GET['attribute_id'] == $attributes_values['products_attributes_id'])) {
              ?>
              <tr class="table-danger">
                <td><?php echo $products_name_only; ?></td>
                <td><?php echo $options_name; ?></td>
                <td><?php echo $values_name; ?></td>
                <td class="text-right"><?php echo $attributes_values["options_values_price"]; ?></td>
                <td class="text-center"><?php echo $attributes_values["price_prefix"]; ?></td>
                <td class="text-right"><?php echo tep_draw_bootstrap_button(null, 'fas fa-trash text-danger', tep_href_link('products_attributes.php', 'action=delete_attribute&attribute_id=' . $_GET['attribute_id'] . '&' . $page_info), null, null, 'btn-link') . tep_draw_bootstrap_button(null, 'fas fa-times text-dark', tep_href_link('products_attributes.php', $page_info), null, null, 'btn-link'); ?></td>
              </tr>
              <?php
            } else {
            ?>
            <tr>
              <td><?php echo $products_name_only; ?></td>
              <td><?php echo $options_name; ?></td>
              <td><?php echo $values_name; ?></td>
              <td class="text-right"><?php echo $attributes_values["options_values_price"]; ?></td>
              <td class="text-center"><?php echo $attributes_values["price_prefix"]; ?></td>
              <td class="text-right"><?php echo tep_draw_bootstrap_button(null, 'fas fa-cogs text-dark', tep_href_link('products_attributes.php', 'action=update_attribute&attribute_id=' . $attributes_values['products_attributes_id'] . '&' . $page_info), null, null, 'btn-link') . tep_draw_bootstrap_button(null, 'fas fa-trash text-danger', tep_href_link('products_attributes.php', 'action=delete_product_attribute&attribute_id=' . $attributes_values['products_attributes_id'] . '&' . $page_info), null, null, 'btn-link'); ?></td>
            </tr>
            <?php
            }
            $max_attributes_id_query = tep_db_query("select max(products_attributes_id) + 1 as next_id from products_attributes");
            $max_attributes_id_values = tep_db_fetch_array($max_attributes_id_query);
            $next_id = $max_attributes_id_values['next_id'];
          }
    
          if ($action != 'update_attribute') {
            ?>
            <tr class="bg-white">
              <td>
                <select name="products_id" class="form-control">
                  <option value=""><?php echo PLEASE_SELECT_OPTION; ?></option>
                  <?php
                  $products = tep_db_query("select p.products_id, pd.products_name from products p, products_description pd where pd.products_id = p.products_id and pd.language_id = '" . $languages_id . "' order by pd.products_name");
                  while ($products_values = tep_db_fetch_array($products)) {
                    echo '<option name="' . $products_values['products_name'] . '" value="' . $products_values['products_id'] . '">' . $products_values['products_name'] . '</option>';
                  } 
                  ?>
                </select>
              </td>
              <td>
                <select name="options_id" class="form-control">
                  <option value=""><?php echo PLEASE_SELECT_OPTION; ?></option>
                  <?php
                  $options = tep_db_query("select * from products_options where language_id = '" . $languages_id . "' order by products_options_name");
                  while ($options_values = tep_db_fetch_array($options)) {
                    echo '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '">' . $options_values['products_options_name'] . '</option>';
                  } 
                  ?>
                </select>
              </td>
              <td>
                <select name="values_id" class="form-control">
                  <option value=""><?php echo PLEASE_SELECT_OPTION; ?></option>
                  <?php
                  $values = tep_db_query("select pov.*, pov2po.* from products_options_values pov left join products_options_values_to_products_options pov2po on pov.products_options_values_id = pov2po.products_options_values_id where pov.language_id = '" . (int)$languages_id . "' order by pov.products_options_values_name");

                  while ($values_values = tep_db_fetch_array($values)) {
                    echo '<option name="' . $values_values['products_options_values_name'] . '" value="' . $values_values['products_options_values_id'] . '" data-id="' . $values_values['products_options_id'] . '">' . $values_values['products_options_values_name'] . '</option>';
                  } 
                  ?>
                </select>
              </td>
              <td class="text-right"><input class="form-control" type="text" name="value_price" value="0"></td>
              <td class="text-right"><input class="form-control" type="text" name="price_prefix" value="+"></td>
              <td class="text-right"><?php echo tep_draw_bootstrap_button(null, 'fas fa-plus text-success', null, null, null, 'btn-link'); ?></td>
            </tr>
            <?php
            if (DOWNLOAD_ENABLED == 'true') {
              $products_attributes_maxdays  = DOWNLOAD_MAX_DAYS;
              $products_attributes_maxcount = DOWNLOAD_MAX_COUNT;
              ?>
              <tr>
                <td colspan="6">
                  <table>
                    <tr>
                      <td><?php echo TABLE_HEADING_DOWNLOAD; ?></td>
                      <td><?php echo TABLE_TEXT_FILENAME; ?></td>
                      <td><?php echo tep_draw_input_field('products_attributes_filename', $products_attributes_filename, 'class="form-control"'); ?></td>
                      <td><?php echo TABLE_TEXT_MAX_DAYS; ?></td>
                      <td><?php echo tep_draw_input_field('products_attributes_maxdays', $products_attributes_maxdays, 'class="form-control"'); ?></td>
                      <td><?php echo TABLE_TEXT_MAX_COUNT; ?></td>
                      <td><?php echo tep_draw_input_field('products_attributes_maxcount', $products_attributes_maxcount, 'class="form-control"'); ?></td>
                    </tr>
                  </table>
                </td>
              </tr>
              <?php
            }
          }
          ?>
        </tbody>
      </table>
    </form>
  </div>
  
  <hr>
 
  <?php
  if ($action == 'delete_product_option') {
    $options = tep_db_query("select products_options_id, products_options_name from products_options where products_options_id = '" . (int)$_GET['option_id'] . "' and language_id = '" . (int)$languages_id . "'");
    $options_values = tep_db_fetch_array($options);
    ?>
    
    <h1 class="display-4"><?php echo $options_values['products_options_name']; ?></h1>
    
    <div class="table-responsive">
      <table class="table table-striped">
        <?php
        $products = tep_db_query("select p.products_id, pd.products_name, pov.products_options_values_name from products p, products_options_values pov, products_attributes pa, products_description pd where pd.products_id = p.products_id and pov.language_id = '" . (int)$languages_id . "' and pd.language_id = '" . (int)$languages_id . "' and pa.products_id = p.products_id and pa.options_id='" . (int)$_GET['option_id'] . "' and pov.products_options_values_id = pa.options_values_id order by pd.products_name");
        
        if (tep_db_num_rows($products)) {
          ?>
          <thead class="thead-dark">
            <tr>
              <th><?php echo TABLE_HEADING_ID; ?></th>
              <th><?php echo TABLE_HEADING_PRODUCT; ?></th>
              <th><?php echo TABLE_HEADING_OPT_VALUE; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            while ($products_values = tep_db_fetch_array($products)) {
              ?>
              <tr>
                <td><?php echo $products_values['products_id']; ?></td>
                <td><?php echo $products_values['products_name']; ?></td>
                <td><?php echo $products_values['products_options_values_name']; ?></td>
              </tr>
              <?php
            }
            ?>
            <tr>
              <td class="bg-danger text-white" colspan="3"><?php echo TEXT_WARNING_OF_DELETE; ?></td>
            </tr>
            <tr>
              <td colspan="3" class="text-right"><?php echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('products_attributes.php', $page_info), null, null, 'btn-light'); ?></td>
            </tr>
            <?php
          } else {
            ?>
            <tr>
              <td class="bg-success text-white" colspan="3"><?php echo TEXT_OK_TO_DELETE; ?></td>
            </tr>
            <tr>
              <td colspan="3"><?php echo tep_draw_bootstrap_button(null, 'fas fa-trash text-danger', tep_href_link('products_attributes.php', 'action=delete_option&option_id=' . $_GET['option_id'] . '&' . $page_info), null, null, 'btn-link mr-2') . tep_draw_bootstrap_button(null, 'fas fa-times text-dark', tep_href_link('products_attributes.php', $page_info), null, null, 'btn-light'); ?></td>
            </tr>
            <?php
          }
          ?>
        </tbody>
      </table>
    </div>
    <?php
  } else {
    ?>
    
    <h1 class="display-4"><?php echo HEADING_TITLE_OPT; ?></h1>
  
    <?php 
    $options = "select * from products_options where language_id = '" . (int)$languages_id . "' order by sort_order"; 
    $options_split = new splitPageResults($option_page, MAX_ROW_LISTS_OPTIONS, $options, $options_query_numrows);
    ?>
        
    <p class="my-2 text-right mr-2"><?php echo $options_split->display_links($options_query_numrows, MAX_ROW_LISTS_OPTIONS, MAX_DISPLAY_PAGE_LINKS, $option_page, 'value_page=' . $value_page . '&attribute_page=' . $attribute_page, 'option_page'); ?></p>

    <div class="table-responsive">
      <table class="table table-striped">
        <thead class="thead-dark">
          <tr>
            <th><?= TABLE_HEADING_OPT_NAME; ?></th>
            <th><?= TABLE_HEADING_OPT_SORT_ORDER; ?></th>
            <th class="text-right" style="width: 120px;"><?= TABLE_HEADING_ACTION; ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $next_id = 1;
          $options = tep_db_query($options);
          while ($options_values = tep_db_fetch_array($options)) {
            if (($action == 'update_option') && ($_GET['option_id'] == $options_values['products_options_id'])) {
              $inputs = $sort = null;
              for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
                $option_name = tep_db_query("select products_options_name, sort_order from products_options where products_options_id = '" . $options_values['products_options_id'] . "' and language_id = '" . $languages[$i]['id'] . "'");
                $option_name = tep_db_fetch_array($option_name);
                
                $inputs .= '<div class="input-group mb-1">';
                  $inputs .= '<div class="input-group-prepend">';
                    $inputs .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
                  $inputs .= '</div>';
                  $inputs .= '<input type="text" name="option_name[' . $languages[$i]['id'] . ']" required aria-required="true" class="form-control" value="' . $option_name['products_options_name'] . '">';
                $inputs .= '</div>';
                
                $sort .= '<div class="input-group mb-1">';
                  $sort .= '<div class="input-group-prepend">';
                    $sort .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
                  $sort .= '</div>';
                  $sort .= '<input type="text" name="sort_order[' . $languages[$i]['id'] . ']" required aria-required="true" class="form-control" value="' . $option_name['sort_order'] . '">';
                $sort .= '</div>';
                
              }
              ?>
            <tr class="table-success">
              <td colspan="3">
                <?php echo '<form name="option" action="' . tep_href_link('products_attributes.php', 'action=update_option_name&' . $page_info) . '" method="post">'; ?>
                  <div class="form-row">
                    <div class="col-6">
                      <input type="hidden" name="option_id" value="<?php echo $options_values['products_options_id']; ?>">
                      <?php echo $inputs; ?>
                    </div>
                    <div class="col-2">
                      <?php echo $sort; ?>
                    </div>
                    <div class="col-4 text-right">
                      <?= tep_draw_bootstrap_button(null, 'fas fa-save text-success', null, 'primary', null, 'btn-link mr-2') . tep_draw_bootstrap_button(null, 'fas fa-times text-dark', tep_href_link('products_attributes.php', $page_info), null, null, 'btn-link'); ?>
                    </div>
                  </div>
                </form>
              </td>                
            </tr>
            <?php
          } else {
            ?>
            <tr>
              <td><?= $options_values['products_options_name']; ?></td>
              <td class="w-25"><?= $options_values['sort_order']; ?></td>
              <td class="w-25 text-right"><?= tep_draw_bootstrap_button(null, 'fas fa-cogs text-dark', tep_href_link('products_attributes.php', 'action=update_option&option_id=' . $options_values['products_options_id'] . '&' . $page_info), null, null, 'btn-link') . tep_draw_bootstrap_button(null, 'fas fa-trash text-danger', tep_href_link('products_attributes.php', 'action=delete_product_option&option_id=' . $options_values['products_options_id'] . '&' . $page_info), null, null, 'btn-link'); ?></td>
            </tr>
            <?php
          }
          
          $max_options_id_query = tep_db_query("select max(products_options_id) + 1 as next_id from products_options");
          $max_options_id_values = tep_db_fetch_array($max_options_id_query);
          $next_id = $max_options_id_values['next_id'];
        }
        
        if ($action != 'update_option') {
          $inputs = $sort = null;
          for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
            $inputs .= '<div class="input-group mb-1">';
              $inputs .= '<div class="input-group-prepend">';
                $inputs .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
              $inputs .= '</div>';
              $inputs .= '<input type="text" name="option_name[' . $languages[$i]['id'] . ']" required aria-required="true" class="form-control">';
            $inputs .= '</div>';
            
             $sort .= '<div class="input-group mb-1">';
              $sort .= '<div class="input-group-prepend">';
                $sort .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
              $sort .= '</div>';
              $sort .= '<input type="number" name="sort_order[' . $languages[$i]['id'] . ']" required aria-required="true" class="form-control">';
            $sort .= '</div>';
            
          }
          ?>
          <tr class="bg-white">
            <td colspan="3">
              <?php echo '<form name="options" action="' . tep_href_link('products_attributes.php', 'action=add_product_options&' . $page_info) . '" method="post"><input type="hidden" name="products_options_id" value="' . $next_id . '">'; ?>
                <div class="form-row">
                  <div class="col-6">
                    <?= $inputs; ?>
                  </div>
                  <div class="col-2">
                    <?= $sort; ?>
                  </div>
                  <div class="col-4 text-right">
                    <?= tep_draw_bootstrap_button(null, 'fas fa-plus text-success', null, null, null, 'btn-link'); ?>
                  </div>
                </div>
              </form>
            </td>                
          </tr>
          <?php
        }
        ?>
        </tbody>
      </table>
    </div>
    <?php
  }
  ?>
  
  <hr>

  <?php
  if ($action == 'delete_option_value') {
    $values = tep_db_query("select products_options_values_id, products_options_values_name from products_options_values where products_options_values_id = '" . (int)$_GET['value_id'] . "' and language_id = '" . (int)$languages_id . "'");
    $values_values = tep_db_fetch_array($values);
    ?>
    
    <h1 class="display-4"><?php echo $values_values['products_options_values_name']; ?></h1>
    
    <div class="table-responsive">
      <table class="table table-striped">
        <?php
        $products = tep_db_query("select p.products_id, pd.products_name, po.products_options_name from products p, products_attributes pa, products_options po, products_description pd where pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "' and po.language_id = '" . (int)$languages_id . "' and pa.products_id = p.products_id and pa.options_values_id='" . (int)$_GET['value_id'] . "' and po.products_options_id = pa.options_id order by pd.products_name");
        
        if (tep_db_num_rows($products)) {
          ?>
          <thead class="thead-dark">
            <tr>
              <th><?php echo TABLE_HEADING_ID; ?></th>
              <th><?php echo TABLE_HEADING_PRODUCT; ?></th>
              <th><?php echo TABLE_HEADING_OPT_NAME; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            while ($products_values = tep_db_fetch_array($products)) {
              ?>
              <tr>
                <td><?php echo $products_values['products_id']; ?></td>
                <td><?php echo $products_values['products_name']; ?></td>
                <td><?php echo $products_values['products_options_name']; ?></td>
              </tr>
              <?php
            }
            ?>
            <tr>
              <td class="bg-danger text-white" colspan="3"><?php echo TEXT_WARNING_OF_DELETE; ?></td>
            </tr>
            <tr>
              <td class="text-right bg-white" colspan="3"><?php echo tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left text-dark', tep_href_link('products_attributes.php', $page_info), null, null, 'btn-light btn-block'); ?></td>
            </tr>
            <?php
        } else {
          ?>
          <tr>
            <td class="bg-success text-white" colspan="3"><?php echo TEXT_OK_TO_DELETE; ?></td>
          </tr>
          <tr>
            <td class="text-right" colspan="3"><?php echo tep_draw_bootstrap_button(null, 'fas fa-trash text-danger', tep_href_link('products_attributes.php', 'action=delete_value&value_id=' . $_GET['value_id'] . '&' . $page_info), null, null, 'btn-link mr-2') .  tep_draw_bootstrap_button(null, 'fas fa-times text-dark', tep_href_link('products_attributes.php', $page_info), null, null, 'btn-link'); ?></td>
          </tr>
          <?php
        }
        ?>
        </tbody>
      </table>
    </div>
    <?php
  }
  else {
    ?>
    <h1 class="display-4"><?php echo HEADING_TITLE_VAL; ?></h1>
    
    <?php
    $values = "select po.*, pov.*, pov2po.* from products_options po, products_options_values pov left join products_options_values_to_products_options pov2po on pov.products_options_values_id = pov2po.products_options_values_id where pov.language_id = '" . (int)$languages_id . "' and po.products_options_id = pov2po.products_options_id order by po.sort_order, pov.sort_order";
    
    $values_split = new splitPageResults($value_page, MAX_ROW_LISTS_OPTIONS, $values, $values_query_numrows);
    ?>
        
    <p class="my-2 text-right mr-2"><?php echo $values_split->display_links($values_query_numrows, MAX_ROW_LISTS_OPTIONS, MAX_DISPLAY_PAGE_LINKS, $value_page, 'option_page=' . $option_page . '&attribute_page=' . $attribute_page, 'value_page'); ?></p>
    
    <div class="table-responsive">
      <table class="table table-striped">
        <thead class="thead-dark">
          <tr>
            <th class="w-25"><?php echo TABLE_HEADING_OPT_NAME; ?></th>
            <th class="w-25"><?php echo TABLE_HEADING_OPT_VALUE; ?></th>
            <th><?php echo TABLE_HEADING_OPT_SORT_ORDER; ?></th>
            <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $next_id = 1;
          $values = tep_db_query($values);
          while ($values_values = tep_db_fetch_array($values)) {
            $options_name = $values_values['products_options_name'];
            $values_name = $values_values['products_options_values_name'];
            $values_sort_order = $values_values['sort_order'];

            if (($action == 'update_option_value') && ($_GET['value_id'] == $values_values['products_options_values_id'])) {
              $inputs = $sort = null;
              for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
                $value_name = tep_db_query("select products_options_values_name, sort_order from products_options_values where products_options_values_id = '" . (int)$values_values['products_options_values_id'] . "' and language_id = '" . (int)$languages[$i]['id'] . "'");
                $value_name = tep_db_fetch_array($value_name);
                
                $inputs .= '<div class="input-group mb-1">';
                  $inputs .= '<div class="input-group-prepend">';
                    $inputs .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
                  $inputs .= '</div>';
                  $inputs .= '<input type="text" name="value_name[' . $languages[$i]['id'] . ']" required aria-required="true" class="form-control" value="' . $value_name['products_options_values_name'] . '">';
                $inputs .= '</div>';
                
                $sort .= '<div class="input-group mb-1">';
                  $sort .= '<div class="input-group-prepend">';
                    $sort .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
                  $sort .= '</div>';
                  $sort .= '<input type="text" name="sort_order[' . $languages[$i]['id'] . ']" required aria-required="true" class="form-control" value="' . $value_name['sort_order'] . '">';
                $sort .= '</div>';
              }
              ?>
              <tr class="table-success">
                <td colspan="4">
                  <?php echo '<form name="values" action="' . tep_href_link('products_attributes.php', 'action=update_value&' . $page_info) . '" method="post">'; ?>
                    <input type="hidden" name="value_id" value="<?php echo $values_values['products_options_values_id']; ?>">
                    <div class="form-row">
                      <div class="col-3">
                        <select name="option_id" class="form-control">
                          <?php
                          $options = tep_db_query("select products_options_id, products_options_name from products_options where language_id = '" . (int)$languages_id . "' order by products_options_name");
                          while ($options_values = tep_db_fetch_array($options)) {
                            echo "\n" . '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '"';
                            if ($values_values['products_options_id'] == $options_values['products_options_id']) { 
                              echo ' selected';
                            }
                            echo '>' . $options_values['products_options_name'] . '</option>';
                          } 
                          ?>
                        </select>
                      </div>
                      <div class="col-3">
                        <?php echo $inputs; ?>
                      </div>
                      <div class="col-2">
                        <?php echo $sort; ?> 
                      </div>
                      <div class="col-4 text-right">
                        <?php echo tep_draw_bootstrap_button(null, 'fas fa-save text-success', null, 'primary', null, 'btn-link') . tep_draw_bootstrap_button(null, 'fas fa-times text-dark', tep_href_link('products_attributes.php', $page_info), null, null, 'btn-link'); ?>
                      </div>
                    </div>
                  </form>
                </td>                
              </tr>
              <?php
            } else {
              ?>
              <tr>
                <td><?php echo $options_name; ?></td>
                <td><?php echo $values_name; ?></td>
                <td><?php echo $values_sort_order; ?></td>
                <td class="text-right"><?php echo tep_draw_bootstrap_button(null, 'fas fa-cogs text-dark', tep_href_link('products_attributes.php', 'action=update_option_value&value_id=' . $values_values['products_options_values_id'] . '&' . $page_info), null, null, 'btn-link') . tep_draw_bootstrap_button(null, 'fas fa-trash text-danger', tep_href_link('products_attributes.php', 'action=delete_option_value&value_id=' . $values_values['products_options_values_id'] . '&' . $page_info), null, null, 'btn-link'); ?></td>
              </tr>
              <?php
            }
            $max_values_id_query = tep_db_query("select max(products_options_values_id) + 1 as next_id from products_options_values");
            $max_values_id_values = tep_db_fetch_array($max_values_id_query);
            $next_id = $max_values_id_values['next_id'];
          }
          if ($action != 'update_option_value') {
            ?>
            <tr class="bg-white">
              <td colspan="4">
                <?php echo '<form name="values" action="' . tep_href_link('products_attributes.php', 'action=add_product_option_values&' . $page_info) . '" method="post">'; ?>
                  <div class="form-row">
                    <div class="col-3">
                      <select name="option_id" class="form-control">
                      <?php
                      $options = tep_db_query("select products_options_id, products_options_name from products_options where language_id = '" . $languages_id . "' order by products_options_name");
                      while ($options_values = tep_db_fetch_array($options)) {
                        echo '<option name="' . $options_values['products_options_name'] . '" value="' . $options_values['products_options_id'] . '">' . $options_values['products_options_name'] . '</option>';
                      }

                      $inputs = $sort = null;
                      for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
                        $inputs .= '<div class="input-group mb-1">';
                          $inputs .= '<div class="input-group-prepend">';
                            $inputs .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
                          $inputs .= '</div>';
                          $inputs .= '<input type="text" name="value_name[' . $languages[$i]['id'] . ']" required aria-required="true" class="form-control">';
                        $inputs .= '</div>';
                        
                        $sort .= '<div class="input-group mb-1">';
                          $sort .= '<div class="input-group-prepend">';
                            $sort .= '<span class="input-group-text">'. tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']) . '</span>';
                          $sort .= '</div>';
                          $sort .= '<input type="text" name="sort_order[' . $languages[$i]['id'] . ']" required aria-required="true" class="form-control">';
                        $sort .= '</div>';
                      }
                      ?>
                      </select>
                    </div>
                    <div class="col-3">
                      <input type="hidden" name="value_id" value="<?php echo $next_id; ?>">
                      <?php echo $inputs; ?>
                    </div>
                    <div class="col-2">
                      <?php echo $sort; ?>
                    </div>
                    <div class="col-4 text-right">
                      <?php echo tep_draw_bootstrap_button(null, 'fas fa-plus text-success', null, null, null, 'btn-link'); ?>
                    </div>
                  </div>
                </form>
              </td>
            </tr>
            <?php
          }
          ?>
        </tbody>
      </table>
    </div>
    <?php
  }

  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
