<?php
/**
* Test Todoist-RESTAPI
* Todoist Developer REST-API
* Authorization: Token
*
* Author: Syc <syc@bilibili.de>
* Version: 20180212
* GNU General Public License V3.0
*
*/

//require Libarary
require_once('./Todoist.class.php');

//Simple
$Todoist = new Todoist('your_token');
$Todoist->project()->get();
