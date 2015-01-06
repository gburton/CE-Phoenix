<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

  File information: AAS main php file

*/

//Uncomment bellow lines for debugging
//error_reporting(E_ALL);
//error_reporting(-1);
//ini_set('error_reporting', E_ALL);

require 'includes/application_top.php';

if(!tep_session_is_registered('admin') && isset($sessionTimeout) && $sessionTimeout==true) tep_redirect(tep_href_link(FILENAME_LOGIN, (isset($redirect_origin['auth_user']) ? 'action=process' : '')));

//store all session variables under admin|AAS
if(!isset($_SESSION['admin']['AAS'])) $_SESSION['admin']['AAS']=array();

define('AAS', 1);

if(!isset($_SESSION['admin']['AAS']['ajaxToken']) || empty($_SESSION['admin']['AAS']['ajaxToken'])) $_SESSION['admin']['AAS']['ajaxToken']=md5(uniqid(mt_rand(),true)); //generateToken(); //md5(uniqid(mt_rand(),true)); //session_id();
define('AAS_AJAX',$_SESSION['admin']['AAS']['ajaxToken']);

//load language
if(file_exists('ext/aas/languages/'.$language.'.php')) require 'ext/aas/languages/'.$language.'.php';
elseif(file_exists('ext/aas/languages/english.php')) require 'ext/aas/languages/english.php';
else{ ?>
<div style="text-align:center">
<h1>Alternative Administration System</h1>
Error: cannot find language file: <strong>ext/aas/languages/<?php echo $language; ?>.php</strong>
<br><br>
<a href="<?php echo tep_href_link(FILENAME_DEFAULT, ''); ?>" >Back to default admin panel <img src="ext/aas/images/glyphicons_020_home.png" alt="" style="width:24px;"></a>
</div>
<?php
exit;
}

require 'ext/aas/application_top.php';

?>
<!DOCTYPE html>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo AAS_TITLE; ?></title>
<link rel="shortcut icon" href="ext/aas/images/favicon.ico">
<link rel="stylesheet" href="ext/aas/css/smoothness/jquery-ui-1.10.4.custom.css">
<?php if($defaults['enableCalendar']){ ?>
<link href="ext/aas/plugins/calendar/css/fullcalendar.css" rel="stylesheet" />
<link href="ext/aas/plugins/calendar/css/fullcalendar.print.css" rel="stylesheet" media="print" />
<?php } ?>
<link rel="stylesheet" href="ext/aas/css/style.css">
<link rel="stylesheet" href="ext/aas/css/print.css" type="text/css" media="print">
<?php if($modules_count>0){ foreach($modules as $key => $module){ ?>
<?php if(tep_not_null($module['css']['style'])){ foreach($module['css']['style'] as $ext_css){ if(tep_not_null($ext_css)){ ?><link href="ext/aas_modules/<?php echo $key; ?>/css/<?php echo $ext_css; ?>" rel="stylesheet" /><?php }}} ?>

<?php if(tep_not_null($module['css']['print'])){ foreach($module['css']['print'] as $ext_css){ if(tep_not_null($ext_css)){ ?><link href="ext/aas_modules/<?php echo $key; ?>/css/<?php echo $ext_css; ?>" rel="stylesheet" media="print" /><?php }}} ?>
<?php } } ?>
<script type="text/javascript" src="ext/aas/js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="ext/aas/js/jquery-ui-1.10.4.custom.min.js"></script>
<script type="text/javascript" src="ext/aas/js/jquery.ui.resizable_extend.js"></script>
<script type="text/javascript" src="ext/aas/js/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript" src="ext/aas/editors/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="ext/aas/editors/tinymce/js/tinymce/jquery.tinymce.min.js"></script>
<script type="text/javascript" src="ext/aas/editors/tinymce/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="ext/aas/js/jquery.jeditable.js"></script>
<?php if(isset($enableColumnSorting) && $enableColumnSorting){ ?>
<script type="text/javascript" src="ext/aas/js/jquery.tablesorter.min.js"></script>
<script type="text/javascript">$(function(){
 $("table#tbl").tablesorter({headers: { <?php echo $sorting; ?> }  });
});</script>
<?php } ?>
<?php include 'ext/aas/js.php'; ?>
<script type="text/javascript" src="ext/aas/js/dialogs.js"></script>
<script type="text/javascript" src="ext/aas/js/functions.js"></script>
<script type="text/javascript" src="ext/aas/js/aas.js"></script>
<script type="text/javascript" src="ext/aas/js/filedrop/jquery.filedrop.js"></script>
<script type="text/javascript" src="ext/aas/js/jquery.dragtable.js"></script>
<script type="text/javascript" src="ext/aas/js/jquery.jOrgChart.js"></script>
<script type="text/javascript" src="ext/aas/js/moment.min.js"></script>
<script>
<?php if($modules_count>0){
$modules_counter=1; foreach($modules as $key => $module){ ?>
modules_installed['<?php echo $module["code"]; ?>']=<?php echo $module["version"]; ?>;
<?php } } ?>
</script>
</head>
<body>

<div id="panel" style="<?php echo isset($search) && $search!='' ? '':'display:none;'; ?>">
  <div style="overflow:hidden;text-align:center">
    <?php echo tep_draw_form('search', FILENAME_AAS, '', 'get');
    echo tep_draw_input_field('search','','id="lfora" class="lfora0" placeholder="'.AAS_TEXT_PLACEHOLDER_SEARCH.'" style="display:inline-block;vertical-align:top;" required="required"'); ?>
    <?php if(isset($_GET['search'])){ ?><input class="search_clear_button lfor" onclick="clearSearchQuery('<?php echo isset($_SESSION['admin']['AAS']['preSearchUrl']) ? $_SESSION['admin']['AAS']['preSearchUrl'] : tep_href_link(FILENAME_AAS, ''); ?>')" type="button" value="x" /><?php } ?>
    <?php echo tep_draw_pull_down_menu('searchOnField', $searchOnFieldArray, '', ' style="display:inline-block;vertical-align:top;" onchange="this.form.submit();"'); ?>

    <select style="display:inline-block;vertical-align:top;" name="orderBy" onchange="submit();">
    <?php foreach($orderByArray as $key => $value) echo '<option '.(isset($_GET['orderBy']) && $_GET['orderBy']==$key ? 'selected="selected"' : '' ).' value="'.$key.'">'.$value.'</option>'; ?>
    </select>
    <select style="display:inline-block;vertical-align:top;" name="ascDesc" onchange="submit();">
    <?php foreach($ascDescArray as $key => $value) echo '<option '.(isset($_GET['ascDesc']) && $_GET['ascDesc']==$key ? 'selected="selected"' : '' ).' value="'.$key.'">'.$value.'</option>'; ?>
    </select>
    </form>
  </div>
</div>

<div id="koumpakia-wrapper">

<?php if(!isset($aasAac['default']['search'][$_SESSION['admin']['id']])){ ?>
  <div id="topPanelToggle" class="koumpakia buttonakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_SEARCH; ?>" ><img src="ext/aas/images/glyphicons_027_search.png" alt=""></div>
<?php } ?>

<?php if(!isset($aasAac['default']['print'][$_SESSION['admin']['id']])){
    if($categories_entries>0 || $entries>0){ ?>
  <div id="printbutton" class="koumpakia buttonakia applyButtonDropdownOnClick" data-title="<?php echo AAS_BUTTON_TOOLTIP_PRINT; ?>" ><img src="ext/aas/images/glyphicons_015_print.png" alt=""></div>
  <ul class="dropdown">
    <?php if($categories_entries>0 && $entries>0){ ?><li><a onclick="printHtmlElement(1,this)" href="#"><?php echo AAS_TEXT_PRINT_CATEGORIES_AND_PRODUCTS_TABLE; ?></a><?php if($entries>0){ ?><label><input type="checkbox" name="printHtmlElement1" ><?php echo AAS_TEXT_PRINT_INCLUDE_PAGINATION; ?></label><?php } ?></li><?php } ?>
    <?php if($categories_entries>0){ ?><li><a onclick="printHtmlElement(2,this)" href="#"><?php echo AAS_TEXT_PRINT_CATEGORIES_TABLE; ?></a></li><?php } ?>
    <?php if($entries>0){ ?><li><a onclick="printHtmlElement(3,this)" href="#"><?php echo AAS_TEXT_PRINT_PRODUCTS_TABLE; ?></a><label><input type="checkbox" name="printHtmlElement3" ><?php echo AAS_TEXT_PRINT_INCLUDE_PAGINATION; ?></label></li><?php } ?>
  </ul>
  <?php } } ?>

