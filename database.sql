/*Table structure for table `activities` */

DROP TABLE IF EXISTS `activities`;

CREATE TABLE `activities` (
  `activityID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `objectID` int(11) DEFAULT NULL,
  `objectType` varchar(20) DEFAULT NULL,
  `activityType` varchar(20) DEFAULT NULL,
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actionID` int(11) DEFAULT '0',
  `isNew` tinyint(2) DEFAULT '0',
  `activityStatus` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`activityID`),
  KEY `userID_index` (`userID`) USING BTREE,
  KEY `objectID_index` (`objectID`) USING BTREE,
  KEY `actionID_index` (`actionID`) USING BTREE,
  KEY `activityType_index` (`activityType`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=27847 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `albums` */

DROP TABLE IF EXISTS `albums`;

CREATE TABLE `albums` (
  `albumID` int(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `visibility` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`albumID`)
) ENGINE=MyISAM AUTO_INCREMENT=369 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `albums_photos` */

DROP TABLE IF EXISTS `albums_photos`;

CREATE TABLE `albums_photos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=801 DEFAULT CHARSET=utf8;

/*Table structure for table `banned_users` */

DROP TABLE IF EXISTS `banned_users`;

CREATE TABLE `banned_users` (
  `bannedID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `bannedUserID` int(11) DEFAULT NULL,
  `bannedDate` datetime DEFAULT NULL,
  PRIMARY KEY (`bannedID`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/*Table structure for table `countries` */

DROP TABLE IF EXISTS `countries`;

CREATE TABLE `countries` (
  `countryID` int(11) NOT NULL AUTO_INCREMENT,
  `country_title` varchar(255) NOT NULL DEFAULT '',
  `country_code` varchar(2) NOT NULL DEFAULT '',
  `status` tinyint(2) DEFAULT '1' COMMENT '0: disable, 1: enable',
  PRIMARY KEY (`countryID`)
) ENGINE=MyISAM AUTO_INCREMENT=246 DEFAULT CHARSET=utf8;

/*Data for the table `countries` */
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Afghanistan','AF',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Aland Islands','AX',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Albania','AL',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Algeria','DZ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('American Samoa','AS',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Andorra','AD',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Angola','AO',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Anguilla','AI',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Antarctica','AQ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Antigua and Barbuda','AG',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Argentina','AR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Armenia','AM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Aruba','AW',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Australia','AU',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Austria','AT',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Azerbaijan','AZ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Bahamas, The','BS',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Bahrain','BH',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Bangladesh','BD',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Barbados','BB',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Belarus','BY',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Belgium','BE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Belize','BZ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Benin','BJ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Bermuda','BM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Bhutan','BT',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Bolivia','BO',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Bosnia and Herzegovina','BA',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Botswana','BW',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Bouvet Island','BV',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Brazil','BR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('British Indian Ocean Territory','IO',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('British Virgin Islands','VG',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Brunei','BN',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Bulgaria','BG',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Burkina Faso','BF',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Burundi','BI',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Cambodia','KH',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Cameroon','CM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Canada','CA',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Cape Verde','CV',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Cayman Islands','KY',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Central African Republic','CF',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Chad','TD',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Chile','CL',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('China','CN',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Christmas Island','CX',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Cocos (Keeling) Islands','CC',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Colombia','CO',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Comoros','KM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Congo, Democratic Republic of the','CD',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Congo, Republic of the','CG',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Cook Islands','CK',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Costa Rica','CR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Cote d\'Ivoire','CI',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Croatia','HR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Cuba','CU',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Cyprus','CY',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Czech Republic','CZ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Denmark','DK',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Djibouti','DJ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Dominica','DM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Dominican Republic','DO',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('East Timor','TL',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Ecuador','EC',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Egypt','EG',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('El Salvador','SV',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Equatorial Guinea','GQ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Eritrea','ER',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Estonia','EE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Ethiopia','ET',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Falkland Islands (Islas Malvinas)','FK',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Faroe Islands','FO',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Fiji','FJ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Finland','FI',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('France','FR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('French Guiana','GF',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('French Polynesia','PF',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('French Southern and Antarctic Lands','TF',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Gabon','GA',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Gambia, The','GM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Georgia','GE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Germany','DE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Ghana','GH',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Gibraltar','GI',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Greece','GR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Greenland','GL',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Grenada','GD',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Guadeloupe','GP',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Guam','GU',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Guatemala','GT',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Guinea','GN',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Guinea-Bissau','GW',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Guyana','GY',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Haiti','HT',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Heard Island and McDonald Islands','HM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Holy See (Vatican City)','VA',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Honduras','HN',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Hong Kong','HK',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Hungary','HU',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Iceland','IS',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('India','IN',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Indonesia','ID',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Iran','IR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Iraq','IQ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Ireland','IE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Isle of Man','IM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Israel','IL',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Italy','IT',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Jamaica','JM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Japan','JP',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Jersey','JE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Jordan','JO',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Kazakhstan','KZ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Kenya','KE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Kiribati','KI',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Korea, North','KP',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Korea, South','KR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Kosovo','KV',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Kuwait','KW',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Kyrgyzstan','KG',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Laos','LA',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Latvia','LV',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Lebanon','LB',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Lesotho','LS',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Liberia','LR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Libyan Arab','LY',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Liechtenstein','LI',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Lithuania','LT',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Luxembourg','LU',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Macau','MO',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Madagascar','MG',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Malawi','MW',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Malaysia','MY',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Maldives','MV',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Mali','ML',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Malta','MT',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Marshall Islands','MH',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Martinique','MQ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Mauritania','MR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Mauritius','MU',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Mayotte','YT',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Mexico','MX',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Micronesia, Federated States of','FM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Moldova, Republic of','MD',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Monaco','MC',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Mongolia','MN',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Montenegro','ME',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Montserrat','MS',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Morocco','MA',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Mozambique','MZ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Myanmar','MM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Namibia','NA',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Nauru','NR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Nepal','NP',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Netherlands','NL',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Netherlands Antilles','AN',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('New Caledonia','NC',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('New Zealand','NZ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Nicaragua','NI',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Niger','NE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Nigeria','NG',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Niue','NU',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Norfolk Island','NF',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Northern Mariana Islands','MP',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Norway','NO',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Oman','OM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Pakistan','PK',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Palau','PW',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Panama','PA',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Papua New Guinea','PG',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Paraguay','PY',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Peru','PE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Philippines','PH',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Pitcairn Islands','PN',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Poland','PL',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Portugal','PT',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Puerto Rico','PR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Qatar','QA',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Reunion','RE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Romania','RO',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Russia','RU',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Rwanda','RW',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Saint Helena','SH',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Saint Kitts and Nevis','KN',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Saint Lucia','LC',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Saint Martin','MF',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Saint Pierre and Miquelon','PM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Saint Vincent and the Grenadines','VC',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Samoa','WS',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('San Marino','SM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Sao Tome and Principe','ST',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Saudi Arabia','SA',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Senegal','SN',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Serbia','RS',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Serbia and Montenegro','CS',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Seychelles','SC',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Sierra Leone','SL',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Singapore','SG',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Slovakia','SK',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Slovenia','SI',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Solomon Islands','SB',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Somalia','SO',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('South Africa','ZA',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('South Georgia and the Islands','GS',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('South Sudan','SS',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Spain','ES',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Sri Lanka','LK',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Sudan','SD',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Suriname','SR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Svalbard and Jan Mayen Islands','SJ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Swaziland','SZ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Sweden','SE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Switzerland','CH',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Syrian Arab Republic','SY',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Taiwan','TW',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Tajikistan','TJ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Tanzania, United Republic of','TZ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Thailand','TH',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('The Former Yugoslav Republic of Macedonia','MK',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Togo','TG',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Tokelau','TK',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Tonga','TO',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Trinidad and Tobago','TT',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Tunisia','TN',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Turkey','TR',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Turkmenistan','TM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Turks and Caicos Islands','TC',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Tuvalu','TV',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Uganda','UG',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Ukraine','UA',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('United Arab Emirates','AE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('United Kingdom','GB',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('United States','US',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Uruguay','UY',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Uzbekistan','UZ',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Vanuatu','VU',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Venezuela','VE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Vietnam','VN',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Virgin Islands (US)','VI',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Wallis and Futuna','WF',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Western Sahara','EH',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Yemen','YE',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Zambia','ZM',1);
insert  into `countries`(`country_title`,`country_code`,`status`) values ('Zimbabwe','ZW',1);

/*Table structure for table `credit_activity` */

DROP TABLE IF EXISTS `credit_activity`;

CREATE TABLE `credit_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `receiverID` int(11) unsigned NOT NULL COMMENT 'The user ID who has received this credits',
  `payerID` int(11) unsigned NOT NULL COMMENT 'The user ID who has sent this credits. 0 means paypal',
  `activityType` tinyint(2) DEFAULT '0' COMMENT '0: Deposit from Paypal Payments, 1: Payment to other, 2: Trade Item Add, 9: other',
  `amount` double(11,5) DEFAULT '0.00000',
  `transactionID` int(11) unsigned DEFAULT '0' COMMENT 'If the payer is paypal, then it will save transaction ID',
  `receiverBalance` double(11,5) DEFAULT '0.00000',
  `payerBalance` double(11,5) DEFAULT '0.00000',
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=195 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `forum_categories` */

DROP TABLE IF EXISTS `forum_categories`;

CREATE TABLE `forum_categories` (
  `categoryID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(255) DEFAULT NULL,
  `parentID` int(11) DEFAULT '0' COMMENT 'Parent category id',
  `topics` int(11) DEFAULT '0' COMMENT 'The number of topics',
  `replies` int(11) DEFAULT '0' COMMENT 'The number of replies',
  `lastTopicID` int(11) DEFAULT '0',
  `sortOrder` int(11) DEFAULT '0',
  `createdDate` datetime DEFAULT NULL,
  PRIMARY KEY (`categoryID`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;


/*Data for the table `forum_categories` */

insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (1,'Programming',0,0,0,0,2,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (2,'Computer Science',0,0,0,0,3,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (3,'Adobe',0,0,0,0,4,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (4,'BuckysRoom',0,0,0,0,5,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (5,'Hangout',0,0,0,0,1,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (6,'ActionScript',1,0,0,0,1,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (7,'Assembly',1,0,0,0,2,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (8,'C',1,0,0,0,3,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (9,'C#',1,0,0,0,4,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (10,'Java / Android Development',1,0,0,0,5,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (11,'Javascript',1,0,0,0,6,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (12,'Objective-C / iPhone Development',1,0,0,0,7,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (13,'Perl',1,0,0,0,8,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (14,'PHP',1,0,0,0,9,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (15,'Python',1,0,0,0,10,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (16,'Ruby',1,0,0,0,11,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (17,'Visual Basic',1,0,0,0,12,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (18,'Shell',1,0,0,0,13,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (19,'SQL & Databases',1,0,0,0,14,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (20,'All other Programming Languages',1,0,0,0,15,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (21,'3D Modeling and Animation',2,0,0,0,1,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (22,'Apple',2,0,0,0,2,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (23,'Game Design / UDK / Unity',2,0,0,0,3,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (24,'Gaming',2,0,0,0,4,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (25,'Google / YouTube',2,0,0,0,5,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (26,'Hardware',2,0,0,0,6,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (27,'HTML / CSS / Web Design',2,0,0,0,7,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (28,'Linux',2,0,0,0,8,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (29,'Microsoft',2,0,0,0,9,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (30,'Networking',2,0,0,0,10,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (31,'Video Editing / Final Cut Pro / Sony Vegas',2,3,2,127,11,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (32,'All Other Computer Related',2,1,9,87,12,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (33,'After Effects',3,1,0,49,1,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (34,'Dreamweaver',3,1,0,50,2,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (35,'Flash',3,3,9,128,3,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (36,'Illustrator',3,1,0,52,4,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (37,'InDesign',3,1,1,79,5,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (38,'Photoshop',3,2,2,111,6,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (39,'Premiere Pro',3,1,0,81,7,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (40,'All Other Adobe Products',3,1,1,82,8,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (41,'Bug Reports',4,8,13,135,1,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (42,'Suggestions',4,23,37,148,2,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (43,'General Chat',5,19,42,140,1,'2013-05-06 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (44,'C++',1,0,0,0,5,'2013-05-31 14:09:37');
insert  into `forum_categories`(`categoryID`,`categoryName`,`parentID`,`topics`,`replies`,`lastTopicID`,`sortOrder`,`createdDate`) values (45,'Introductions',5,0,0,0,2,'2013-05-31 14:09:37');

/*Table structure for table `forum_replies` */

DROP TABLE IF EXISTS `forum_replies`;

CREATE TABLE `forum_replies` (
  `replyID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `topicID` int(11) DEFAULT NULL,
  `replyContent` text,
  `creatorID` int(11) DEFAULT NULL,
  `createdDate` datetime DEFAULT NULL,
  `status` varchar(10) DEFAULT '',
  `votes` int(11) DEFAULT NULL,
  PRIMARY KEY (`replyID`)
) ENGINE=MyISAM AUTO_INCREMENT=298 DEFAULT CHARSET=latin1;

/*Table structure for table `forum_settings` */

DROP TABLE IF EXISTS `forum_settings`;

CREATE TABLE `forum_settings` (
  `settingID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `notifyRepliedToMyTopic` tinyint(2) DEFAULT '1' COMMENT 'Someone replies to my topic',
  `notifyRepliedToMyReply` tinyint(2) DEFAULT '1' COMMENT 'Someone replies to a topic that I replied to',
  `notifyMyPostApproved` tinyint(2) DEFAULT '1' COMMENT 'My post has been approved.',
  PRIMARY KEY (`settingID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Table structure for table `forum_topics` */

DROP TABLE IF EXISTS `forum_topics`;

CREATE TABLE `forum_topics` (
  `topicID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `topicTitle` varchar(500) DEFAULT NULL,
  `topicContent` text,
  `categoryID` int(11) DEFAULT NULL,
  `creatorID` int(11) DEFAULT NULL,
  `createdDate` datetime DEFAULT NULL,
  `replies` int(11) DEFAULT NULL COMMENT 'The number of Replies',
  `lastReplyID` int(11) DEFAULT NULL,
  `lastReplyDate` datetime DEFAULT NULL,
  `lastReplierID` int(11) DEFAULT NULL,
  `views` int(11) DEFAULT NULL,
  `status` varchar(10) DEFAULT 'pending',
  `votes` int(11) DEFAULT '0',
  PRIMARY KEY (`topicID`)
) ENGINE=MyISAM AUTO_INCREMENT=151 DEFAULT CHARSET=latin1;

/*Table structure for table `forum_votes` */

DROP TABLE IF EXISTS `forum_votes`;

CREATE TABLE `forum_votes` (
  `voteID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `objectID` int(11) DEFAULT NULL,
  `voterID` int(11) DEFAULT NULL,
  `voteDate` datetime DEFAULT NULL,
  `voteStatus` tinyint(2) DEFAULT '1',
  `objectType` varchar(10) DEFAULT '',
  PRIMARY KEY (`voteID`)
) ENGINE=MyISAM AUTO_INCREMENT=197 DEFAULT CHARSET=latin1;

/*Table structure for table `friends` */

DROP TABLE IF EXISTS `friends`;

CREATE TABLE `friends` (
  `friendID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `userFriendID` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0' COMMENT '0: Pending, 1: approved, -1: declined',
  PRIMARY KEY (`friendID`),
  KEY `userID_index` (`userID`) USING BTREE,
  KEY `userFriendID_index` (`userFriendID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=22314 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `friends_old` */

DROP TABLE IF EXISTS `friends_old`;

CREATE TABLE `friends_old` (
  `friendID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `userFriendID` int(11) DEFAULT NULL,
  `status` enum('pending','approved','declined') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`friendID`)
) ENGINE=MyISAM AUTO_INCREMENT=5710 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `messages` */

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `messageID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL COMMENT 'Message Owner ID',
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `subject` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `body` varchar(5000) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('read','unread') COLLATE utf8_unicode_ci NOT NULL,
  `created_date` datetime NOT NULL,
  `is_trash` tinyint(2) DEFAULT '0',
  `messageStatus` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`messageID`)
) ENGINE=MyISAM AUTO_INCREMENT=5051 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `messenger_blocklist` */

DROP TABLE IF EXISTS `messenger_blocklist`;

CREATE TABLE `messenger_blocklist` (
  `messengerBlocklistID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT '0',
  `blockedID` int(11) DEFAULT '0',
  `blockedDate` datetime DEFAULT NULL,
  PRIMARY KEY (`messengerBlocklistID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `messenger_buddylist` */

DROP TABLE IF EXISTS `messenger_buddylist`;

CREATE TABLE `messenger_buddylist` (
  `messengerBuddylistID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `buddyID` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`messengerBuddylistID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `messenger_messages` */

DROP TABLE IF EXISTS `messenger_messages`;

CREATE TABLE `messenger_messages` (
  `messageID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `buddyID` int(11) DEFAULT NULL,
  `messageType` tinyint(2) DEFAULT '0' COMMENT '0: Sent, 1: received',
  `message` text,
  `isNew` tinyint(2) DEFAULT '0',
  `createdDate` datetime DEFAULT NULL,
  PRIMARY KEY (`messageID`)
) ENGINE=MyISAM AUTO_INCREMENT=7491 DEFAULT CHARSET=utf8;

/*Table structure for table `messenger_messages_old` */

DROP TABLE IF EXISTS `messenger_messages_old`;

CREATE TABLE `messenger_messages_old` (
  `messageID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `messageOwnerID` int(11) DEFAULT NULL,
  `senderID` int(11) DEFAULT NULL,
  `receiverID` int(11) DEFAULT NULL,
  `isNew` tinyint(2) DEFAULT '0',
  `message` text,
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`messageID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `moderator` */

DROP TABLE IF EXISTS `moderator`;

CREATE TABLE `moderator` (
  `moderatorID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `moderatorType` tinyint(5) DEFAULT NULL COMMENT '1: Community, 2: Forum, 3: Trade',
  `userID` int(11) DEFAULT NULL,
  `moderatorStatus` tinyint(2) DEFAULT '0' COMMENT '0: Expired, 1: Active',
  `electedDate` datetime DEFAULT NULL,
  PRIMARY KEY (`moderatorID`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

/*Table structure for table `moderator_candidates` */

DROP TABLE IF EXISTS `moderator_candidates`;

CREATE TABLE `moderator_candidates` (
  `candidateID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `candidateType` tinyint(5) DEFAULT NULL COMMENT '1: Community, 2: Forum, 3: Trade',
  `userID` int(11) DEFAULT NULL,
  `candidateText` text,
  `votes` int(11) DEFAULT '0',
  `appliedDate` datetime DEFAULT NULL,
  PRIMARY KEY (`candidateID`)
) ENGINE=MyISAM AUTO_INCREMENT=141 DEFAULT CHARSET=latin1;

/*Table structure for table `moderator_votes` */

DROP TABLE IF EXISTS `moderator_votes`;

CREATE TABLE `moderator_votes` (
  `voteID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `voterID` int(11) DEFAULT NULL,
  `candidateID` int(11) DEFAULT NULL,
  `voteType` tinyint(2) DEFAULT '0' COMMENT '1: Approval Vote, 0: Negative Vote',
  `voteDate` datetime DEFAULT NULL,
  `voteStatus` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`voteID`)
) ENGINE=MyISAM AUTO_INCREMENT=1322 DEFAULT CHARSET=latin1;

/*Table structure for table `page_followers` */

DROP TABLE IF EXISTS `page_followers`;

CREATE TABLE `page_followers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageID` int(11) unsigned NOT NULL,
  `userID` int(11) unsigned NOT NULL COMMENT 'follower ID',
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pageID_index` (`pageID`) USING BTREE,
  KEY `userID_index` (`userID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=423 DEFAULT CHARSET=utf8;

/*Table structure for table `pages` */

DROP TABLE IF EXISTS `pages`;

CREATE TABLE `pages` (
  `pageID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) unsigned NOT NULL COMMENT 'Creator ID',
  `title` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `about` text COLLATE utf8_unicode_ci,
  `links` text COLLATE utf8_unicode_ci COMMENT 'serialized title=>link pair list',
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(2) DEFAULT '1' COMMENT '0: Inactive, 1: Active',
  PRIMARY KEY (`pageID`),
  KEY `userID_index` (`userID`) USING BTREE,
  FULLTEXT KEY `title` (`title`,`about`)
) ENGINE=MyISAM AUTO_INCREMENT=297 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `posts` */

DROP TABLE IF EXISTS `posts`;

CREATE TABLE `posts` (
  `postID` int(11) NOT NULL AUTO_INCREMENT,
  `poster` int(11) NOT NULL,
  `pageID` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '0: means it doesnot belong to a page. If it is bigger than 0, it means it belonged to a page',
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `post_date` datetime NOT NULL,
  `visibility` tinyint(10) NOT NULL DEFAULT '0' COMMENT '0: private, 1: public',
  `content` text COLLATE utf8_unicode_ci,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `youtube_url` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_profile` tinyint(2) DEFAULT '0',
  `likes` int(11) DEFAULT '0',
  `comments` int(11) DEFAULT '0',
  `post_status` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`postID`),
  KEY `poster_index` (`poster`) USING BTREE,
  KEY `pageID_index` (`pageID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7753 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `posts_comments` */

DROP TABLE IF EXISTS `posts_comments`;

CREATE TABLE `posts_comments` (
  `commentID` int(11) NOT NULL AUTO_INCREMENT,
  `postID` int(11) NOT NULL,
  `commenter` int(11) NOT NULL,
  `content` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `posted_date` datetime NOT NULL,
  `commentStatus` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`commentID`),
  KEY `postID_index` (`postID`) USING BTREE,
  KEY `commenter_index` (`commenter`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=12034 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `posts_hits` */

DROP TABLE IF EXISTS `posts_hits`;

CREATE TABLE `posts_hits` (
  `hitID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `postID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `hitDate` datetime DEFAULT NULL,
  PRIMARY KEY (`hitID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `posts_likes` */

DROP TABLE IF EXISTS `posts_likes`;

CREATE TABLE `posts_likes` (
  `likeID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `postID` int(11) NOT NULL,
  `liked_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `likeStatus` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`likeID`)
) ENGINE=MyISAM AUTO_INCREMENT=15587 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `reports` */

DROP TABLE IF EXISTS `reports`;

CREATE TABLE `reports` (
  `reportID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `reporterID` int(11) DEFAULT NULL,
  `objectID` int(11) DEFAULT NULL,
  `objectType` varchar(20) DEFAULT NULL COMMENT 'post, comment, message',
  `reportedDate` datetime DEFAULT NULL,
  `reportStatus` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`reportID`)
) ENGINE=MyISAM AUTO_INCREMENT=147 DEFAULT CHARSET=latin1;

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `sessionID` char(32) NOT NULL DEFAULT '',
  `userID` int(11) DEFAULT '0',
  `expiry` int(11) DEFAULT NULL,
  `value` mediumtext,
  PRIMARY KEY (`sessionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `stats_post` */

DROP TABLE IF EXISTS `stats_post`;

CREATE TABLE `stats_post` (
  `postID` int(11) unsigned NOT NULL,
  `postType` varchar(10) DEFAULT NULL,
  `sortOrder` int(10) DEFAULT '0',
  `createdDate` datetime DEFAULT NULL,
  PRIMARY KEY (`postID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `temp_users` */

DROP TABLE IF EXISTS `temp_users`;

CREATE TABLE `temp_users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `lastName` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `activation` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tracker` */

DROP TABLE IF EXISTS `tracker`;

CREATE TABLE `tracker` (
  `trackID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT '0',
  `ipAddr` varchar(255) DEFAULT NULL,
  `trackedTime` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT '',
  PRIMARY KEY (`trackID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `trade` */

DROP TABLE IF EXISTS `trade`;

CREATE TABLE `trade` (
  `tradeID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sellerID` int(11) unsigned NOT NULL,
  `buyerID` int(11) unsigned NOT NULL,
  `sellerItemID` int(11) unsigned NOT NULL,
  `buyerItemID` int(11) unsigned NOT NULL,
  `sellerShippingID` int(11) unsigned NOT NULL,
  `buyerShippingID` int(11) unsigned NOT NULL,
  `sellerTrackingNo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `buyerTrackingNo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(2) DEFAULT '1' COMMENT '1: traded',
  PRIMARY KEY (`tradeID`),
  KEY `sellerID_index` (`sellerID`) USING BTREE,
  KEY `buyerID_index` (`buyerID`) USING BTREE,
  KEY `sellerItemID_index` (`sellerItemID`) USING BTREE,
  KEY `buyerItemID_index` (`buyerItemID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `trade_categories` */

DROP TABLE IF EXISTS `trade_categories`;

CREATE TABLE `trade_categories` (
  `catID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parentID` int(11) DEFAULT '0',
  `status` tinyint(2) DEFAULT '1' COMMENT '0: disable, 1: enable',
  PRIMARY KEY (`catID`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `trade_feedbacks` */

DROP TABLE IF EXISTS `trade_feedbacks`;

CREATE TABLE `trade_feedbacks` (
  `feedbackID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tradeID` int(11) unsigned NOT NULL,
  `sellerID` int(11) unsigned NOT NULL COMMENT 'seller means the owner of item listed',
  `buyerID` int(11) unsigned NOT NULL COMMENT 'buyer means the man who offered',
  `buyerToSellerScore` int(11) DEFAULT '0' COMMENT '0: No score 1: positive, -1: negative',
  `buyerToSellerFeedback` text COLLATE utf8_unicode_ci,
  `buyerToSellerFeedbackCreatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sellerToBuyerScore` int(11) DEFAULT '0' COMMENT '0: No score 1: positive, -1: negative',
  `sellerToBuyerFeedback` text COLLATE utf8_unicode_ci,
  `sellerToBuyerFeedbackCreatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`feedbackID`),
  KEY `tradeID_index` (`tradeID`) USING BTREE,
  KEY `sellerID_index` (`sellerID`) USING BTREE,
  KEY `buyerID_index` (`buyerID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `trade_items` */

DROP TABLE IF EXISTS `trade_items`;

CREATE TABLE `trade_items` (
  `itemID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) unsigned NOT NULL,
  `title` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `subtitle` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `itemWanted` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `catID` int(11) unsigned NOT NULL,
  `locationID` int(11) unsigned DEFAULT NULL,
  `images` text COLLATE utf8_unicode_ci,
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(2) DEFAULT '1' COMMENT '0: Inactive, 1: ACTIVE, 2: Traded',
  PRIMARY KEY (`itemID`),
  KEY `userID_index` (`userID`) USING BTREE,
  KEY `catID_index` (`catID`) USING BTREE,
  KEY `locationID_index` (`locationID`) USING BTREE,
  FULLTEXT KEY `title` (`title`,`subtitle`,`description`)
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `trade_offers` */

DROP TABLE IF EXISTS `trade_offers`;

CREATE TABLE `trade_offers` (
  `offerID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `targetItemID` int(11) unsigned NOT NULL,
  `offeredItemID` int(11) unsigned NOT NULL,
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastUpdateDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'it will save the last status change date',
  `targetHideDeclined` tinyint(2) DEFAULT '0' COMMENT 'The man who declines this offer removed this after declining. 0: show, 1: hide',
  `offeredHideDeclined` tinyint(2) DEFAULT '0' COMMENT 'The man who offered this offer removed this after being declined. 0: show, 1: hide',
  `status` tinyint(2) DEFAULT '1' COMMENT '0: Inactive, 1: Active, 2: Declined',
  `isNew` tinyint(2) DEFAULT '1' COMMENT '0: not new, 1: new',
  PRIMARY KEY (`offerID`),
  KEY `targetItemID_index` (`targetItemID`) USING BTREE,
  KEY `offeredItemID_index` (`offeredItemID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `trade_shipping_info` */

DROP TABLE IF EXISTS `trade_shipping_info`;

CREATE TABLE `trade_shipping_info` (
  `shippingID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `countryID` int(11) unsigned DEFAULT NULL,
  `zip` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`shippingID`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `trade_users` */

DROP TABLE IF EXISTS `trade_users`;

CREATE TABLE `trade_users` (
  `userID` int(11) unsigned NOT NULL,
  `totalRating` int(11) unsigned DEFAULT '0' COMMENT 'count of feedback, cronjob will update this automatically',
  `positiveRating` int(11) unsigned DEFAULT '0' COMMENT 'Positive rating count',
  `shippingAddress` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shippingCity` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shippingState` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shippingZip` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shippingCountryID` int(11) unsigned DEFAULT NULL,
  `optOfferReceived` tinyint(2) DEFAULT '1' COMMENT '0: disagree; 1: agree that you receive notification when someone makes me an offer',
  `optOfferAccepted` tinyint(2) DEFAULT '1' COMMENT '0: disagree; 1: agree that you receive notification when someone accepts my offer',
  `optOfferDeclined` tinyint(2) DEFAULT '1' COMMENT '0: disagree; 1: agree that you receive notification when someone declines my offer',
  `optFeedbackReceived` tinyint(2) DEFAULT '1' COMMENT '0: disagree; 1: agree that you receive notification when someone declines my offer',
  PRIMARY KEY (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `transactions` */

DROP TABLE IF EXISTS `transactions`;

CREATE TABLE `transactions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) unsigned NOT NULL,
  `payer_email` varchar(255) DEFAULT NULL COMMENT 'payer email address',
  `amount` double(11,5) NOT NULL,
  `currency` varchar(5) DEFAULT NULL,
  `trackNumber` varchar(500) DEFAULT NULL COMMENT 'You may read paypal transaction id from here',
  `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `user_acl` */

DROP TABLE IF EXISTS `user_acl`;

CREATE TABLE `user_acl` (
  `aclID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Level` tinyint(11) DEFAULT '0',
  PRIMARY KEY (`aclID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `lastName` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `email_visibility` tinyint(2) DEFAULT '0',
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `gender` varchar(10) COLLATE utf8_unicode_ci DEFAULT '',
  `gender_visibility` tinyint(2) DEFAULT '0',
  `birthdate` date DEFAULT '0000-00-00',
  `birthdate_visibility` tinyint(2) DEFAULT '0',
  `relationship_status` tinyint(2) DEFAULT '0' COMMENT '1: Single, 2: In a Relation',
  `relationship_status_visibility` tinyint(2) DEFAULT '0',
  `religion` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `religion_visibility` tinyint(2) DEFAULT '0',
  `political_views` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `political_views_visibility` tinyint(2) DEFAULT '0',
  `birthplace` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `birthplace_visibility` varchar(255) COLLATE utf8_unicode_ci DEFAULT '0',
  `current_city` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `current_city_visibility` tinyint(2) DEFAULT '0',
  `home_phone` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `home_phone_visibility` tinyint(2) DEFAULT '0',
  `cell_phone` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `cell_phone_visibility` tinyint(2) DEFAULT '0',
  `work_phone` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `work_phone_visibility` tinyint(2) DEFAULT '0',
  `address1` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `address2` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `state` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `zip` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `address_visibility` tinyint(2) DEFAULT '0',
  `token` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  `status` tinyint(2) DEFAULT '0',
  `messenger_privacy` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'all',
  `show_messenger` tinyint(2) DEFAULT '0',
  `timezone` varchar(255) COLLATE utf8_unicode_ci DEFAULT '(UTC) Coordinated Universal Time',
  `timezone_visibility` tinyint(2) DEFAULT '0',
  `attributes` varchar(5000) COLLATE utf8_unicode_ci DEFAULT '',
  `user_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'Registered',
  `user_acl_id` int(11) DEFAULT '2',
  `credits` double(11,5) DEFAULT '0.00000',
  PRIMARY KEY (`userID`),
  FULLTEXT KEY `firstName` (`firstName`,`lastName`)
) ENGINE=MyISAM AUTO_INCREMENT=5712 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `users_contact` */

DROP TABLE IF EXISTS `users_contact`;

CREATE TABLE `users_contact` (
  `userID` int(11) NOT NULL,
  `contact_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `contact_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `visibility` tinyint(2) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`,`order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `users_educations` */

DROP TABLE IF EXISTS `users_educations`;

CREATE TABLE `users_educations` (
  `userID` int(11) NOT NULL,
  `school` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `visibility` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0: Private, 1: Public',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`,`order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `users_employments` */

DROP TABLE IF EXISTS `users_employments`;

CREATE TABLE `users_employments` (
  `userID` int(11) NOT NULL,
  `employer` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `visibility` tinyint(2) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`,`order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `users_links` */

DROP TABLE IF EXISTS `users_links`;

CREATE TABLE `users_links` (
  `userID` int(11) NOT NULL,
  `title` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `visibility` tinyint(2) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`,`order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `users_token` */

DROP TABLE IF EXISTS `users_token`;

CREATE TABLE `users_token` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `userToken` varchar(255) DEFAULT NULL,
  `tokenDate` int(11) DEFAULT NULL,
  `tokenType` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
