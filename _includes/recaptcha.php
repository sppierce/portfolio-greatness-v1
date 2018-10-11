<?php
  require_once('/php/recaptchalib.php');
  $publickey = "6LcBVhgUAAAAAL5vx7DZbvuuFqKU5z-NT-PECkdi"; // you got this from the signup page
  echo recaptcha_get_html($publickey);
?>