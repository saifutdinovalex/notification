<?php

namespace backend\models;

use backend\components\behaviors\NotificationAddBehavior;

use backend\models\manager_notification\notifications\AppointedManager;
use backend\models\manager_notification\notifications\SellCall;

class Users extends ActiveRecord
{

    public function behaviors()
    {
        return [
            
            'notification_manager' => [
                'class' => NotificationAddBehavior::class,
                'argument' =>
                [
                    [
                        'field' => 'manager_id',
                        'key' => AppointedManager::KEY_MANAGER_APPOINTED
                    ],
                    [
                        'field' => 'sell_date_call',
                        'key' => SellCall::KEY_SELL_CALL_5M,
                        'time_before' => 5 * 60,
                        'is_check' => 1,
                    ],
                ]
            ],
           
        ];
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'user';
    }


    public function getUserIdNotification($key)
    {
        switch ($key) {
            case  AppointedManager::KEY_MANAGER_APPOINTED:
            case  SellCall::KEY_SELL_CALL_5M:
                return $this->user_id;
                break;

            default:
                return 0;
                break;
        }
    }

    public function getManagerId($key)
    {
        switch ($key) {
            case  AppointedManager::KEY_MANAGER_APPOINTED:
            case  SellCall::KEY_SELL_CALL_5M:
                return [$this->manager_id];
                break;

            default:
                return 0;
                break;
        }
    }

}