<?php if(!isset($aasAac['default']['import'][$_SESSION['admin']['id']])){ ?>
  <div id="importbutton" class="koumpakia buttonakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_IMPORT; ?>" ><img src="ext/aas/images/glyphicons_358_file_import.png" alt=""></div>
<?php } ?>
<?php if(!isset($aasAac['default']['export'][$_SESSION['admin']['id']])){ ?>
  <div id="exportbutton" class="koumpakia buttonakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_EXPORT; ?>" ><img src="ext/aas/images/glyphicons_359_file_export.png" alt=""></div>
<?php } ?>

  <div class="koumpakia_spacer"></div>

<?php if(!isset($aasAac['default']['delete_products'][$_SESSION['admin']['id']])){ ?>
  <div id="deletebutton" class="koumpakia buttonakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_DELETE_PRODUCTS; ?>" style="display:none;" ><img src="ext/aas/images/glyphicons_192_circle_remove.png" alt=""></div>
<?php } ?>

<?php if($defaults['enableTempProductsList']){ ?>
  <div id="savedbutton" class="koumpakia buttonakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_ADD_SELECTED_TO_SAVED_LIST; ?>" style="display:none;" ><img src="ext/aas/images/glyphicons_335_pin_classic.png" alt="" style="height:24px"></div>
<?php } ?>

<?php if(!isset($aasAac['default']['all_edit'][$_SESSION['admin']['id']]) && ($categories_entries>0 || $entries>0) ){ ?>
  <div id="masseditbutton" class="koumpakia buttonakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_ALL_EDIT; ?>"><img src="ext/aas/images/glyphicons_030_pencil.png" alt="" style="height:24px"></div>
<?php } ?>

<?php $massColumnsEdit=false; if(!isset($aasAac['default']['mass_columns_edit'][$_SESSION['admin']['id']])){

  //check to see if we have columns that can be mass edited
  foreach($fieldsArray as $key => $value){

   if($value['visible'] && $value['massEdit']){

    if(!isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']])){ $massColumnsEdit=true; break; }
    elseif(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]=='0' ){ $massColumnsEdit=true; break; }

   }

  }
  if($massColumnsEdit){ ?>
  <div id="massColumnsEditbutton" class="koumpakia buttonakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_MASS_EDIT; ?>" ><img src="ext/aas/images/glyphicons_241_flash.png" alt=""></div>
<?php }
 } ?>

  <div class="koumpakia_spacer"></div>

  <?php if($defaults['enableAttributesManager']){ ?><div id="attributesManagerbutton" class="koumpakia buttonakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_ATTRIBUTES_MANAGER; ?>" ><img src="ext/aas/images/glyphicons_048_dislikes.png" alt="" style="height:24px"></div><?php } ?>
  
  <?php if($defaults['enableSpecials']){ ?><div id="specialsbutton" class="koumpakia buttonakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_SPECIALS; ?>" ><img src="ext/aas/images/glyphicons_062_attach.png" alt="" style="height:25px;"></div><?php } ?>
  
  <div class="koumpakia_spacer"></div>
  <?php if($defaults['enableOnlineUsers']){ ?><div id="onlineUsersbutton" class="koumpakia buttonakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_ONLINE_USERS; ?>" ><img src="ext/aas/images/glyphicons_040_stats.png" alt="" style="height:25px;"></div><?php } ?>
  
  <?php if($defaults['enableCalendar']){ ?><div id="calendarbutton" class="koumpakia buttonakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_CALENDAR; ?>" ><img src="ext/aas/images/glyphicons_045_calendar.png" alt="" style="height:25px"></div><?php } ?>
  
  <?php if($defaults['enableClocks']){ ?><div id="clocksbutton" class="koumpakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_CLOCKS; ?>" ><img src="ext/aas/images/glyphicons_054_clock.png" alt="" style="height:25px;"></div><?php } ?>
  <?php if($defaults['enableContactMe']) { ?><div id="contactmebutton" class="koumpakia  buttonakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_CONTACTME; ?>" ><img src="ext/aas/images/glyphicons_077_headset.png" alt="" style="padding-top:0px;height:25px;"></div><?php } ?>
  
  <?php if($defaults['enableDonations']) { ?><div id="donationbutton" class="koumpakia  buttonakia" data-title="<?php echo AAS_BUTTON_TOOLTIP_DONATION; ?>" ><img src="ext/aas/images/glyphicons_226_euro.png" alt="" style="padding-top:2px;"></div><?php } ?>
  
  <div class="koumpakia_spacer"></div>
  <?php if($defaults['displaySettingsButton']){ ?><div id="trigger_settings_dialog" class="koumpakia buttonakia" data-title="<?php echo AAS_HEADING_TITLE_DISPLAY_SETTINGS; ?>" ><img src="ext/aas/images/glyphicons_280_settings.png" alt=""></div><?php } ?>

  <?php if($defaults['displayGoToDefaultAdministrationPanel']){ ?><div class="koumpakia buttonakia" data-title="<?php echo AAS_HEADING_TITLE_BACK_TO_DEFAULT_ADMIN; ?>" ><a href="<?php echo tep_href_link(FILENAME_DEFAULT, ''); ?>" ><img src="ext/aas/images/glyphicons_020_home.png" alt="" style="width:24px;"></a></div><?php } ?>

  <?php if($defaults['displayLogoffButton']){ ?><div id="logoffbutton" class="koumpakia buttonakia" data-title="<?php echo AAS_HEADING_TITLE_LOGOFF; ?>" ><a href="<?php echo tep_href_link(FILENAME_LOGIN, 'action=logoff'); ?>" ><img src="ext/aas/images/glyphicons_026_road.png" alt="" style="height:23px;"></a></div><?php } ?>

<?php if($defaults['displayGoBackButton']){
    if(!empty($cPath_back)) { ?>
  <div style="padding:8px 5px;" class="koumpakia buttonakia" data-title="<?php echo AAS_HEADING_TITLE_BACK_TO_PARENT; ?>" ><a href="<?php echo tep_href_link(FILENAME_AAS, 'cPath='.$parent_id); ?>"><img src="ext/aas/images/glyphicons_221_unshare.png" alt=""></a></div>
    <?php }
  } ?>

  <select class="koumpakia buttonakia modules-select" data-title="<?php echo AAS_MODULES_ADDITIONAL_FEATURES; ?>" onchange="load_module(this)">
    <option value=""><?php echo AAS_MODULES_SELECT_TITLE; ?></option>
<?php if($modules_count>0){
$modules_counter=1; foreach($modules as $key => $module){ ?>
    <option value="<?php echo $key; ?>" class="koumpakia buttonakia" ><?php echo $modules_counter++.') '.$module['title']; ?></option>
<?php } } ?>
    <option disabled="disabled"> </option>
    <?php if($defaults['enableModulesManagerDialog']){ ?><option value="upload_module"><?php echo AAS_TEXT_UPLOAD_MODULE; ?></option><?php } ?>
  
  </select>

</div>

<?php if(count($alerts)>0){ ?>
<div id="alertsWrapper">
<?php foreach($alerts as $alertKey => $alert){ ?>
<div class="alert-<?php echo $alertKey; ?>"><?php echo $alert; ?></div>
<?php } ?>
</div>
<?php } ?>
<?php if($defaults['displayBreadcrumb']){

  $pathakia=tep_get_category_parents($categoryId);
  $pathakia=array_reverse($pathakia,true);
  $currentcPath=(int)$categoryId;

?>
<div id="nav">
  <span class="selected_language_wrapper">[<img title="<?php echo AAS_TEXT_SELECTED_LANGUAGE.$languages_selected['name'].' / '.$languages_selected['code']; ?>" src="<?php echo DIR_WS_CATALOG_LANGUAGES . $languages_selected['directory'] . '/images/' . $languages_selected['image']; ?>" alt="<?php echo $languages_selected['name']; ?>" height="10" >]</span>
  <a href="<?php echo tep_href_link(FILENAME_AAS, 'cPath=0'); ?>"><?php echo AAS_TEXT_TOP; ?></a>&nbsp;<span class="raquo" data-catid="0">&raquo;</span>
<?php
  $pathakia_count=count($pathakia);
  $pathakia_cnt=0;
  foreach($pathakia as $key=>$pathy){
    if($currentcPath==$key){ ?>
  <a class="active"><?php echo $pathy; ?></a>
<?php }else{ ?>
  <a href="<?php echo tep_href_link(FILENAME_AAS, 'cPath='.$key); ?>"><?php echo $pathy; ?></a>
<?php }
    
if(++$pathakia_cnt < $pathakia_count || $categories_entries>0){ ?><span class="raquo" data-catid="<?php echo $key; ?>">&raquo;</span>&nbsp;<?php }
    
  } ?>
</div>
<div id="nav-tooltip"><span class="arrow up"></span><div id="nav-tooltip-data"><img src="ext/aas/images/loading.gif" alt="loading"></div></div>
<?php } ?>

