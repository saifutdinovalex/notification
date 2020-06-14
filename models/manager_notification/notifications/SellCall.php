<?php
namespace backend\models\manager_notification\notifications;

use Yii;
use backend\models\manager_notification\KeyNotification;
use backend\models\manager_notification\ModalNotification;
use backend\models\manager_notification\ModalPriority;


class SellCall implements KeyNotification, ModalNotification, ModalPriority
{
    const KEY_SELL_CALL_5M = 'sell_call_5m';

    public static function getKeys()
    {
        return array_values((new \ReflectionClass(static::class))->getConstants());
    }

    public static function _getTitleName($key)
    {
        switch ($key) {
            case static::KEY_SELL_CALL_5M:
                return \Yii::t('admin','Call reminder');
            break;
        }
        return '';
    }

    public static function _getDescription($model)
    {
        switch ($model->key) {
            case static::KEY_SELL_CALL_5M:
                return \Yii::t('admin','Call reminder');;
            break;
           
        }
        return '';
    }

    public static function _getRoute($key, $key_id, $id)
    {
        switch ($key) {
            case static::KEY_SELL_CALL_5M:
                return ['/user/view', 'id' => $key_id];
            break;
        };

        return '';
    }

  
}
