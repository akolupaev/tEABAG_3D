<?php
 include_once('./lib/teabag_3d/classes/teabagFront.php');
 
 
 $captcha = new teabagFace();
 
 //this allows you to store image files, without giving it back from the script as PNG stream
 $captcha->setMethod('file');
 //$captcha->setBgColor(new Color(100,150, 200)); //set it for specific color. White by default
 //$captcha->setFgColor(new Color(20,20,150)); //set if for specific color. Random dark is default
 $captcha->setSavePath ( $captcha->getBasePath() . '/images/' ) ;
 $imgFile = $captcha->generate();
 
 echo '<img src="./images/'. $imgFile . '" />';
 echo $captcha->getCode();
 
?>