<section id="tbl-wrapper">
<?php if($defaults['paginationPosition']=='top'){ ?>
  <div id="pagination">

    <?php echo $pagination->draw(); ?>
    <?php if($entries>0){ ?>
    <div class="pagination-form-wrapper">
      <form name="itemsPerRowSelection-form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <span style="padding-top:10px;"><?php echo AAS_PAGINATION_MAX_PRODUCTS; ?></span>
        <select class="entriesPerPage" name="entriesPerPage" onchange="submit();">
          <?php foreach($perPageArray as $ppa) echo '<option '.($ppa==$entriesPerPage ? 'selected="selected"' : '').' value="'.$ppa.'">'.$ppa.'</option>'; ?>
        </select><?php if($entries>0){ ?><span style="padding-top:10px;"><?php echo AAS_PAGINATION_DISPLAYING; ?><strong><?php echo $entries; ?></strong><?php echo AAS_PAGINATION_FROM; ?><strong><?php echo $totalRows; ?></strong><?php echo AAS_PAGINATION_PRODUCTS; ?></span><?php } ?>
      </form>
    </div>
    <?php } ?>

  </div>
<?php } ?>
  <?php if($defaults['inTableSearchPosition']['visible'] && $defaults['inTableSearchPosition']['y']=='top'){ ?><div class="inTableSearch-wrapper inTableSearch-wrapper-<?php echo isset($defaults['inTableSearchPosition']['x']) ? $defaults['inTableSearchPosition']['x'] : 'right'; ?>"><input type="text" id="search" placeholder="<?php echo AAS_TEXT_IN_TABLE_SEARCH; ?>" class="inTableSearch inTableSearch-top" /></div><?php } ?>

<div id="tbl_thead_helper" style="display:none;"><table id="tbl0" class="tbl-general tablesorter"></table></div>
<div id="tbl_wrapper">
<table id="tbl" class="tablesorter">
  <thead>
    <tr>
      <th data-clmn="checkbox" class="checkboxen"><input type="checkbox" name="massCheckbox" id="massCheckbox"></th>
      <th data-clmn="diesi" class="diesi">#</th>
      <th data-clmn="name"><?php echo AAS_HEADING_CATEGORIES_PRODUCTS; ?></th>
<?php foreach($fieldsArray as $key => $value){

        if($value['visible'] ){

          if($key=='products_price_gross' && isset($currency_symbols[0])) echo'<th data-clmn="'.$key.'" class="draggable"><div class="draghandle">&nbsp;</div>'.$currency_symbols[0]['symbol_left'].' '.$value['theadText'].' '.$currency_symbols[0]['symbol_right'].'</th>'.PHP_EOL;
          else echo'<th data-clmn="'.$key.'" class="draggable"><div class="draghandle">&nbsp;</div>'.$value['theadText'].'</th>'.PHP_EOL;

        }

      } ?>
    </tr>
  </thead>
<?php if($displayCategories && $categories_entries>0){ ?>
  <tbody class="categories_tbody">
<?php //FIND CATEGORIES

    $anwkatwteleia=($bool_count_products || $bool_count_subcategories) ?' : ':'';
    $comma=($bool_count_products && $bool_count_subcategories) ? ' , ':'';

    while ($categories = tep_db_fetch_array($categories_query)){

      //The next two lines slow execution down if count_products=true or/and count_subcategories=true. By default they are set to false.
      $bcp = $bool_count_products ? tep_products_in_category_count($categories['categories_id'],true).'<span class="cat_products_count" id="catid_'.$categories['categories_id'].'">'.AAS_HEADING_PRODUCTS_COUNT.'</span>' : '';
      $bcs = $bool_count_subcategories ? tep_childs_in_category_count($categories['categories_id']).AAS_HEADING_SUBCATEGORIES_COUNT : '';
    ?>
    <tr class="folder gradeX" id="cat_<?php echo $categories['categories_id']; ?>">
      <td class="nojedit centerAlign checkboxen" ><input type="checkbox" id="category_<?php echo $categories['categories_id']; ?>" disabled="disabled"></td>
      <td class="nojedit centerAlign previewPage" style="padding:0px;"><?php echo '<a href="' . tep_href_link(FILENAME_AAS,'cPath='.$categories['categories_id']) . '">' . tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>'; ?>

        <a target="_blank" class="view_category_class" title="<?php echo AAS_VIEW_CATEGORYS_PAGE; ?>" href="<?php echo tep_aas_link('front','index.php','cPath='.$categories['categories_id'].'&&language='.$languages_selected['code']); ?>"><img src="ext/aas/images/glyphicons_152_new_window_op.png" alt=""></a>
      </td>
<?php if(isset($aasAac['fields_disable_action']['categories_name'][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action']['categories_name'][$_SESSION['admin']['id']]){ ?>
      <td data-column="categories_name" class="nojedit centerAlign"><?php echo $categories['categories_name'].$anwkatwteleia.$bcp.$comma.$bcs;?></td>
<?php }else{ ?>
      <td  data-column="categories_name" class="<?php echo $bool_count_products || $bool_count_subcategories ? 'nojedit' : 'cursor-pointer'; ?> centerAlign"><?php echo $categories['categories_name'].$anwkatwteleia.$bcp.$comma.$bcs;?></td>
<?php }

     foreach($fieldsArray as $key => $value){

      if($value['visible']){

        switch($key){

          case'sort_order':

            if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]){

              echo '<td class="categories_sort_order centerAlign nojedit" data-column="sort_order">'.$categories['sort_order'].'</td>';

            }else{

             echo '<td class="categories_sort_order centerAlign" style="cursor:move" data-column="sort_order">'.$categories['sort_order'].'</td>';

            }

          break;

          case'id':

              echo '<td class="categories_id centerAlign nojedit" data-column="sort_order">'.$categories['categories_id'].'</td>';

          break;
          
          case 'date_added':
          
            if(isset($categories[$key])){
            $dtdateAdded=substr($categories[$key],0,-9);

              echo'<td data-agostatus="0" data-celldata="'.$dtdateAdded.'" data-ago="'.(strtotime($categories[$key])).'" data-massedit="'.$value['massEdit'].'" data-column="'.$key.'" id="'.$key.'_'.$categories['categories_id'].'" style="'.$_table_td_rules[$key]['style'].'" class="nojedit agoCellToggle '.$_table_td_rules[$key]['class'].'" ><span>'.$dtdateAdded.'</span>&nbsp;<input type="hidden" name="edit_date_added_input" value="'.$dtdateAdded.'" class="edit_date_added_input"><img class="edit_date_added" src="ext/aas/images/glyphicons_030_pencil.png" alt="edit"></td>';
            
            }else{
            
              echo'<td data-ago="" data-massedit="'.$value['massEdit'].'" data-column="'.$key.'" id="'.$key.'_'.$categories['categories_id'].'" style="'.$_table_td_rules[$key]['style'].'" class="nojedit '.$_table_td_rules[$key]['class'].'" ></td>';
            
            }
          
          break;


          case 'last_modified':
          
            if(isset($categories[$key])){

              echo'<td data-agostatus="0" data-celldata="'.$categories[$key].'" data-ago="'.(strtotime($categories[$key])).'" data-massedit="'.$value['massEdit'].'" data-column="'.$key.'" id="'.$key.'_'.$categories['categories_id'].'" style="'.$_table_td_rules[$key]['style'].'" class="nojedit agoCellToggle '.$_table_td_rules[$key]['class'].'" ><span>'.$categories[$key].'</span></td>';
            
            }else{
            
              echo'<td data-ago="" data-massedit="'.$value['massEdit'].'" data-column="'.$key.'" id="'.$key.'_'.$categories['categories_id'].'" style="'.$_table_td_rules[$key]['style'].'" class="nojedit '.$_table_td_rules[$key]['class'].'" ></td>';
            
            }
          
          break;

          case'products_image':

            if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]){

              echo'<td style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].'" >'.tep_info_image($categories['categories_image'], $categories['categories_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT).'</td>';

            }else echo'<td style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].'" ><a data-poc="category" data-categoryname="'.$categories['categories_name'].'" class="product_image_link" href="#">'.tep_info_image($categories['categories_image'], $categories['categories_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT).'</a></td>';

          break;
          default:
            echo '<td class="nojedit centerAlign">&nbsp;</td>';
        }

      }

    } ?>
  </tr>
