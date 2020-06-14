<?php
namespace backend\models\manager_notification\notifications;

use Yii;
use backend\models\manager_notification\KeyNotification;
use backend\models\manager_notification\BellsNotification;
use backend\models\manager_notification\ModalNotification;
use backend\models\manager_notification\conditions\PrAccount;
use backend\models\manager_notification\NotificationDTO;
use backend\models\manager_notification\relation_ar\ManagerRelation;

class ProfAccount implements KeyNotification, BellsNotification, ModalNotification
{
    use NotificationDTO, ManagerRelation;

    const USER_NOT_VISITED_6_12 = 'user_not_visited_6_12';

    public static function getNameBell()
    {
        return 'pf_account';
    }
    
    public static function getKeys()
    {
        return array_values((new \ReflectionClass(static::class))->getConstants());
    }

    public static function _getTitleName($key)
    {
        switch ($key) {
            case static::USER_NOT_VISITED_6_12:
                return \Yii::t('admin','The {user} does not go to the service for 1 month or more', ['user' => 'Пользователь']);
            break;
        }
        return '';
    }

    public static function _getDescription($model)
    {
        switch ($model->key) {
            case static::USER_NOT_VISITED_6_12:
                return \Yii::t('admin','The {user} does not go to the service for 1 month or more', ['user' => $model->usersData->fullname]);
            break;
        }
    }

    public static function _getRoute($key, $key_id, $id)
    {
        switch ($key) {
            case static::USER_NOT_VISITED_6_12:
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
