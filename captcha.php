<?php
session_start();
class CaptchaSecurityImages {
   var $font = 'fonts/monofont.ttf';
   /*array_rand(array('fonts/AirbagStreet.ttf',
				'fonts/AkashiMF.ttf',
				'fonts/AlaCarte.ttf',
				'fonts/AldosNova.ttf',
				'fonts/monofont.ttf',
				'fonts/Valiant.ttf',
				'fonts/VampireGames.ttf',
				'fonts/VampireGames3D.ttf',
				'fonts/VanishInTheHeat.ttf',
				'fonts/Variant4GeM.ttf',
				'fonts/VelvendaMegablack.ttf'),1);*/
   function generateCode($characters) {
      /* list all possible characters, similar looking characters and vowels have been removed */
      //~ $possible = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
      $possible = 'ABCDEFGHIJKLMNPQRSTUVWXYZ';
      $code = '';
      $i = 0;
      while ($i < $characters) {
         $code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
         $i++;
      }
      return $code;
   }
   function CaptchaSecurityImages($width='120',$height='40',$characters='6') {
      $code = $this->generateCode($characters);
      /* font size will be 75% of the image height */
      $font_size = $height * 0.9;
      $image = imagecreate($width, $height) or die('Cannot initialize new GD image stream');
      /* set the colours */
      $background_color = imagecolorallocate($image, 000, 000,000);
      $text_color = imagecolorallocate($image, 249,150,72);
      $noise_color = imagecolorallocate($image, 000, 000,000);
      /* generate random dots in background */

      for( $i=0; $i<($width*$height)/3; $i++ ) {

         imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
      }

      /* generate random lines in background */
      for( $i=0; $i<($width*$height)/150; $i++ ) {

         imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
      }

      /* create textbox and add text */
      $textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
      $x = ($width - $textbox[4])/2;
      $y = ($height - $textbox[5])/2;
      imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');
      /* output captcha image to browser */

      header('Content-Type: image/jpeg');
      imagejpeg($image);
      imagedestroy($image);
      $_SESSION['security_code'] = $code;
   }

}

$width = isset($_GET['width']) && $_GET['width'] < 600 ? $_GET['width'] : '150';

$height = isset($_GET['height']) && $_GET['height'] < 200 ? $_GET['height'] : '40';

$characters = (isset($_GET['characters']) && $_GET['characters'] > 2) ? $_GET['characters'] : '6';

$captcha = new CaptchaSecurityImages($width,$height,$characters);

?>
      