<?php } ?>
  </tbody>
<?php } ?>
  <tbody class="products_tbody">
<?php //FIND PRODUCTS

$products_names_id_array=array();
$status_unavailable_class='';
$counter=0;

foreach($products_array as $products){

//store for attributes cloning select list
$products_names_id_array[$products['products_id']]=$fieldsArray['products_model']['visible']?$products['products_name'].' [ '.$products['products_model'].' ]':$products['products_name'];

if($fieldsArray['products_status']['visible'] && $products['products_status']=='0') $status_unavailable_class=' unavailable'; else $status_unavailable_class='';

if(isset($ptc_array[$products['products_id']])) $isLinked=' tr_linked'; else $isLinked='';

?>
  <tr class="product <?php echo ($counter & 1) && $defaults['colorEachTableRowDifferently'] ? 'even' : 'odd'; echo $status_unavailable_class.' '.$isLinked; ?>" id="pid_<?php echo (int)$products['products_id']; ?>" data-category="<?php echo $products['categories_id']; ?>">
    <td class="nojedit checkboxen centerAlign"><input type="checkbox" name="massCheckbox" id="checkboxMassActions_<?php echo $products['products_id']; ?>" class="checkboxMassActions" value="<?php echo $products['products_id']; ?>"></td>
    <td class="nojedit centerAlign previewPage" style="padding:5px;">
    <?php if($ascDesc==='DESC') echo $totalRows - ( ($counter++)+(($currentPage-1)*$entriesPerPage)); else echo (++$counter)+(($currentPage-1)*$entriesPerPage); ?>    
    <a target="_blank" class="view_product_class" title="<?php echo AAS_VIEW_PRODUCTS_PAGE; ?>" href="<?php echo tep_aas_link('front','product_info.php','products_id='.$products['products_id'].'&&language='.$languages_selected['code']); ?>"><img src="ext/aas/images/glyphicons_152_new_window_op.png" alt=""></a>
    </td>
<?php

      if(isset($aasAac['fields_disable_action']['products_name'][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action']['products_name'][$_SESSION['admin']['id']] ){

          $cursorDefault='cursor:default;';
          $noJedit='nojedit';

      }else{

          $noJedit = '';
          $cursorDefault='';

      }
?>
    <td data-column="products_name" style="<?php echo $_table_td_rules['products_name']['style'].' '.$cursorDefault; ?>" class="<?php echo $noJedit.' '.$_table_td_rules['products_name']['class']; ?>"><?php echo $products['products_name']; ?></td>
<?php foreach($fieldsArray as $key => $value){

      if($value['visible']){

        if(!isset($_table_td_rules[$key])) $_table_td_rules[$key] = array('style'=>'','class'=>'');

        switch($key){

          case'products_date_available':

            if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ){

              echo'<td style="'.$_table_td_rules[$key]['style'].'" class="nojedit '.$_table_td_rules[$key]['class'].'" data-column="'.$key.'" >'.(tep_not_null($products[$key]) ? substr($products[$key],0,-9):'').'</td>';

            }else{

              echo'<td style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].'" data-column="'.$key.'" ><input type="text" style="text-align:center;cursor:pointer;" id="datepicker_'.(int)$products['products_id'].'" class="datepicker lfor" value="'.substr($products[$key],0,-9).'" /><a id="product-available-to-null_'.$products['products_id'].'" class="product-available-to-null" title="'.AAS_TITLE_TEXT_NOT_AVAILABLE_DATE.'" style="'.(tep_not_null($products[$key]) ? '':'visibility:hidden;').'" href="#"><img style="opacity:0.3;height:20px" src="ext/aas/images/remove_white_no_round_1.png" alt="Never Expire"></a></td>';

            }

          break;
          case 'products_description':

            $hasDescription = tep_not_null($products['products_description']) ? true : false;

            echo'<td style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].' '.($hasDescription?'hasDescription_true':'hasDescription_false').'" data-column="'.$key.'" >';

            ?>
            <img data-productname="<?php echo $products['products_name']; ?>" id="trigger_<?php echo (int)$products['products_id']; ?>" title="<?php echo AAS_VIEW_EDIT_DESCRIPTION; ?>" class="<?php echo isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ? '' : 'descriptionbuttonImg'; ?>" src="<?php echo DIR_WS_CATALOG_LANGUAGES . $languages_selected['directory'] . '/images/' . $languages_selected['image']; ?>" alt="<?php echo $languages_selected['name']; ?>" >&nbsp;
            <?php

            if($hasDescription) echo tep_image('ext/aas/images/icn_alert_success.png'); else echo tep_image('ext/aas/images/icn_alert_error.png');
            echo '</td>';

          break;
          case 'products_ordered':

            echo'<td style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].'"  data-column="'.$key.'" >'.$products[$key].'</td>';

          break;

          case'products_url':

            if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ){

              $cursorDefault='cursor:default;';
              $noJedit='nojedit';

            }else{

              $noJedit = $value['cellEditable']?'':'nojedit';
              $cursorDefault='';

            }

            echo'<td style="'.$_table_td_rules[$key]['style'].' '.$cursorDefault.'" class="'.$noJedit.' '.$_table_td_rules[$key]['class'].'"  data-column="'.$key.'" >'.$products[$key].'</td>';

          break;

          case 'products_sort_order':

            echo'<td style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].'"  data-column="'.$key.'" >'.(tep_not_null($products[$key])?$products[$key]:'0').'</td>';
          break;
          case'products_status':

            if($products[$key]){

              $ficon='icn_alert_success.png';
              $statusTitle=AAS_STATUS_ICON_SET_OUT_OF_STOCK;

            }else{

              $ficon='icn_alert_error.png';
              $statusTitle=AAS_STATUS_ICON_SET_IN_STOCK;

            }

            if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ) echo '<td style="'.$_table_td_rules[$key]['style'].'" class="nojedit '.$_table_td_rules[$key]['class'].'"><img src="ext/aas/images/'.$ficon.'" alt="'.$statusTitle.'" title="'.$statusTitle.'"></td>';
            else echo '<td style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].'"  id="products_status_'.(int)$products['products_id'].'" ><a class="radiostockajax" id="status_'.$products['products_id'].'" href="#" ><img src="ext/aas/images/'.$ficon.'" alt="'.$statusTitle.'" title="'.$statusTitle.'"></a></td>';

          break;
          case'attributes':

            if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ){

              if(!isset($products_attributes[$products['products_id']])){

                echo'<td style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].' attributesCellLink_zero" >';
                echo '<span id="attributes_trigger_'.(int)$products['products_id'].'">0</span>';

              }else{

                echo'<td style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].' attributesCellLink_non_zero" >';
                echo '<span id="attributes_trigger_'.(int)$products['products_id'].'">'.$products_attributes[$products['products_id']].'</span>';

              }

              echo '</td>';

            }else{

              if(!isset($products_attributes[$products['products_id']])){

                echo'<td style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].' attributesCell attributesCellLink_zero" >';
                echo'<span data-productname="'.$products['products_name'].'" id="attributes_trigger_'.(int)$products['products_id'].'" class="attributesCellLink">0</span>';

              }else{

                echo'<td style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].' attributesCell  attributesCellLink_non_zero" >';
                echo'<span data-productname="'.$products['products_name'].'" id="attributes_trigger_'.(int)$products['products_id'].'" class="attributesCellLink">'.$products_attributes[$products['products_id']].'</span>';

              }
              echo '</td>';

            }

          break;
          case'tax_class_title':

            if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ){ ?>
            <td style="<?php echo $_table_td_rules[$key]['style']; ?>" class="nojedit" >
            <?php foreach($tax_class_array as $tca){ if($tca['id']==$products['products_tax_class_id']) echo $tca['title'].' '.$tca['tax_rate'].'%'; break; } ?>
            </td>
            <?php }else{  ?>
              <td style="<?php echo $_table_td_rules[$key]['style']; ?>" class="<?php echo $value['cellEditable']?'':'nojedit'; ?>" >
              <select class="selectMenus" id="tcts_<?php echo (int)$products['products_id']; ?>" name="tax_class_title_select_<?php echo (int)$products['products_id']; ?>" onchange="selectMenuChange(this,'products_tax_class_id')" style=""><option value="0" <?php echo $products['products_tax_class_id']=='0' ? 'selected="selected"' : ''; ?>><?php echo AAS_NONE; ?></option><?php foreach($tax_class_array as $tca) echo '<option value="'.$tca['id'].'_'.$tca['tax_rate'].'" '.($tca['id']==$products['products_tax_class_id'] ? 'selected="selected"' : '').' >'.$tca['title'].' '.$tca['tax_rate'].'%</option>'; ?>
              </select></td>
              <?php }

          break;
          case'manufacturers_name':

            if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ){

              echo'<td style="'.$_table_td_rules[$key]['style'].'" class="nojedit '.$_table_td_rules[$key]['class'].'" data-column="'.$key.'" >';
              foreach($manufacturers_array as $man){ if($man['id']==$products['manufacturers_id']) echo $man['name']; }
              echo '</td>';

            }else{ ?>
              <td style="<?php echo $_table_td_rules[$key]['style']; ?>" class="<?php echo $value['cellEditable']?'':'nojedit'; ?>" >
            <select class="selectMenus" id="manu_<?php echo (int)$products['products_id']; ?>" name="manufacturers_select_<?php echo (int)$products['products_id']; ?>" onchange="selectMenuChange(this,'manufacturers_id')" ><option value="0" <?php echo $products['manufacturers_id']=='0' ? 'selected="selected"' : ''; ?>><?php echo AAS_NONE; ?></option><?php foreach($manufacturers_array as $man) echo '<option value="'.$man['id'].'" '.($man['id']==$products['manufacturers_id'] ? 'selected="selected"' : '').' >'.$man['name'].'</option>'; ?>
            </select></td>
            <?php
            }

          break;
          case'products_image':

            if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ){

            echo'<td style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].'" >'.tep_info_image($products[$key], $products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT).'</td>'.PHP_EOL;

            }else echo'<td style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].'" ><a data-poc="product" data-productname="'.$products['products_name'].'" class="product_image_link" href="#">'.tep_info_image($products[$key], $products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT).'</a></td>'.PHP_EOL;

          break;
          case'products_price_gross':

            $taxRate=tep_get_tax_rate($products['products_tax_class_id']);

            if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ){

              $cursorDefault='cursor:default;';
              $noJedit='nojedit';

            }else{
              $noJedit = $value['cellEditable']?'':'nojedit';
              $cursorDefault='';
            }

            echo'<td data-price-net="'.$products['products_price'].'" data-tax-rate="'.$taxRate.'" data-column="'.$key.'" id="'.$key.'_'.$products['products_id'].'" style="'.$_table_td_rules[$key]['style'].' '.$cursorDefault.'" class="'.$noJedit.' '.$_table_td_rules[$key]['class'].'" >'.tep_get_price_with_tax($products['products_price'],$taxRate,$currency_symbols[0]).'</td>';

          break;

          case'products_order_status':

            echo'<td id="'.$key.'_'.$products['products_id'].'" style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].'" >';
            $numItems = count($orders_status_array);
            foreach($orders_status_array as $key_osa => $osa) echo $osa.': '.(isset($products_quantity_by_orders[$products['products_id']][$key_osa])?$products_quantity_by_orders[$products['products_id']][$key_osa]:'0').($key_osa<$numItems? ' / ': '');
            echo '</td>';

          break;
          case'special':

          if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ){

              echo'<td style="'.$_table_td_rules[$key]['style'].'" class="nojedit '.$_table_td_rules[$key]['class'].'" data-column="'.$key.'" >'.AAS_TEXT_EDIT_DISABLED.'</td>';

          }else{

            $td_bg='';
            if(isset($specials_products[$products['products_id']])){

              if($specials_products[$products['products_id']]['status']=='0') $td_bg='unavailable';

            }

            echo'<td data-column="'.$key.'" id="'.$key.'_'.$products['products_id'].'" style="'.$_table_td_rules[$key]['style'].'" class="'.($value['cellEditable']?'':'nojedit').' '.$_table_td_rules[$key]['class'].' '.$td_bg.'" >';
if(isset($specials_products[$products['products_id']])){

              if($specials_products[$products['products_id']]['status']=='1'){

                $ficon='icn_alert_success.png';
                $statusTitle='Set Inactive';

              }else{

                $ficon='icn_alert_error.png';
                $statusTitle='Set Active';

              }

            ?>
              <span class="oldPrice"><?php echo $currencies->format($specials_products[$products['products_id']]['old_price']); ?></span>
              <span class="specialPrice"><?php echo $currencies->format($specials_products[$products['products_id']]['new_price']); ?></span>
              <?php if($fieldsArray['products_price_gross']['visible']){
                $taxRate=tep_get_tax_rate($products['products_tax_class_id']);
                echo '<span class="specialPriceGross">'.$currencies->format(tep_get_price_with_tax($specials_products[$products['products_id']]['new_price'],$taxRate,$currency_symbols[0])).' (Gross)</span>';
              }
              ?>

              ,&nbsp;<?php echo AAS_SPECIALS_TEXT_STATUS; ?><a class="radiostockajax-special" id="special-status_<?php echo $specials_products[$products['products_id']]['specials_id']; ?>" href="#" ><img src="ext/aas/images/<?php echo $ficon; ?>" alt="<?php echo $statusTitle; ?>" title="<?php echo $statusTitle; ?>"></a>
              ,&nbsp;<?php echo AAS_SPECIALS_TEXT_EXPIRES_AT; ?><input type="text" style="text-align:center" class="lfor specials_expires_at" id="specials_expires_at_<?php echo $specials_products[$products['products_id']]['specials_id']; ?>" value="<?php echo substr($specials_products[$products['products_id']]['expires_date'],0,-9); ?>" />

              <a id="specials-unexpire_<?php echo $specials_products[$products['products_id']]['specials_id']; ?>" class="specials-unexpire" title="<?php echo AAS_SPECIALS_TEXT_NEVER_EXPIRE; ?>" style="<?php echo tep_not_null($specials_products[$products['products_id']]['expires_date']) ? '':'visibility:hidden;'; ?>" href="#"><img style="opacity:0.3;height:20px" src="ext/aas/images/remove_white_no_round_1.png" alt="never expire"></a>

              <?php if($defaults['enableSpecials']){ ?><a href="#" id="edit-selected-product-as-special_<?php echo $specials_products[$products['products_id']]['specials_id']; ?>" class="edit-selected-product-as-special" title="<?php echo AAS_SPECIALS_TEXT_EDIT_SPECIAL; ?>"><img style="opacity:0.6;height:15px" src="ext/aas/images/glyphicons_030_pencil.png" alt="Edit"></a><?php } ?>
            <?php }else{ ?>

              <?php echo AAS_SPECIALS_TEXT_NOT_A_SPECIAL_YET; ?>
              <?php if($defaults['enableSpecials']){ ?><button class="applyButton add-selected-product-as-special" id="add_specials_products_id_<?php echo $products['products_id']; ?>" ><?php echo AAS_SPECIALS_TEXT_ADD; ?> &nbsp;<img style="opacity:0.3;height:15px" src="ext/aas/images/glyphicons_190_circle_plus.png" alt="Add"></button><?php } ?>

            <?php
            }

            echo '</td>';
          }
          break;
          case'products_linked':

          if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ){

              echo'<td style="'.$_table_td_rules[$key]['style'].'" class="nojedit '.$_table_td_rules[$key]['class'].'" data-column="'.$key.'" >'.AAS_TEXT_EDIT_DISABLED.'</td>';

          }else{

          ?>
            <td id="products_linked_<?php echo $products['products_id']; ?>" style="<?php echo $_table_td_rules[$key]['style']; ?> text-align:left" class="<?php echo $value['cellEditable']?'':'nojedit'; ?>" >
              <?php $caid = tep_not_null($categoryId) ? (int)$categoryId : 0;

              //if(isset($protoca_array[$products['products_id']][1]['cid']) && (int)$protoca_array[$products['products_id']][1]['cid']==$caid){
              if(!isset($ptc_array[$products['products_id']])){

                if(count($protoca_array[$products['products_id']])>1){

                  echo AAS_TEXT_LINKED_WITH; ?><br /><br />
                  <?php foreach($protoca_array[$products['products_id']] as $kpropo => $propro ){
                    if($propro['linked']=='0') continue;
                    $tjohn=array_reverse(tep_get_category_parents($propro['cid']));
                    array_pop($tjohn);
                    echo '<div id="'.$propro['cid'].'_'.$products['products_id'].'">';
                    echo count($tjohn)>0 ? implode('&nbsp;&raquo;&nbsp;',$tjohn) : AAS_TEXT_TOP;
                    echo '&nbsp;&raquo;&nbsp;<a href="'.tep_href_link(FILENAME_AAS, 'cPath='.$propro['cid']).'">'.$propro['cname'].'</a>&nbsp;<button data-cid="'.$propro['cid'].'" data-pid="'.$products['products_id'].'" class="applyButton removeLinkedProductFromParent" style="font-size:10px;margin:2px 0">Unlink</button></div>';

                  }

                }else echo AAS_TEXT_NOT_LINKED;

              }else{ echo '<strong><i>'.AAS_TEXT_LINKED_PRODUCT.'</i></strong>&nbsp;<button data-cid="'.$caid.'" data-pid="'.$products['products_id'].'" class="applyButton removeLinkedProduct">'.AAS_TEXT_REMOVE_LINKED_PRODUCT.'</button>';

              if(isset($protoca_array[$products['products_id']])){

                foreach($protoca_array[$products['products_id']] as $kpropo => $propro ){
                echo '<br>';
                    if($propro['linked']=='0'){

                      echo AAS_TEXT_ORIGINAL_PRODUCT_AT;
                      $tjohn=array_reverse(tep_get_category_parents($propro['cid']));
                      array_pop($tjohn);
                      echo count($tjohn)>0 ? implode('&nbsp;&raquo;&nbsp;',$tjohn) : AAS_TEXT_TOP;
                      echo '&nbsp;&raquo;&nbsp;<a href="'.tep_href_link(FILENAME_AAS, 'cPath='.$propro['cid']).'">'.$propro['cname'].'</a><br><br>';

                    }else{

                      if((int)$propro['cid']==$caid) continue;
                      echo AAS_TEXT_ALSO_LINKED_WITH;
                      $tjohn=array_reverse(tep_get_category_parents($propro['cid']));
                      array_pop($tjohn);
                      echo count($tjohn)>0 ? implode('&nbsp;&raquo;&nbsp;',$tjohn) : AAS_TEXT_TOP;
                      echo '&nbsp;&raquo;&nbsp;<a href="'.tep_href_link(FILENAME_AAS, 'cPath='.$propro['cid']).'">'.$propro['cname'].'</a><br>';

                    }
                }
              }

            }
            ?>
            </td>
          <?php
          }
          break;

          case 'sort_order':

            if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ){

              echo'<td class="nojedit products_sort_order '.$_table_td_rules[$key]['class'].'" data-column="products_'.$key.'" >'.(isset($products['products_sort_order']) ? $products['products_sort_order'] : '---').'</td>';

            }else{

              echo'<td style="'.(isset($products['products_sort_order']) && ( !isset($search) || $search=="" ) ? $_table_td_rules[$key]['style'] : '').'" class="'.($value['cellEditable'] && isset($products['products_sort_order']) ?'':'nojedit').' products_sort_order '.$_table_td_rules[$key]['class'].'" data-column="products_'.$key.'" >'.(isset($products['products_sort_order']) ? $products['products_sort_order'] : '---').'</td>';

            }

          break;

          case 'id':

              echo'<td class="nojedit products_id '.$_table_td_rules[$key]['class'].'" data-column="products_'.$key.'" >'.$products['products_id'].'</td>';

          break;
          
          case 'date_added':
          
            if(isset($products['products_'.$key])){
            
            $dtdateAdded=substr($products['products_'.$key],0,-9);

              echo'<td data-agostatus="0" data-celldata="'.$dtdateAdded.'" data-ago="'.(strtotime($products['products_'.$key])).'" data-massedit="'.$value['massEdit'].'" data-column="'.$key.'" id="'.$key.'_'.$products['products_id'].'" style="'.$_table_td_rules[$key]['style'].'" class="nojedit agoCellToggle '.$_table_td_rules[$key]['class'].'" ><span>'.$dtdateAdded.'</span>';
  
              if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ) ;
              else echo '&nbsp;<input type="hidden" name="edit_date_added_input" value="'.$dtdateAdded.'" class="edit_date_added_input"><img class="edit_date_added" src="ext/aas/images/glyphicons_030_pencil.png" alt="edit">';

              echo '</td>';
            
            }else{
            
              echo'<td data-ago="" data-massedit="'.$value['massEdit'].'" data-column="'.$key.'" id="'.$key.'_'.$categories['categories_id'].'" style="'.$_table_td_rules[$key]['style'].'" class="nojedit '.$_table_td_rules[$key]['class'].'" ></td>';
            
            }
            
          break;
          
          case 'last_modified':
          
            if(isset($products['products_'.$key])){
              
             echo'<td data-agostatus="0" data-celldata="'.$products['products_'.$key].'" data-ago="'.(strtotime($products['products_'.$key])).'" data-massedit="'.$value['massEdit'].'" data-column="'.$key.'" id="'.$key.'_'.$products['products_id'].'" style="'.$_table_td_rules[$key]['style'].'" class="nojedit agoCellToggle '.$_table_td_rules[$key]['class'].'" ><span>'.$products['products_'.$key].'</span></td>';
            
            }else{
            
              echo'<td data-ago="" data-massedit="'.$value['massEdit'].'" data-column="'.$key.'" id="'.$key.'_'.$products['products_id'].'" style="'.$_table_td_rules[$key]['style'].'" class="nojedit '.$_table_td_rules[$key]['class'].'" ></td>';
            
            }
          
          break;
          
          default:

          if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']] ){

            $cursorDefault='cursor:default;';
            $noJedit='nojedit';

          }else{

            $noJedit = $value['cellEditable']?'':'nojedit';
            $cursorDefault='';
          }

            echo'<td data-massedit="'.$value['massEdit'].'" data-column="'.$key.'" id="'.$key.'_'.$products['products_id'].'" style="'.$_table_td_rules[$key]['style'].' '.$cursorDefault.'" class="'.$noJedit.' '.$_table_td_rules[$key]['class'].'" >'.(isset($products[$key]) ? $products[$key] : '').'</td>';

        }

      }
    }?>
