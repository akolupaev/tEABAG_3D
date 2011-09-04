<?php
session_start();

include_once('./lib/teabag_3d/classes/teabagFront.php');
 
 $captcha = new teabagFace();
 $captcha->setMethod('stream');//this outputs image just as like PNG file was requested
 //$captcha->setBgColor(new Color(100,150, 200));
 //$captcha->setFgColor(new Color(20,20,150));

$_SESSION['captcha'] = $captcha->getCode();//never pass code to client, anyhow encrypted. Store it only at server!
 
$imgFile = $captcha->generate();