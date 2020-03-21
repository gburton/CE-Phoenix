<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License

  mail.php - a class to assist in building mime-HTML eMails

  The original class was made by Richard Heyes <richard@phpguru.org>
  and can be found here: http://www.phpguru.org

  Renamed and Modified by Jan Wildeboer for osCommerce
*/

  class email {

    /**
     * If you want the auto load functionality
     * to find other mime-image/file types, add the
     * extension and content type here.
     */
    const IMAGE_TYPES = [
      'gif' => 'image/gif',
      'jpg' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'jpe' => 'image/jpeg',
      'bmp' => 'image/bmp',
      'png' => 'image/png',
      'tif' => 'image/tiff',
      'tiff' => 'image/tiff',
      'swf' => 'application/x-shockwave-flash',
    ];

    const LINEFEEDS = ["\r\n", "\n", "\r"];

    protected $html;
    protected $text;
    protected $output;
    protected $html_text;
    protected $html_images = [];
    protected $build_params = [];
    protected $attachments = [];
    protected $headers = [];
    protected $lf;

    public function __construct($headers = []) {
      $this->lf = ((EMAIL_LINEFEED == 'CRLF') ? "\r\n" : "\n");


      $this->build_params['html_encoding'] = 'quoted-printable';
      $this->build_params['text_encoding'] = '7bit';
      $this->build_params['html_charset'] = constant('CHARSET');
      $this->build_params['text_charset'] = constant('CHARSET');
      $this->build_params['text_wrap'] = 998;

/**
 * Make sure the MIME version header is first.
 */
      $this->headers[] = 'MIME-Version: 1.0';
      $this->headers += array_filter(array_values($headers), 'tep_not_null');
    }

/**
 * This function will read a file in
 * from a supplied filename and return
 * it. This can then be given as the first
 * argument of the the functions
 * add_html_image() or add_attachment().
 */
    public function get_file($filename) {
      if ($fp = fopen($filename, 'rb')) {
        $return = '';

        while (!feof($fp)) {
          $return .= fread($fp, 1024);
        }
        fclose($fp);

        return $return;
      }

      return false;
    }

/**
 * Function for extracting images from
 * html source. This function will look
 * through the html code supplied by add_html()
 * and find any file that ends in one of the
 * extensions defined in $obj->image_types.
 * If the file exists it will read it in and
 * embed it, (not an attachment).
 *
 * Function contributed by Dan Allen
 */
    public function find_html_images($images_dir) {
// Build the list of image extensions
      $extensions = array_keys(static::IMAGE_TYPES);

      preg_match_all('/"([^"]+\.(' . implode('|', $extensions).'))"/Ui', $this->html, $images);

      $html_images = [];
      foreach ($images[1] as $image) {
        if (file_exists("$images_dir$image")) {
          $html_images[] = $image;
          $this->html = str_replace($image, basename($image), $this->html);
        }
      }

      if ([] !== $html_images) {
// If duplicate images are embedded, they may show up as attachments, so remove them.
        $html_images = array_unique($html_images);
        sort($html_images);

        foreach ($html_images as $html_image) {
          if ($image = $this->get_file("$images_dir$html_image")) {
            $content_type = static::IMAGE_TYPES[pathinfo($html_image, PATHINFO_EXTENSION)];
            $this->add_html_image($image, basename($html_image), $content_type);
          }
        }
      }
    }

/**
 * Adds plain text. Use this function
 * when NOT sending html email
 */
    public function add_text($text = '') {
      $this->text = str_replace(static::LINEFEEDS, $this->lf, $text);
    }

/**
 * Adds a html part to the mail.
 * Also replaces image names with
 * content-id's.
 */
    public function add_html($html, $text = null, $images_dir = null) {
      $this->html = str_replace(static::LINEFEEDS, '<br>', $html);
      $this->html_text = str_replace(static::LINEFEEDS, $this->lf, $text);

      if (isset($images_dir)) {
        $this->find_html_images($images_dir);
      }
    }

    public function add_message($email_text) {
      // Build the text version
      $text = strip_tags($email_text);
      if (EMAIL_USE_HTML == 'true') {
        $this->add_html($email_text, $text);
      } else {
        $this->add_text($text);
      }
    }

/**
 * Adds an image to the list of embedded
 * images.
 */
    public function add_html_image($file, $name = '', $c_type='application/octet-stream') {
      $this->html_images[] = [
        'body' => $file,
        'name' => $name,
        'c_type' => $c_type,
        'cid' => md5(uniqid(time())),
      ];
    }

/**
 * Adds a file to the list of attachments.
 */
    public function add_attachment($file, $name = '', $c_type='application/octet-stream', $encoding = 'base64') {
      $this->attachments[] = [
        'body' => $file,
        'name' => $name,
        'c_type' => $c_type,
        'encoding' => $encoding,
      ];
    }

    public function get_parameters($param_type, $value = null) {
      $params = [];
      switch ($param_type) {
        case 'text':
          $params['content_type'] = 'text/plain';
          $params['encoding'] = $this->build_params['text_encoding'];
          $params['charset'] = $this->build_params['text_charset'];
          return $params;
        case 'html':
          $params['content_type'] = 'text/html';
          $params['encoding'] = $this->build_params['html_encoding'];
          $params['charset'] = $this->build_params['html_charset'];
          return $params;
        case 'mixed':
        case 'alternative':
        case 'related':
          $params['content_type'] = "multipart/$param_type";
          return $params;
        case 'html_image':
          $params['content_type'] = $value['c_type'];
          $params['encoding'] = 'base64';
          $params['disposition'] = 'inline';
          $params['dfilename'] = $value['name'];
          $params['cid'] = $value['cid'];
          return $params;
        case 'attachment':
          $params['content_type'] = $value['c_type'];
          $params['encoding'] = $value['encoding'];
          $params['disposition'] = 'attachment';
          $params['dfilename'] = $value['name'];
          return $params;
      }

      return false;
    }

    protected function _build_message() {
      $attachments = tep_not_null($this->attachments);
      $html_images = tep_not_null($this->html_images);
      $html = tep_not_null($this->html);
      $text = tep_not_null($this->text);

      $message = null;
      switch (true) {
        case ($text && !$attachments):
          return new mime($this->text, $this->get_parameters('text'));
        case (!$text && $attachments && !$html):
          return new mime('', ['content_type' => 'multipart/mixed']);
        case ($text && $attachments):
          $message = new mime('', ['content_type' => 'multipart/mixed']);
          $message->addSubpart($this->text, $this->get_parameters('text'));
          return $message;
        case ($html && !$attachments && !$html_images):
          if (tep_not_null($this->html_text)) {
            $message = new mime('', ['content_type' => 'multipart/alternative']);
            $message->addSubpart($this->html_text, $this->get_parameters('text'));
            $message->addSubpart($this->html, $this->get_parameters('html'));
          } else {
            $message = new mime($this->html, $this->get_parameters('html'));
          }
          break;
        case ($html && !$attachments && $html_images):
          if (tep_not_null($this->html_text)) {
            $message = new mime('', ['content_type' => 'multipart/alternative']);
            $message->addSubpart($this->html_text, $this->get_parameters('text'));
            $related = $message->addSubpart('', ['content_type' => 'multipart/related']);
          } else {
            $message = new mime('', ['content_type' => 'multipart/related']);
            $related = $message;
          }
          $related->addSubpart($this->html, $this->get_parameters('html'));
          break;
        case ($html && $attachments && !$html_images):
          $message = new mime('', ['content_type' => 'multipart/mixed']);
          if (tep_not_null($this->html_text)) {
            $alt = $message->addSubpart('', ['content_type' => 'multipart/alternative']);
            $alt->addSubpart($this->html_text, $this->get_parameters('text'));
            $alt->addSubpart($this->html, $this->get_parameters('html'));
          } else {
            $message->addSubpart($this->html, $this->get_parameters('html'));
          }
          break;
        case ($html && $attachments && $html_images):
          $message = new mime('', ['content_type' => 'multipart/mixed']);

          if (tep_not_null($this->html_text)) {
            $alt = $message->addSubpart('', ['content_type' => 'multipart/alternative']);
            $alt->addSubpart($this->html_text, $this->get_parameters('text'));
            $related = $alt->addSubpart('', ['content_type' => 'multipart/related']);
          } else {
            $related = $message->addSubpart('', ['content_type' => 'multipart/related']);
          }
          $related->addSubpart($this->html, $this->get_parameters('html'));

          break;
      }

      if ($html && $html_images) {
        foreach ($this->html_images as $image) {
          $related->addSubpart($image['body'], $this->get_parameters('html_image', $image));
        }
      }

      return $message;
    }

/**
 * Builds the multipart message from the
 * list ($this->_parts). $params is an
 * array of parameters that shape the building
 * of the message. Currently supported are:
 *
 * $params['html_encoding'] - The type of encoding to use on html. Valid options are
 *                            "7bit", "quoted-printable" or "base64" (all without quotes).
 *                            7bit is EXPRESSLY NOT RECOMMENDED. Default is quoted-printable
 * $params['text_encoding'] - The type of encoding to use on plain text Valid options are
 *                            "7bit", "quoted-printable" or "base64" (all without quotes).
 *                            Default is 7bit
 * $params['text_wrap']     - The character count at which to wrap 7bit encoded data.
 *                            Default this is 998.
 * $params['html_charset']  - The character set to use for a html section.
 *                            Default is iso-8859-1
 * $params['text_charset']  - The character set to use for a text section.
 *                          - Default is iso-8859-1
 */
    public function build_message($params = []) {
      foreach ($params as $key => $value) {
        $this->build_params[$key] = $value;
      }

      foreach ($this->html_images as $value) {
        $this->html = str_replace($value['name'], 'cid:' . $value['cid'], $this->html);
      }

      $message = $this->_build_message();

      if ( is_object($message) ) {
        if (tep_not_null($this->attachments)) {
          foreach ($this->attachments as $attachment) {
            $message->addSubpart($attachment['body'], $this->get_parameters('attachment', $attachment));
          }
        }

        $output = $message->encode();
        $this->output = $output['body'];

        foreach($output['headers'] as $key => $value) {
          $headers[] = $key . ': ' . $value;
        }

        $this->headers = array_merge($this->headers, $headers);

        return true;
      } else {
        return false;
      }
    }

    public function normalize_headers($headers = []) {
      if (is_string($headers)) {
        $headers = explode($this->lf, trim($headers));
      }

      $xtra_headers = [];
      foreach ($headers as $header) {
        if (is_array($header)) {
          $xtra_headers += array_filter($header);
        } elseif ($header) {
          $xtra_headers[] = $header;
        }
      }

      return $xtra_headers;
    }

    public function format_address($address, $name = '') {
      return (('' == $name) ? $address : '"' . $name . '" <' . $address . '>');
    }

/**
 * Sends the mail.
 */
    public function send($to_name, $to_addr, $from_name, $from_addr, $subject = '', $headers = []) {
      // No need to check for "\r\n" separately as will match the other two
      foreach (["\n", "\r"] as $line_ending) {
        foreach ([$to_name, $to_addr, $subject, $from_name, $from_addr] as $header_value) {
          if (false !== strstr($header_value, $line_ending)) {
            return false;
          }
        }
      }

      $to = $this->format_address($to_addr, $to_name);
      $from = $this->format_address($from_addr, $from_name);

      if (defined('EMAIL_FROM')) {
        $sender_headers = ['From: ' . EMAIL_FROM, 'Reply-to: ' . $from];
        $from_addr = EMAIL_FROM;
      } else {
        $sender_headers = ['From: ' . $from];
      }

      $headers = array_merge($this->headers, $sender_headers, $this->normalize_headers($headers));

      return mail($to, $subject, $this->output, implode($this->lf, $headers), "-f$from_addr");
    }

/**
 * Use this method to return the email
 * in message/rfc822 format. Useful for
 * adding an email to another email as
 * an attachment. there's a commented
 * out example in example.php.
 *
 * string get_rfc822(string To name,
 *       string To email,
 *       string From name,
 *       string From email,
 *       [string Subject,
 *        string Extra headers])
 */
    public function get_rfc822($to_name, $to_addr, $from_name, $from_addr, $subject = '', $headers = []) {
// Make up the date header as according to RFC822
      $date = 'Date: ' . date('D, d M y H:i:s');
      $to = 'To: ' . $this->format_address($to_addr, $to_name);
      $from = 'From: ' . $this->format_address($from_addr, $from_name);

      if (is_string($subject)) {
        $subject = 'Subject: ' . $subject;
      }

      $headers = array_merge($this->headers, $this->normalize_headers($headers));

      return $date . $this->lf . $from . $this->lf . $to . $this->lf . $subject . $this->lf . implode($this->lf, $headers) . $this->lf . $this->lf . $this->output;
    }
  }
