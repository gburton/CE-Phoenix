<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $directory = DIR_FS_CATALOG . 'includes/actions/';

  require('includes/template_top.php');
?>

  <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
  
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <th><?php echo TABLE_HEADING_FILE; ?></th>
          <th><?php echo TABLE_HEADING_ACTION; ?></th>
          <th><?php echo TABLE_HEADING_CLASS; ?></th>
          <th><?php echo TABLE_HEADING_METHOD; ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $files = array_diff(scandir($directory), ['.', '..']);
        
        foreach ($files as $file) {
          $code = substr($file, 0, strrpos($file, '.'));
          $class = 'osC_Actions_' . $code;
          
          if ( !class_exists($class) ) {
            include($directory . '/' . $file);
          }
          
          $obj = new $class();
          
          foreach (get_class_methods($obj) as $method) {
            ?>
            <tr>
              <td><?php echo $file; ?></td>
              <td><?php echo $code; ?></td>
              <td><?php echo $class; ?></td>
              <td><?php echo $method; ?></td>
            </tr>
          <?php
          }
        }
        ?>
      </tbody>
    </table>
  </div>
  
  <p><?php echo TEXT_ACTIONS_DIRECTORY . ' ' . DIR_FS_CATALOG . 'includes/actions/'; ?></p>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