</tr>
<?php } ?>

   </tbody>
</table>
</div>

<?php if($defaults['inTableSearchPosition']['visible'] && $defaults['inTableSearchPosition']['y']=='bottom'){ ?><div class="inTableSearch-wrapper inTableSearch-wrapper-<?php echo isset($defaults['inTableSearchPosition']['x']) ? $defaults['inTableSearchPosition']['x'] : 'right'; ?>"><input type="text" id="search" placeholder="In Table Search" class="inTableSearch inTableSearch-top" /></div><?php } ?>
<?php if($defaults['paginationPosition']=='bottom'){ ?>
<div id="pagination">
  <?php echo $pagination->draw(); ?>
  <?php if($entries>0){ ?>
  <div class="pagination-form-wrapper">
    <form name="itemsPerRowSelection-form" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
      <span style="padding-top:10px;"><?php echo AAS_PAGINATION_MAX_PRODUCTS; ?></span>
      <select class="entriesPerPage" name="entriesPerPage" onchange="submit();">
        <?php foreach($perPageArray as $ppa) echo '<option '.($ppa==$entriesPerPage ? 'selected="selected"' : '').' value="'.$ppa.'">'.$ppa.'</option>'; ?>
      </select><?php if($entries>0){ ?><span style="padding-top:10px;"><?php echo AAS_PAGINATION_DISPLAYING; ?><strong><?php echo $entries; ?></strong><?php echo AAS_PAGINATION_FROM; ?><strong><?php echo $totalRows; ?></strong><?php echo AAS_PAGINATION_PRODUCTS; ?></span><?php } ?>
    </form>
  </div>
<?php } ?>
</div>
<?php } ?>
<?php if($defaults['displayCountProducts'] && $categories_entries>0){ ?>
<table class="displayCountProducts" id="tbl-count">
  <tr>
    <td class="centerAlign">
      <div id="radiocount" class="radiocount">
        <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
          <?php if($bool_count_products) $checked1='checked="checked"'; else $checked1=''; ?>
            <input type="checkbox" name="countcheck[]" value="count_products" <?php echo $checked1; ?> id="check_count_products" /><label for="check_count_products"><?php echo AAS_COUNT_PRODUCTS; ?></label>
            <?php if($bool_count_subcategories) $checked2='checked="checked"'; else $checked2=''; ?>
            <input type="checkbox" name="countcheck[]" value="count_subcategories" <?php echo $checked2; ?> id="check_count_subcategories" /><label for="check_count_subcategories"><?php echo AAS_COUNT_SUBCATEGORIES; ?></label>
          <input type="submit" style="color:#005;" class="applyButton" value="<?php echo AAS_APPLY; ?>"/>
          <input type="hidden" name="ccheck" value="1" />
        </form>
      </div>
    </td>
  </tr>
</table>
<?php } ?>
<?php if($defaults['displayDatetime-info']){ ?>
<div id="datetime-information">
  <span id="toolbox-datetime"><?php echo tep_formatDate(time()); ?></span><?php echo AAS_TEXT_STAYED_IN_THIS_PAGE; ?><span id="toolbox-time-stated"></span>
  <script type="text/javascript" src="ext/aas/js/timer.js"></script>
  <script>
    date_time('toolbox-datetime','local');
    timerUp('<?php echo $time; ?>','<?php echo $time; ?>',function(tm){ document.getElementById('toolbox-time-stated').innerHTML=tm;});
  </script>
</div>
<?php } ?>
<?php if($defaults['displayBottomInformation']){ ?>
<div id="copywrite"><a target="_blank" href="http://www.alternative-administration-system.com"><?php echo AAS_TITLE; ?></a> <?php echo AAS_TEXT_VERSION; echo AAS_VERSION; ?><br><br> <?php echo AAS_TEXT_CREATED_BY; ?><a target="_blank" href="http://www.johnbarounis.com/">John Barounis</a></div>
<?php } ?>

