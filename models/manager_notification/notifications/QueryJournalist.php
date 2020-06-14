<?php
namespace backend\models\manager_notification\notifications;

use Yii;
use backend\models\manager_notification\relation_ar\ManagerRelation;
use backend\models\manager_notification\NotificationDTO;
use backend\models\manager_notification\KeyNotification;
use backend\models\manager_notification\BellsNotification;

class QueryJournalist implements KeyNotification, BellsNotification
{
    use ManagerRelation, NotificationDTO;

    const KEY_QUERY_DEADLINE_END = 'query_deadline_end';

    public static function getNameBell()
    {
        return 'smi_deadline';
    }
    
    public static function getKeys()
    {
        return array_values((new \ReflectionClass(static::class))->getConstants());
    }

    public static function _getTitleName($key)
    {
        switch ($key) {
     
            case self::KEY_QUERY_DEADLINE_END:
                return \Yii::t('admin','request deadline passed, but no accepted answers');
            break;
        }
        return '';
    }

    public static function _getDescription($model)
    {
        switch ($model->key) {
           
            case self::KEY_QUERY_DEADLINE_END:
                return \Yii::t('admin','request deadline passed, but no accepted answers');
            break;
        }
    }

    public static function _getRoute($key, $key_id, $id)
    {
        switch ($key) {
            case self::KEY_QUERY_DEADLINE_END:
                return ['/query/index'];
            break;
        };

        return '';
    }

    public static function getBellNotification()
    {
        return self::getStringSearchNotification(static::getKeys());
    }
}
