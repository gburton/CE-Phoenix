<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License

  browser language detection logic Copyright phpMyAdmin (select_lang.lib.php3 v1.24 04/19/2002)
                                   Copyright Stephane Garin <sgarin@sgarin.com> (detect_language.php v0.1 04/02/2002)
*/

  class language {

    const LANGUAGES = [
      'af' => 'af|afrikaans',
      'ar' => 'ar([-_][[:alpha:]]{2})?|arabic',
      'be' => 'be|belarusian',
      'bg' => 'bg|bulgarian',
      'br' => 'pt[-_]br|brazilian portuguese',
      'ca' => 'ca|catalan',
      'cs' => 'cs|czech',
      'da' => 'da|danish',
      'de' => 'de([-_][[:alpha:]]{2})?|german',
      'el' => 'el|greek',
      'en' => 'en([-_][[:alpha:]]{2})?|english',
      'es' => 'es([-_][[:alpha:]]{2})?|spanish',
      'et' => 'et|estonian',
      'eu' => 'eu|basque',
      'fa' => 'fa|farsi',
      'fi' => 'fi|finnish',
      'fo' => 'fo|faeroese',
      'fr' => 'fr([-_][[:alpha:]]{2})?|french',
      'ga' => 'ga|irish',
      'gl' => 'gl|galician',
      'he' => 'he|hebrew',
      'hi' => 'hi|hindi',
      'hr' => 'hr|croatian',
      'hu' => 'hu|hungarian',
      'id' => 'id|indonesian',
      'it' => 'it|italian',
      'ja' => 'ja|japanese',
      'ko' => 'ko|korean',
      'ka' => 'ka|georgian',
      'lt' => 'lt|lithuanian',
      'lv' => 'lv|latvian',
      'mk' => 'mk|macedonian',
      'mt' => 'mt|maltese',
      'ms' => 'ms|malaysian',
      'nl' => 'nl([-_][[:alpha:]]{2})?|dutch',
      'no' => 'no|norwegian',
      'pl' => 'pl|polish',
      'pt' => 'pt([-_][[:alpha:]]{2})?|portuguese',
      'ro' => 'ro|romanian',
      'ru' => 'ru|russian',
      'sk' => 'sk|slovak',
      'sq' => 'sq|albanian',
      'sr' => 'sr|serbian',
      'sv' => 'sv|swedish',
      'sz' => 'sz|sami',
      'sx' => 'sx|sutu',
      'th' => 'th|thai',
      'ts' => 'ts|tsonga',
      'tr' => 'tr|turkish',
      'tn' => 'tn|tswana',
      'uk' => 'uk|ukrainian',
      'ur' => 'ur|urdu',
      'vi' => 'vi|vietnamese',
      'tw' => 'zh[-_]tw|chinese traditional',
      'zh' => 'zh|chinese simplified',
      'ji' => 'ji|yiddish',
      'zu' => 'zu|zulu',
    ];

    public static function parse_browser_languages() {
      $acceptable_locales = [];
      foreach (explode(',', str_replace(' ', '', getenv('HTTP_ACCEPT_LANGUAGE'))) as $entry) {
        $locale_qualities = explode(';q=', $entry);
        $acceptable_locales[] = [
          'locale' => $locale_qualities[0],
          'quality' => $locale_qualities[1] ?? 1,
          'codes' => explode('-', $locale_qualities[0]),
        ];
      }

      usort($acceptable_locales, function ($a, $b) {
        $result = $b['quality'] <=> $a['quality'];
        if ((0 === $result) && ($b['codes'][0] === $a['codes'][0])) {
          return count($b['codes']) <=> count($a['codes']);
        }

        return $result;
      });

      return array_filter(
        array_map('strtolower', array_column($acceptable_locales, 'locale')),
        function ($v) {
          foreach (static::LANGUAGES as $language) {
            if (preg_match("{\A(?:$v)\z}", $language)) {
              return true;
            }
          }

          return false;
        });
    }

    public static function load_languages() {
      $languages = [];

      $languages_query = tep_db_query("SELECT languages_id, name, code, image, directory FROM languages ORDER BY sort_order");
      while ($language = $languages_query->fetch_assoc()) {
        $languages[$language['code']] = [
          'id' => $language['languages_id'],
          'name' => $language['name'],
          'image' => $language['image'],
          'directory' => $language['directory'],
        ];
      }

      return $languages;
    }

    public static function negotiate($languages) {
      $fallback = null;
      foreach (static::parse_browser_languages() as $locale) {
        if (isset($languages[$locale])) {
          return $locale;
        }

        if (is_null($fallback) && isset($languages[$locale = substr($locale, 0, 2)])) {
// if we do not yet have a fallback in case no locale matches, create one
          $fallback = $locale;
        }
      }

      return $fallback ?? DEFAULT_LANGUAGE;
    }

    public static function build() {
      $languages = static::load_languages();

      if (empty($_GET['language'])) {
        $locale = static::negotiate($languages);
      } else {
        $locale = $_GET['language'];
      }

      $language = new static($locale, $languages);

      $_SESSION['language'] = $language->language['directory'];
      $_SESSION['languages_id'] = $language->language['id'];

      return $language;
    }

    public static function map_to_translation($page, $language = null) {
      if (is_null($language)) {
        $language = $_SESSION['language'];
      }

      $page = ('.php' === $page)
            ? "includes/languages/$language.php"
            : "includes/languages/$language/$page";
      $template =& Guarantor::ensure_global('oscTemplate');
      $translation = $template->map_to_template($page, 'translation')
                  ?? DIR_FS_CATALOG . $page;

      return file_exists($translation) ? $translation : DIR_FS_CATALOG . $page;
    }

    public $catalog_languages;
    public $language;

    public function __construct($selection = null, $languages = null) {
      $this->catalog_languages = $languages ?? static::load_languages();

      $this->set_language($selection);
    }

    public function set_language($language) {
      $this->language = $this->catalog_languages[$language ?? DEFAULT_LANGUAGE]
                     ?? $this->catalog_languages[DEFAULT_LANGUAGE];
    }

    public function get_browser_language() {
      trigger_error('The get_browser_language function has been deprecated.', E_USER_DEPRECATED);
      $this->language = $this->catalog_languages[static::negotiate($this->catalog_languages)];
    }

  }