</section>

<?php if($defaults['displayFieldsPanel']){ ?>
<section id="selectedFields">
  <div id="panelToggle" data-title="<?php echo AAS_BUTTON_TOOLTIP_COLUMNS; ?>" ><img title="" src="ext/aas/images/glyphicons_114_list.png" alt=""></div>
  <div id="selectedFields-inwrapper">

    <div class="title"><?php echo AAS_TEXT_COLUMNS; ?>&nbsp;<img class="toolBox-help-icon" id="toolBox_help_icon_sortable_columns" src="ext/aas/images/glyphicons_194_circle_question_mark.png" alt="">
      <div class="info-explain"><?php echo AAS_DIALOG_TEXT_COLUMNS_PANEL_INFORMATION; ?></div>
    </div>

    <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" id="format-form" method="post">
      <ul id="format">
<?php foreach($fieldsArray as $key => $value){

        $checked=$value['visible'] ? 'checked="checked"' : '';

        if($value['lockVisibility']) $checked.=' disabled="disabled" '; ?>
        <li id="<?php echo $key; ?>" class="wr">
          &nbsp;<input type="checkbox" name="columncheck[<?php echo $key; ?>]" value="<?php echo $key; ?>" <?php echo $checked; ?> id="check<?php echo $key; ?>" /><label for="check<?php echo $key; ?>"><?php echo $value['theadText']; ?></label>
<?php if($value['lockVisibility'] && $value['visible']){ ?>
          <input type="hidden" name="columncheck[<?php echo $key; ?>]" value="<?php echo $key; ?>" />
<?php } ?>
          <input type="hidden" name="columncheckAll[<?php echo $key; ?>]" value="<?php echo $key; ?>" />
        </li>
<?php } ?>
      </ul>
      <input type="submit" style="color:#005;" class="apply-button" value="<?php echo AAS_APPLY; ?>"/>
      <input type="hidden" name="check" value="1" />
      <input type="hidden" name="sortFields" value="0" />
    </form>
  </div>
</section>
<?php } ?>

