<nav class="navbar <?= $navbar_style ?> cm-navbar">
  <div class="<?= BOOTSTRAP_CONTAINER ?>">
    <?php
    if ($oscTemplate->hasBlocks('navbar_modules_home')) {
      echo '<div class="navbar-header">' . PHP_EOL;
        echo $oscTemplate->getBlocks('navbar_modules_home');
      echo '</div>' . PHP_EOL;
    }
    ?>
    <div class="collapse navbar-collapse" id="collapseCoreNav">
      <?php
      if ($oscTemplate->hasBlocks('navbar_modules_left')) {
        echo '<ul class="navbar-nav mr-auto">' . PHP_EOL;
          echo $oscTemplate->getBlocks('navbar_modules_left');
        echo '</ul>' . PHP_EOL;
      }
      if ($oscTemplate->hasBlocks('navbar_modules_center')) {
        echo '<ul class="navbar-nav mx-auto">' . PHP_EOL;
          echo $oscTemplate->getBlocks('navbar_modules_center');
        echo '</ul>' . PHP_EOL;
      }
      if ($oscTemplate->hasBlocks('navbar_modules_right')) {
        echo '<ul class="navbar-nav ml-auto">' . PHP_EOL;
          echo $oscTemplate->getBlocks('navbar_modules_right');
        echo '</ul>' . PHP_EOL;
      }
      ?>
    </div>
  </div>
</nav>

<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/
?>
