<?php
/**
* Config.php
* Including all configuration info such as Database, Path, URL
*/
//For Security 
if(!defined('BUCKYS_APP'))
    define('BUCKYS_APP', true);
    
//Database Info    
if(!defined('DATABASE_HOST'))
    define('DATABASE_HOST', 'localhost');
if(!defined('DATABASE_USERNAME'))
    define('DATABASE_USERNAME', 'buckysroom');
if(!defined('DATABASE_PASSWORD'))
    define('DATABASE_PASSWORD', ''); //
if(!defined('DATABASE_NAME'))
    define('DATABASE_NAME', 'buckysroom');


//Define CSS PATH FOR WEB
if(!defined('DIR_WS_CSS'))
    define('DIR_WS_CSS', "/css/");

//Define Javascript PATH For Web
if(!defined('DIR_WS_JS'))
    define('DIR_WS_JS', "/js/");

//Define Image Path For Web
if(!defined('DIR_WS_IMAGE'))
    define('DIR_WS_IMAGE', "/images/");

//Define Photo Path For Web
if(!defined('DIR_WS_PHOTO'))
    define('DIR_WS_PHOTO', "/photos/");
    
//Define Site ROOT PATH
if(!defined('DIR_FS_ROOT'))
    define('DIR_FS_ROOT', dirname(dirname(__FILE__)) . "/");

//Define Include Path
if(!defined('DIR_FS_INCLUDES'))
    define('DIR_FS_INCLUDES', DIR_FS_ROOT . "includes/");

//Define Classes Path
if(!defined('DIR_FS_CLASSES'))
    define('DIR_FS_CLASSES', DIR_FS_INCLUDES . "classes/");

//Define Functions Path
if(!defined('DIR_FS_TEMPLATE'))
    define('DIR_FS_TEMPLATE', DIR_FS_ROOT. "templates/");

//Define Functions Path
if(!defined('DIR_FS_FUNCTIONS'))
    define('DIR_FS_FUNCTIONS', DIR_FS_INCLUDES . "functions/");

//Define Photos Path
if(!defined('DIR_FS_PHOTO'))
    define('DIR_FS_PHOTO', DIR_FS_ROOT . "photos/");
//Define Photos TMP Path
if(!defined('DIR_FS_PHOTO_TMP'))
    define('DIR_FS_PHOTO_TMP', DIR_FS_PHOTO . "tmp/");

//Define Trade Constants    
if(!defined('DIR_FS_TRADE_IMG'))
    define('DIR_FS_TRADE_IMG', DIR_FS_ROOT . "images/trade/");
    
if(!defined('DIR_FS_TRADE_IMG_TMP'))
    define('DIR_FS_TRADE_IMG_TMP', DIR_FS_TRADE_IMG . "tmp/");

//Define Session Name
if(!defined('SESSION_NAME'))
    define('SESSION_NAME', 'BUCKYSSESSID');
//Define Default Session LifeTIme
if(!defined('SESSION_LIFETIME'))
{
    if(ini_get('session.gc_maxlifetime') <= 0)
        define('SESSION_LIFETIME', 1440);        
    else
        define('SESSION_LIFETIME', ini_get('session.gc_maxlifetime'));    
}

//Define the cookie lifetime for Keep Signed In
if(!defined('COOKIE_LIFETIME'))
    define('COOKIE_LIFETIME', 60 * 60 * 24 * 7); //7 Days
    
//Default Default Template Name
if(!defined('DEFAULT_THEME'))
    define('DEFAULT_THEME', 'default');

//Define Recaptcha Keys
if(!defined('RECAPTCHA_PUBLIC_KEY'))
    define('RECAPTCHA_PUBLIC_KEY', '6LdKeOESAAAAAHEOUIAkodGKrjkmTmnHSwq5M6J2');
if(!defined('RECAPTCHA_PRIVATE_KEY'))
    define('RECAPTCHA_PRIVATE_KEY', '6LdKeOESAAAAAKfAKstTWIWwCqPF-PIjhn4bkbUT');


//Define Message Types
if(!defined('MSG_TYPE_SUCCESS'))
    define('MSG_TYPE_SUCCESS', 1);
if(!defined('MSG_TYPE_ERROR'))
    define('MSG_TYPE_ERROR', 0);
if(!defined('MSG_TYPE_NOTIFY'))
    define('MSG_TYPE_NOTIFY', 0);

define('MAX_IMAGE_WIDTH', 3000);
define('MAX_IMAGE_HEIGHT', 3000);
define('PROFILE_IMAGE_WIDTH', 230);
define('PROFILE_IMAGE_HEIGHT', 230);
define('POST_IMAGE_WIDTH', 400);
define('POST_IMAGE_HEIGHT', 300);
define('MAX_POST_IMAGE_WIDTH', 677);
define('MAX_POST_IMAGE_HEIGHT', 525);
define('IMAGE_THUMBNAIL_WIDTH', 200);
define('IMAGE_THUMBNAIL_HEIGHT', 200);