<?php if($defaults['enableTempProductsList']){?>
<section id="leftSidePanel">
  <div id="leftSidePanel-toggle" data-title="<?php echo AAS_TEXT_TEMP_SAVED_PRODUCTS; ?>" ><img title="" src="ext/aas/images/glyphicons_114_list.png" alt=""></div>
  <div id="leftSidePanel-inwrapper">
    <?php include 'ext/aas/plugins/temp_list/index.php'; ?>
  </div>
</section>
<?php } ?>

<?php if($defaults['enableClocks']) include 'ext/aas/plugins/clocks/index.php'; ?>

<?php if($defaults['enableToolBox'] && count($products_names_id_array)>0){ ?>
<section id="toolBox">
  <div id="toolBox-inwrapper">

    <fieldset>

      <select id="move-copy-link-selectMenu" style="font-size:12px">
        <option value="1"><?php echo AAS_TEXT_MOVE_SELECTED_PRODUCTS_IN; ?></option>
        <option value="2"><?php echo AAS_TEXT_COPY_SELECTED_PRODUCTS_IN; ?></option>
        <option value="3"><?php echo AAS_TEXT_LINK_SELECTED_PRODUCTS_IN; ?></option>
      </select>

      <select id="categories-list-selectMenu" style="font-size:12px">
        <?php echo $cats_fields; ?>
      </select>

      <select id="after-action-list-selectMenu" style="font-size:12px">
        <option value="1"><?php echo AAS_TEXT_AND_STAY_HERE; ?></option>
        <option value="2"><?php echo AAS_TEXT_AND_RELOAD_THIS_PAGE; ?></option>
        <option value="3"><?php echo AAS_TEXT_AND_GO_TO_SELECTED_CATEGORY; ?></option>
      </select>

      <button id="button-mpm" class="applyButton" onclick="toolBoxAction()"><?php echo AAS_TEXT_GO; ?></button>
      <img class="toolBox-help-icon" id="toolBox_help_icon_mass_products_manager" src="ext/aas/images/glyphicons_194_circle_question_mark.png" alt="" />
      <div class="info-explain"><?php echo AAS_TEXT_MASS_PRODUCTS_ACTIONS_HELP; ?></div>

    </fieldset>
    <?php if(isset($aasAac['fields_disable_action'][$key][$_SESSION['admin']['id']]) && $aasAac['fields_disable_action']['attributes'][$_SESSION['admin']['id']] ){ ?>
    <div class="clear"></div>
    
    
    <?php }else{ ?>
    <div class="clear"></div>
    <fieldset>

      <select id="toolbox-attributes-select-1" style="font-size:12px" onchange="tas(this)">
        <option value="1"><?php echo AAS_TEXT_COPY_ATTRIBUTES_FROM; ?></option>
        <option value="2"><?php echo AAS_TEXT_DELETE_ATTRIBUTES; ?></option>
      </select>

      <select id="toolbox-attributes-select-2" style="font-size:12px">
        <?php foreach($products_names_id_array as $key => $value) echo '<option value="'.$key.'">'.$value.'</option>'; ?>
      </select>

      <select id="toolbox-attributes-select-3" style="font-size:12px" onchange="clsa_display_or_not(this)">
        <option value="1"><?php echo AAS_TEXT_TO_SELECTED_PRODUCTS; ?></option>
        <option value="2"><?php echo AAS_TEXT_TO_SELECTED_PRODUCTS_FROM_TEMP_LIST; ?></option>
        <option value="3"><?php echo AAS_TEXT_TO_ALL_PRODUCTS_IN; ?></option>
      </select>

      <select id="toolbox-attributes-select-6" style="font-size:12px;display:none;" onchange="clsa_display_or_not(this)">
        <option value="1"><?php echo AAS_TEXT_FROM_SELECTED_PRODUCTS; ?></option>
        <option value="2"><?php echo AAS_TEXT_FROM_SELECTED_PRODUCTS_FROM_TEMP_LIST; ?></option>
        <option value="3"><?php echo AAS_TEXT_FROM_ALL_PRODUCTS_IN; ?></option>
      </select>

      <select id="toolbox-attributes-select-4" style="font-size:12px;display:none;">
        <?php echo $cats_fields; ?>
      </select>

      <select id="toolbox-attributes-select-7" style="font-size:12px;display:none;">
        <option value="1"><?php echo AAS_TEXT_RECURSIVELY; ?></option>
        <option value="2"><?php echo AAS_TEXT_NON_RECURSIVELY; ?></option>
      </select>

      <select id="toolbox-attributes-select-5" style="font-size:12px" >
        <option value="1"><?php echo AAS_TEXT_DELETE_EXISTING_ATTRIBUTES; ?></option>
        <option value="2"><?php echo AAS_TEXT_ALLOW_DUPLICATE_ATTRIBUTES; ?></option>
        <option value="3"><?php echo AAS_TEXT_DISALLOW_DUPLICATE_ATTRIBUTES; ?></option>
      </select>

      <button class="applyButton" onclick="toolBoxAttributesAction()"><?php echo AAS_TEXT_GO; ?></button>

      <img class="toolBox-help-icon" id="toolBox_help_icon_attributes" src="ext/aas/images/glyphicons_194_circle_question_mark.png" alt="" />
      <div class="info-explain"><?php echo AAS_TEXT_ATTRIBUTES_ACTIONS_HELP; ?></div>

    </fieldset>
    <?php } ?>
    <div class="clear"></div>
  </div>
</section>
<?php } ?>

