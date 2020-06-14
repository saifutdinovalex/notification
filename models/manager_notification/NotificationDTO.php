<?php
namespace backend\models\manager_notification;

use Yii;

trait NotificationDTO
{
	public static function getStringSearchNotification($keys)
    {
        $new = [];
        foreach ($keys as $key => $value) {
            $new[] = 'Search[key][]='.$value;
        }
        $new[] = 'Search[seen]=0';
        return implode('&', $new);
    }
 
}