<?php
namespace backend\models\manager_notification\notifications;

use Yii;
use backend\models\manager_notification\KeyNotification;
use backend\models\manager_notification\BellsNotification;
use backend\models\manager_notification\ModalNotification;
use backend\models\invoices\PeriodTypes;
use backend\models\manager_notification\conditions\PrAccount;
use backend\models\manager_notification\NotificationDTO;
use backend\models\manager_notification\relation_ar\ManagerRelation;

class ProfAccount implements KeyNotification, BellsNotification, ModalNotification
{
    use NotificationDTO, ManagerRelation;

    const USER_NOT_VISITED_6_12 = 'user_not_visited_6_12';
    const USER_NOT_VISITED_1_3 = 'user_not_visited_1_3';
    const USER_NOT_CREATE_PITCH_6_12 = 'user_not_create_pitch_6_12';
    const USER_NOT_CREATE_PITCH_1_3 = 'user_not_create_pitch_1_3';
    const USER_NOT_ACCEPTED_PITCH_6_12 = 'user_not_accepted_pitch_6_12';
    const USER_NOT_ACCEPTED_PITCH_1_3 = 'user_not_accepted_pitch_1_3';

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
            case static::USER_NOT_VISITED_1_3:
                return \Yii::t('admin','The {user} does not go to the service for 2 weeks', ['user' => 'Пользователь']);
            break;
            case static::USER_NOT_CREATE_PITCH_6_12:
                return \Yii::t('admin','{User} does not leave pitches for more than 2 weeks', ['User' => 'Пользователь']);
            break;
            case static::USER_NOT_CREATE_PITCH_1_3:
                return \Yii::t('admin','{User} does not leave pitches for more than 1 week', ['User' => 'Пользователь']);
            break;
            case static::USER_NOT_ACCEPTED_PITCH_6_12:
                return \Yii::t('admin','{User} has no accepted pitches for 1 month or more', ['User' => 'пользователя']);
            break;
            case static::USER_NOT_ACCEPTED_PITCH_1_3:
                return \Yii::t('admin','{User} has no accepted pitches for 2 weeks or more', ['User' => 'пользователя']);
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
            case static::USER_NOT_VISITED_1_3:
                return \Yii::t('admin','The {user} does not go to the service for 2 weeks', ['user' => $model->usersData->fullname]);
            break;
            case static::USER_NOT_CREATE_PITCH_6_12:
                return \Yii::t('admin','{User} does not leave pitches for more than 2 weeks', ['User' => $model->usersData->fullname]);
            break;
            case static::USER_NOT_CREATE_PITCH_1_3:
                return \Yii::t('admin','{User} does not leave pitches for more than 1 week', ['User' => $model->usersData->fullname]);
            break;
            case static::USER_NOT_ACCEPTED_PITCH_6_12:
                return \Yii::t('admin','{User} has no accepted pitches for 1 month or more', ['User' => $model->usersData->fullname]);
            break;
            case static::USER_NOT_ACCEPTED_PITCH_1_3:
                return \Yii::t('admin','{User} has no accepted pitches for 2 weeks or more', ['User' => $model->usersData->fullname]);
            break;
        }
    }

    public static function _getRoute($key, $key_id, $id)
    {
        switch ($key) {
            case static::USER_NOT_VISITED_6_12:
            case static::USER_NOT_VISITED_1_3:
            case static::USER_NOT_CREATE_PITCH_6_12:
            case static::USER_NOT_CREATE_PITCH_1_3:
            case static::USER_NOT_ACCEPTED_PITCH_6_12:
            case static::USER_NOT_ACCEPTED_PITCH_1_3:
                return ['/user/view', 'user_id' => $key_id];
            break;
        };

        return '';
    }

    public static function getBellNotification()
    {
        return self::getStringSearchNotification(static::getKeys());
    }

    public static function getKeyEscort($period, $const)
    {
        return self::getConstPeriodKey()[$period][$const];
    }

    protected static function getConstPeriodKey()
    {
        return [
            PeriodTypes::ONE => [
                PrAccount::VISITED_AT => static::USER_NOT_VISITED_1_3,
                PrAccount::CREATE_PITCH_AT => static::USER_NOT_CREATE_PITCH_1_3,
                PrAccount::ACCEPTED_PITCH_AT => static::USER_NOT_ACCEPTED_PITCH_1_3, 
            ],
            PeriodTypes::THREE => [
                PrAccount::VISITED_AT => static::USER_NOT_VISITED_1_3,
                PrAccount::CREATE_PITCH_AT => static::USER_NOT_CREATE_PITCH_1_3,
                PrAccount::ACCEPTED_PITCH_AT => static::USER_NOT_ACCEPTED_PITCH_1_3, 
            ],
            PeriodTypes::SIX => [
                PrAccount::VISITED_AT => static::USER_NOT_VISITED_6_12,
                PrAccount::CREATE_PITCH_AT => static::USER_NOT_CREATE_PITCH_6_12,
                PrAccount::ACCEPTED_PITCH_AT => static::USER_NOT_ACCEPTED_PITCH_6_12, 
            ],
            PeriodTypes::TWELVE => [
                PrAccount::VISITED_AT => static::USER_NOT_VISITED_6_12,
                PrAccount::CREATE_PITCH_AT => static::USER_NOT_CREATE_PITCH_6_12,
                PrAccount::ACCEPTED_PITCH_AT => static::USER_NOT_ACCEPTED_PITCH_6_12, 
            ]
        ];
    }
}