<div style="margin:25px;">&nbsp;</div>
<section id="floatBottomBar">
<?php if (sizeof($languages_array) > 1 && $defaults['displayLanguageSelection']) { ?>
  <div class="floatBottomBar-item">
    <?php echo tep_draw_form('adminlanguage1', FILENAME_AAS, 'cPath='.$cPath_back, 'get') . tep_draw_pull_down_menu('language', $languages_array, $languages_selected['code'], 'onchange="this.form.submit();"') . tep_draw_hidden_field('cPath', $cPath_back). '</form>'; ?>

  </div>
<?php } ?>

  <div class="floatBottomBar-item">
    <?php echo tep_draw_form('goto', FILENAME_AAS, '', 'get'); ?>
      <select name="cPath" onchange="this.form.submit();">
        <?php echo $cats_fields; ?>
      </select>        
    </form>
  </div>

<?php if($defaults['displayByStatusSelection']){ ?>
  <div id="radio" class="floatBottomBar-item">
    <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
      <select name="radio" onchange="submit();" <?php echo $show_products_by_status==0 ? 'class="inactiveClassSelect"' : ''; ?>>
        <option value="1" <?php echo $show_products_by_status==1 ? 'selected="selected"' :''; ?> ><?php echo AAS_ACTIVE_PRODUCTS; ?></option>
        <option value="0" <?php echo $show_products_by_status==0 ? 'selected="selected"' :''; ?>><?php echo AAS_INACTIVE_PRODUCTS; ?></option>
        <option value="2" <?php echo $show_products_by_status==2 ? 'selected="selected"' :''; ?> ><?php echo AAS_ALL_PRODUCTS; ?></option>
      </select>
    </form>
  </div>
<?php } ?>

<?php if($defaults['displayOrderBySelection'] && !isset($_GET['search'])){ ?>
  <div class="floatBottomBar-item">
    <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
      <select name="orderBy" onchange="submit();">
        <?php foreach($orderByArray as $key => $value) echo '<option '.($orderBy==$key ? 'selected="selected"' : '' ).' value="'.$key.'">'.$value.'</option>'; ?>
      </select>
    </form>
  </div>
<?php } ?>

<?php if($defaults['displayAscDescSelection'] && !isset($_GET['search'])){ ?>
  <div class="floatBottomBar-item">
    <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
      <select name="ascDesc" onchange="submit();">
        <?php foreach($ascDescArray as $key => $value) echo '<option '.($ascDesc==$key ? 'selected="selected"' : '' ).' value="'.$key.'">'.$value.'</option>'; ?>
      </select>
    </form>
  </div>
<?php } ?>

<?php if($defaults['displayCategoriesSelection']){ ?>
  <div class="floatBottomBar-item">
    <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
      <select name="displayCategories" onchange="submit();" <?php echo $displayCategories==0 ? 'class="inactiveClassSelect"' : ''; ?>>
        <option value="1" <?php echo $displayCategories==1 ? 'selected="selected"' :''; ?> ><?php echo $displayCategories==1 ? AAS_CATEGORIES_VISIBLE : AAS_DISPLAY_CATEGORIES; ?></option>
        <option value="0" <?php echo $displayCategories==0 ? 'selected="selected"' :''; ?>><?php echo $displayCategories==0 ? AAS_CATEGORIES_HIDDEN : AAS_HIDE_CATEGORIES; ?></option>
      </select>
    </form>
  </div>
<?php } ?>

<?php if($defaults['enableToolBox'] && count($products_names_id_array)>0){ ?>
  <div id="toolBox-toggle" data-title="<?php echo AAS_BUTTON_TOOLTIP_TOGGLE_TOOLBOX; ?>"><img src="ext/aas/images/glyphicons_019_cogwheel.png" alt=""></div>
<?php } ?>

</section>
<?php

include 'ext/aas/plugins/products_description/index.php';
include 'ext/aas/plugins/import/index.php';

if($fieldsArray['products_image']['visible']){

include 'ext/aas/plugins/product_images/index.php';
include 'ext/aas/plugins/categories_images/index.php';

}

if($defaults['enableDonations']) include 'ext/aas/plugins/donations/index.php';
if($defaults['enableContactMe']) include 'ext/aas/plugins/contactme/index.php';
if($defaults['enableSpecials']) include 'ext/aas/plugins/specials/index.php';
if($defaults['enableCalendar']) include 'ext/aas/plugins/calendar/index.php';
if($defaults['enableAttributesManager']) include 'ext/aas/plugins/attributes_manager/index.php';
if($defaults['enableOnlineUsers']) include 'ext/aas/plugins/online_users/index.php';

if($modules_count>0){ foreach($modules as $key => $module) include 'ext/aas_modules/'.$key.'/index.php'; }

?>
<div id="tooltip"><span class="arrow"></span><div></div></div>
<div id="previewPageVelakiWrapper">
  <div class="previewPageVelakiWrapper-tools">
    <a target="_blank" class="removeIframePreviewLink" href="#" title="<?php echo AAS_TEXT_PAGE_PREVIEW_CLOSE; ?>"><img class="removeIframePreview" src="ext/aas/images/glyphicons_197_remove_white.png" alt=""></a>
    <a target="_blank" class="refreshIframePreviewLink" href="#" title="<?php echo AAS_TEXT_PAGE_PREVIEW_REFRESH; ?>"><img class="refreshIframePreviewLink" src="ext/aas/images/glyphicons_081_refresh.png" alt=""></a>
    <a target="_blank" class="openInNewWindowLink" href="#" title="<?php echo AAS_TEXT_PAGE_PREVIEW_OPEN; ?>"><img class="openInNewWindow" src="ext/aas/images/glyphicons_152_new_window.png" alt=""></a>
  </div>
  <iframe src="about:blank" id="previewPageVelaki"></iframe>
</div>
<?php include 'ext/aas/dialogs.php'; ?>
<script type="text/javascript" src="ext/aas/js/jquery.touchwipe.1.1.1.js"></script>
<script>
$(function(){
  $("#toolBox").touchwipe({
     //wipeLeft: function(){ },
     //wipeRight: function(){ },
     wipeUp: function(y){ $("#toolBox").css({top:y,height:'100%'}); },
     wipeDown: function(y){ $("#toolBox").css({top:y,height:'100%'});},
     min_move_x: 20,
     min_move_y: 0,
     preventDefaultEvents: true
  });
});
</script>
</body>
</html>
<?php require DIR_WS_INCLUDES . 'application_bottom.php'; ?>
