<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('URL','http://localhost/postermaker/');
define("ADMIN_TITLE","Digital Poster Maker Admin");
define("IMAGE",URL."images/"); 
define("BCSS",URL."bootstrap/css/");
define("BJS",URL."bootstrap/js/");
define("PLUGINS",URL."plugins/"); 

define("ADMIN_ASSETS",URL."assets_admin/");
// define("IMAGESOURCEPATH",$_SERVER['DOCUMENT_ROOT'].'/projects/postermaker/');
define("IMAGESOURCEPATH",$_SERVER['DOCUMENT_ROOT'].'/postermaker/');

define("NO_IMAGE",URL."images/no_logo.png");
define("NO_USER",URL."images/ic_avatar.png");
define("NO_LOGO",URL."images/no_logo.png");
define("LOGO",URL."images/logo.png");

// define("RAZOR_KEY_ID","rzp_test_y03gZA4O70JQKK");
// define("RAZOR_KEY_SECRET","RyiWc6aR1gdVYtKZlSAB34CH");

define("RAZOR_KEY_ID","rzp_live_WO3ENXEyWp2YlL");
define("RAZOR_KEY_SECRET","u24gnUWuBuQANrXOdHXTROga");