<div class="col-sm-<?php echo $content_width; ?> cm-gdpr-contact-addresses">
  <table class="table">
    <thead class="thead-dark">
      <tr>
        <th colspan="2"><?php echo MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_PUBLIC_TITLE; ?></th>
      </tr>
    </thead>
    </tbody>
      <tr>
        <td class="w-50"><p class="text-center"><?php echo sprintf(MODULE_CONTENT_GDPR_CONTACT_ADDRESSES_NUM_ADDRESSES, $port_my_data['YOU']['CONTACT']['ADDRESS']['OTHER']['COUNT']); ?></p></td>
        <td>
          <ul class="list-group">
            <?php
            foreach ($port_my_data['YOU']['CONTACT']['ADDRESS']['OTHER']['LIST'] as $k => $v) {
              echo '<li class="list-group-item">' . $v['ADDRESS'] . '</li>';
            }
            ?>
          </ul>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/
?>