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

    const KEY_QUERY_DEADLINE_66_NOT_ACCEPT = 'query_deadline_66_not_accept';
    const KEY_QUERY_DEADLINE_END = 'query_deadline_end';
    const KEY_PUBLICATION_END = 'publication_end';
    const KEY_QUERY_DEADLINE_2_3_NOT_ANSWER = 'query_deadline_2_3_not_answer';

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
             case self::KEY_QUERY_DEADLINE_66_NOT_ACCEPT:
                return \Yii::t('admin','the request has gone 2/3 of the deadline, but there are no accepted answers');
            break;
            case self::KEY_QUERY_DEADLINE_END:
                return \Yii::t('admin','request deadline passed, but no accepted answers');
            break;
            case self::KEY_PUBLICATION_END:
                return \Yii::t('admin','the request publication date has passed, but the publication is not attached');
            break;
            case self::KEY_QUERY_DEADLINE_2_3_NOT_ANSWER:
                return \Yii::t('admin','the request has gone 2/3 of the deadline, but there are no answers');
            break;
        }
        return '';
    }

    public static function _getDescription($model)
    {
        switch ($model->key) {
            case self::KEY_QUERY_DEADLINE_66_NOT_ACCEPT:
                return \Yii::t('admin','the request has gone 2/3 of the deadline, but there are no accepted answers').', '.$model->getQueryUserName().', '.$model->getQuerySmiName();
            break;
            case self::KEY_QUERY_DEADLINE_END:
                return \Yii::t('admin','request deadline passed, but no accepted answers').', '.$model->getQueryUserName().', '.$model->getQuerySmiName();
            break;
            case self::KEY_PUBLICATION_END:
                return \Yii::t('admin','the request publication date has passed, but the publication is not attached').', '.$model->getQueryUserName().', '.$model->getQuerySmiName();
            break;
            case self::KEY_QUERY_DEADLINE_2_3_NOT_ANSWER:
                return \Yii::t('admin','the request has gone 2/3 of the deadline, but there are no answers').', '.$model->getQueryUserName().', '.$model->getQuerySmiName();
            break;
        }
    }

    public static function _getRoute($key, $key_id, $id)
    {
        switch ($key) {
            case self::KEY_QUERY_DEADLINE_66_NOT_ACCEPT:
            case self::KEY_QUERY_DEADLINE_END:
            case self::KEY_PUBLICATION_END:
            case self::KEY_QUERY_DEADLINE_2_3_NOT_ANSWER:
                return \Yii::getAlias('@pressfeed/query/'.$key_id);
            break;
        };

        return '';
    }

    public static function getBellNotification()
    {
        return self::getStringSearchNotification(static::getKeys());
    }

    public function getQueryUserName()
    {
       return ($this->queryData->usersData)?$this->queryData->usersData->fullname:'';
    }

    public function getQuerySmiName()
    {
        return ($this->queryData->pFSmiData)?$this->queryData->pFSmiData->name:'';
    }
}