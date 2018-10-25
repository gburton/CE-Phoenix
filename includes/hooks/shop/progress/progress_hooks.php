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

class hook_shop_progress_progress_hooks {
  
  function listen_progressBar($arr) {

    $checkout_bar_delivery     = CHECKOUT_BAR_DELIVERY;
    $checkout_bar_payment      = CHECKOUT_BAR_PAYMENT;
    $checkout_bar_confirmation = CHECKOUT_BAR_CONFIRMATION;
      
    $output_progress = <<<eod
      <div class="stepwizard">    
        <div class="progress">
          <div class="{$arr['style']}" role="progressbar" style="width: {$arr['markers']['now']}%" aria-valuenow="{$arr['markers']['now']}" aria-valuemin="{$arr['markers']['min']}" aria-valuemax="{$arr['markers']['max']}"></div>
        </div>
        <div class="row">
          <div class="col text-center">{$checkout_bar_delivery}</div>
          <div class="col text-center">{$checkout_bar_payment}</div>
          <div class="col text-center">{$checkout_bar_confirmation}</div>
        </div>
      </div>  
eod;
    
    return $output_progress;
  }
  
}
