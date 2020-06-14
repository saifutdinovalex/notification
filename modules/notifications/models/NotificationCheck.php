<?php

namespace backend\modules\notifications\models;

use Yii;
use backend\models\manager_notification\notifications\QueryJournalist;
use backend\models\manager_notification\notifications\SellCall;

class NotificationCheck extends \backend\components\Notification
{
    public static function getDefinition($model)
    {
        $status = false;
        try{
            switch ($model->key) {
                case QueryJournalist::KEY_QUERY_DEADLINE_END:
                    if ($model->userData->status == 0) {
                        $status = false;    
                    } else {
                        $status = true;
                    }
                    
                break;
          
                default:
                    $status = false;
                    break;
            }
        }catch(\Exception $e){
            \Yii::debug($e);
        }

        self::deleteModel($status, $model);
        return $status;
    } 

    public static function deleteModel($status, $model)
    {
        if ($status === true)
        {
            $model->delete();
        }
    }
}

