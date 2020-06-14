<?php
namespace backend\models\manager_notification\notifications;

use Yii;
use backend\models\manager_notification\NotificationDTO;
use backend\models\manager_notification\KeyNotification;
use backend\models\manager_notification\BellsNotification;

class AppointedManager implements KeyNotification, BellsNotification
{
    use NotificationDTO;

    const KEY_MANAGER_APPOINTED = 'manager_appointed';

    public static function getNameBell()
    {
        return 'manager_appointed';
    }

    public static function getKeys()
    {
        return array_values((new \ReflectionClass(static::class))->getConstants());
    }

    public static function _getTitleName($key)
    {
        switch ($key) {
            case self::KEY_MANAGER_APPOINTED:
                return Yii::t('admin', 'User is appointed');
            break;
        }
        return '';
    }

    public static function _getDescription($model)
    {
        switch ($model->key) {
            case self::KEY_MANAGER_APPOINTED:
                return Yii::t('admin', '{user} is appointed for you', [
                    'user' => $model->usersData->fullname
                ]);
            break;
        }
        return '';
    }

    public static function _getRoute($key, $key_id, $id)
    {
        switch ($key) {
            case self::KEY_MANAGER_APPOINTED:
                return ['/user/view', 'user_id' => $key_id];
            break;
        };

        return '';
    }

    public static function getBellNotification()
    {
        return self::getStringSearchNotification(static::getKeys());
    }
}