<li class="nav-item dropdown nb-currencies">  
  <a class="nav-link dropdown-toggle" href="#" id="navDropdownCurrencies" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?php echo sprintf(MODULE_NAVBAR_CURRENCIES_SELECTED_CURRENCY, $currency); ?>
  </a>
  <div class="dropdown-menu<?php echo $menu_align; ?>" aria-labelledby="navDropdownCurrencies">
    <?php                
    $currencies_array = array();
    foreach($currencies->currencies as $key => $value) {
      echo '<a class="dropdown-item" href="' . tep_href_link($PHP_SELF, tep_get_all_get_params(array('language', 'currency')) . 'currency=' . $key, $request_type) . '">' . $value['title'] . '</a>' . PHP_EOL;
    }
    ?>
  </div>
</li>

<?php
/*
  Copyright (c) 2018, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
?>         