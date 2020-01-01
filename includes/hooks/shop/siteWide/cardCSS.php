<?php
/*
  Copyright (c) 2020, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

class hook_shop_siteWide_cardCSS {
  
  public $SiteStart = null;

  function listen_injectSiteStart() {
    $sm_count = IS_PRODUCT_PRODUCTS_DISPLAY_ROW_SM;
    $md_count = IS_PRODUCT_PRODUCTS_DISPLAY_ROW_MD;
    $lg_count = IS_PRODUCT_PRODUCTS_DISPLAY_ROW_LG;
    $xl_count = IS_PRODUCT_PRODUCTS_DISPLAY_ROW_XL;
    
    $sm = (100/$sm_count);
    $md = (100/$md_count);
    $lg = (100/$lg_count);
    $xl = (100/$xl_count);

    $cardCSS = <<<eod
<style>@media (min-width: 576px) {.card-group > .card.is-product {max-width: {$sm}%;}.card-deck > .card.is-product {max-width: calc({$sm}% - 30px);}.card-columns {column-count: {$sm_count};}} @media (min-width: 768px) {.card-group > .card.is-product {max-width: {$md}%;}.card-deck > .card.is-product {max-width: calc({$md}% - 30px);}.card-columns {column-count: {$md_count};}} @media (min-width: 992px) {.card-group > .card.is-product {max-width: {$lg}%;}.card-deck > .card.is-product {max-width: calc({$lg}% - 30px);}.card-columns {column-count: {$lg_count};}} @media (min-width: 1200px) {.card-group > .card.is-product {max-width: {$xl}%;}.card-deck > .card.is-product {max-width: calc({$xl}% - 30px);}.card-columns {column-count: {$xl_count};}}</style>
eod;

    $this->SiteStart .= $cardCSS . PHP_EOL;

    return $this->SiteStart;
  }

}
