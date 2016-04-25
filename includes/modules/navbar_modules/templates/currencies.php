<li class="dropdown"> 
  <a class="dropdown-toggle" data-toggle="dropdown" href="#">
  <?php echo sprintf(MODULE_NAVBAR_CURRENCIES_SELECTED_CURRENCY, $currency); ?>
  </a>
  <?php
  if (isset($currencies) && is_object($currencies) && (count($currencies->currencies) > 1)) {
    ?>
    <ul class="dropdown-menu">
      <?php                
      reset($currencies->currencies);
      $currencies_array = array();
      while (list($key, $value) = each($currencies->currencies)) {
        $currencies_array[] = array('id' => $key, 'text' => $value['title']);
        echo '<li><a href="' . tep_href_link($PHP_SELF, tep_get_all_get_params(array('language', 'currency')) . 'currency=' . $key, $request_type) . '">' . $value['title'] . '</a></li>';
      }
      ?>
    </ul>
    <?php
  }
  ?>
</li>         