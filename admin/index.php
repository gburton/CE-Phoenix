<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $languages = tep_get_languages();
  $languages_array = array();
  $languages_selected = DEFAULT_LANGUAGE;
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $languages_array[] = array('id' => $languages[$i]['code'],
                               'text' => $languages[$i]['name']);
    if ($languages[$i]['directory'] == $language) {
      $languages_selected = $languages[$i]['code'];
    }
  }

  require('includes/template_top.php');
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4"><?php echo STORE_NAME; ?></h1>
    </div>
    <?php
    if (sizeof($languages_array) > 1) {
      ?>
      <div class="col-sm-4 text-right"><?php echo tep_draw_form('adminlanguage', 'index.php', '', 'get') . tep_draw_pull_down_menu('language', $languages_array, $languages_selected, 'onchange="this.form.submit();"') . tep_hide_session_id() . '</form>'; ?></div>
      <?php
      }
    ?>
  </div>
  
  <div class="row">
    <?php
    if ( defined('MODULE_ADMIN_DASHBOARD_INSTALLED') && tep_not_null(MODULE_ADMIN_DASHBOARD_INSTALLED) ) {
      $adm_array = explode(';', MODULE_ADMIN_DASHBOARD_INSTALLED);

      for ( $i=0, $n=sizeof($adm_array); $i<$n; $i++ ) {
        $adm = $adm_array[$i];

        $class = substr($adm, 0, strrpos($adm, '.'));

        if ( !class_exists($class) ) {
          include('includes/languages/' . $language . '/modules/dashboard/' . $adm);
          include('includes/modules/dashboard/' . $class . '.php');
        }

        $ad = new $class();

        if ( $ad->isEnabled() ) {
          $module_width = $ad->content_width ?? 6;
          
          echo '<div class="col-md-' . $module_width . '">';
            echo $ad->getOutput();
          echo '</div>' . PHP_EOL;
        }
      }
    }
    ?>
  </div>
  
<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
