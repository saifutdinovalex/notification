<?php
namespace backend\models\manager_notification;

use Yii;

interface  KeyNotification
{
	public static function getKeys();
	public static function _getTitleName($key);
	public static function _getDescription($model);
	public static function _getRoute($key, $key_id, $id);
	//public static function getBellNotification();
}