//Paypal Settings
define('BUCKYSROOM_PAYPAL_MODE_LIVE', true); // you should change this one to yes when you use live paypal
define('BUCKYSROOM_PAYPAL_EMAIL', '');
define('BUCKYSROOM_PAYPAL_CURRENCY', 'USD');
define('BUCKYSROOM_PAYPAL_NOTIFY_URL', '');
define('BUCKYSROOM_PAYPAL_RETURN_URL', '');
define('BUCKYSROOM_PAYPAL_CANCEL_RETURN_URL', '');

//------- SANDBOX --------------//
define('BUCKYSROOM_PAYPAL_SANDBOX_EMAIL', ''); //test personal account


//Trade Constants
define('TRADE_ROWS_PER_PAGE', 30);
define('TRADE_ITEM_LIFETIME', 7); // in days, after this, it will be deleted.
define('TRADE_ITEM_IMAGE_THUMB_SUFFIX', '_thumb'); // in days, after this, it will be deleted.

//For Security
define('MAX_LOGIN_ATTEMPT', 5);
define('MAX_LOGIN_ATTEMPT_PERIOD', 15 * 60); // 900 Seconds = 15 Mins
define('PASSWORD_TOKEN_EXPIRY_DATE', 1); //Password Token Expiry Date = 1 Day

//Moderator Types
define('MODERATOR_FOR_COMMUNITY', 1);
define('MODERATOR_FOR_FORUM', 2);
define('MODERATOR_FOR_TRADE', 3);

