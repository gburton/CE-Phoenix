<div class="card mb-2 <?= $box['classes'] ?>"<?= $box['attributes'] ?? '' ?>>
  <div class="card-header"><?= $box['title'] ?></div>

  <?php include $GLOBALS['oscTemplate']->map_to_template(...$box['parameters']) ?>
</div>

<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
?>