//Global Variable that includes all global settings
$BUCKYS_GLOBALS = array(
    //Months
    'months'                => array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
    //RelationShipStatus
    'relationShipStatus'    => array(
                                    1 => 'Single', 
                                    2 => 'In a Relationship'
                                ),
    //Genders
    'genders'               => array(
                                    'Male' => 'Male',
                                    'Female' => 'Female'
                                ),
    //Messenger Types
    'messengerTypes'        => array(
                                    'AIM',
                                    'Google Talk',
                                    'PS3',
                                    'Skype',
                                    'Xbox',
                                    'Yahoo! Messenger',
                                    'Other'
                                ),
    //Image Types
    'imageTypes'            => array(
                                    'jpg',
                                    'jpeg',
                                    'png',
                                    'gif'
                                ),
    'timezone'              => array(
                                    '(UTC-12:00) International Date Line West' => -12 ,
                                    '(UTC-11:00) Coordinated Universal Time-11' => -11 ,
                                    '(UTC-11:00) Samoa' => -11 ,
                                    '(UTC-10:00) Hawaii' => -10 ,
                                    '(UTC-09:00) Alaska' => -9,
                                    '(UTC-08:00) Baja California' => -8,
                                    '(UTC-08:00) Pacific Time (US & Canada)' => -8,
                                    '(UTC-07:00) Arizona' => -7,
                                    '(UTC-07:00) Chihuahua, La Paz, Mazatlan' => -7,
                                    '(UTC-07:00) Mountain Time(US & Canada)' => -7,
                                    '(UTC-06:00) Central America' => -6,
                                    '(UTC-06:00) Central Time(US & Canada)' => -6,
                                    '(UTC-06:00) Guadalajara, Mexico City, Monterrey' => -6,
                                    '(UTC-06:00) Saskatchewan' => -6,
                                    '(UTC-05:00) Bogota, Lima, Quito' => -6,
                                    '(UTC-05:00) Eastern Time(US & Canada)' => -6,
                                    '(UTC-05:00) Indiana(East)' => -6,
                                    '(UTC-04:30) Caracas' => -4.5,
                                    '(UTC-04:00) Asuncion' => -4,
                                    '(UTC-04:00) Atlantic Time(Canada)' => -4,
                                    '(UTC-04:00) Cuiaba' => -4,
                                    '(UTC-04:00) Georgetown, La Paz, Manaus, Sna Juan' => -4,
                                    '(UTC-04:00) Santiago' => -4,
                                    '(UTC-03:30) Newfoundland' => -3.5,
                                    '(UTC-03:00) Brasilia' => -3,
                                    '(UTC-03:00) Buenos Aires' => -3,
                                    '(UTC-03:00) Cayenne, Fortaleza' => -3,
                                    '(UTC-03:00) Greenland' => -3,
                                    '(UTC-03:00) Montevideo' => -3,
                                    '(UTC-02:00) Coordinated Universal Time-02' => -2,
                                    '(UTC-02:00) Mid-Atlantic' => -2,
                                    '(UTC-02:00) Azores' => -2,
                                    '(UTC-01:00) Cape Verde Is.' => -1,
                                    '(UTC) Casablanca' => 0,
                                    '(UTC) Coordinated Universal Time' => 0,
                                    '(UTC) Dublin, Edinburgh, Lisbon, London' => 0,
                                    '(UTC) Monrovia, Reykjavik' => 0,
                                    '(UTC+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna' => 1,
                                    '(UTC+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague' => 1,
                                    '(UTC+01:00) Brussels, Copenhagen, Madrid, Paris' => 1,
                                    '(UTC+01:00) Sarajevo, Skopje, Warsaw, Zagreb' => 1,
                                    '(UTC+01:00) West Central Africa' => 1,
                                    '(UTC+01:00) Windhoek' => 1,
                                    '(UTC+02:00) Amman' => 2,
                                    '(UTC+02:00) Athens, Bucharest, Istanbul' => 2,
                                    '(UTC+02:00) Beirut' => 2,
                                    '(UTC+02:00) Cairo' => 2,
                                    '(UTC+02:00) Damascus' => 2,
                                    '(UTC+02:00) Harare, Pretoria' => 2,
                                    '(UTC+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius' => 2,
                                    '(UTC+02:00) Jerusalem' => 2,
                                    '(UTC+02:00) Minsk' => 2,
                                    '(UTC+03:00) Baghdad' => 3,
                                    '(UTC+03:00) Kuwait, Riyadh' => 3,
                                    '(UTC+03:00) Moscow, ST. Petersburg, Volgograd' => 3,
                                    '(UTC+03:00) Nairobi' => 3,
                                    '(UTC+03:30) Tehran' => 3.5,
                                    '(UTC+04:00) Abu Dhabi, Muscat' => 4,
                                    '(UTC+04:00) Baku' => 4,
                                    '(UTC+04:00) Port Louis' => 4,
                                    '(UTC+04:00) Tbilisi' => 4,
                                    '(UTC+04:00) Yerevan' => 4,
                                    '(UTC+04:30) Kabul' => 4.5,
                                    '(UTC+05:00) Tashkent' => 5,
                                    '(UTC+05:30) Chennai, Kolkata, Mumbai, New Delhi' => 5.5,
                                    '(UTC+05:30) Sri Jayawardenepura' => 5.5,
                                    '(UTC+05:45) Kathmandu' => 5.75,
                                    '(UTC+06:00) Astana' => 6,
                                    '(UTC+06:00) Dhaka' => 6,
                                    '(UTC+06:00) Novosibirsk' => 6,
                                    '(UTC+06:30) Yangon (Rangoon)' => 6.5,
                                    '(UTC+07:00) Bangkok, Hanoi, Jakarta' => 7,
                                    '(UTC+07:00) Krasnoyarsk' => 7,
                                    '(UTC+08:00) Beijing, Chongqing, Hongkong' => 8,
                                    '(UTC+08:00) Irkutsk' => 8,
                                    '(UTC+08:00) Kuala Lumpur, Singapore' => 8,
                                    '(UTC+08:00) Perth' => 8,
                                    '(UTC+08:00) Taipei' => 8,
                                    '(UTC+08:00) Ulaanbaatar' => 8,
                                    '(UTC+08:00) Osaka, Sapporo, Tokyo' => 8,
                                    '(UTC+09:00) Seoul' => 9,
                                    '(UTC+09:00) Yakutsk' => 9,
                                    '(UTC+09:30) Adelaide' => 9.5,
                                    '(UTC+09:30) Darwin' => 9.5,
                                    '(UTC+10:00) Brisbane' => 10,
                                    '(UTC+10:00) Canberra, Melbourne, Sydney' => 10,
                                    '(UTC+10:00) Guam, Port Moresby' => 10,
                                    '(UTC+10:00) Hobart' => 10,
                                    '(UTC+10:00) Vladivostok' => 10,
                                    '(UTC+11:00) Magadan' => 11,
                                    '(UTC+11:00) Solomon Is., New Caledonia' => 11,
                                    '(UTC+12:00) Auckland, Wellington' => 12,
                                    '(UTC+12:00) Coordinated Universal Time+12' => 12,
                                    '(UTC+12:00) Fiji' => 12,
                                    '(UTC+13:00) Nukualofa' => 13
                                ),
    'moderatorTypes'        => array(MODERATOR_FOR_COMMUNITY => 'community', MODERATOR_FOR_FORUM => 'forum', MODERATOR_FOR_TRADE => 'trade'), //Moderator Types
    'reportObjectTypes'     => array(
                                    MODERATOR_FOR_COMMUNITY => array('post', 'comment', 'message'),
                                    MODERATOR_FOR_FORUM => array('topic', 'reply'),
                                    MODERATOR_FOR_TRADE => array()
                                ),
    //Default Forum Settings
    'forum_settings'        => array(
                                    'notifyRepliedToMyTopic' => 1,
                                    'notifyRepliedToMyReply' => 1,
                                    'notifyMyPostApproved' => 1
                                ),
    'javascripts'           => array(),                             //Javascript files
    'stylesheets'           => array(),                             //Stylesheet Files: load main.css in default
    'template'              => DEFAULT_THEME,                       //Template Name: Default = default
    'layout'                => 'layout',                            //Layout File Name: Default = layout
    'headerType'            => 'default'                           //Layout File Name: Default = layout
);