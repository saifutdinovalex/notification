<?php

namespace backend\modules\notifications\models;

use Yii;
use backend\models\query\Status;
use backend\models\manager_notification\notifications\QueryJournalist;
use backend\models\manager_notification\notifications\SellCall;

class NotificationCheck extends \backend\components\Notification
{
    public static function getDefinition($model)
    {
        $status = false;
        try{
            switch ($model->key) {
                //сми топ или отраслевой, запрос принят модератором и есть хотя бы один питч и не должно быть принятных питчей журом
                case QueryJournalist::KEY_QUERY_DEADLINE_66_NOT_ACCEPT:
                    if($model->queryData->pFSmiData->isTopOrIndustry() && $model->queryData->moderate == Status::CODES[Status::MODERATED] && $model->queryData->pitch){
                        $status = $model->queryData->pitchAccepted;
                    }else{
                        $status = true;
                    }
                break;
                //не дожно быть питчей
                case QueryJournalist::KEY_QUERY_DEADLINE_2_3_NOT_ANSWER:
                    if($model->queryData->pFSmiData->isTopOrIndustry()  && $model->queryData->moderate == Status::CODES[Status::MODERATED]){
                        $status = $model->queryData->pitch;    
                    }else{
                        $status = true;
                    }
                break;
                //не должно быть принятых питчей от жура
                case QueryJournalist::KEY_QUERY_DEADLINE_END:
                    if($model->queryData->pFSmiData->isTopOrIndustry() && $model->queryData->moderate == Status::CODES[Status::MODERATED]){
                        $status = $model->queryData->pitchAccepted;    
                    }else{
                        $status = true;
                    }
                    
                break;
                //не дожно быть публикаций
                case QueryJournalist::KEY_PUBLICATION_END:    
                    if($model->queryData->pFSmiData->isTopOrIndustry() && $model->queryData->moderate == Status::CODES[Status::MODERATED])
                    {
                        $status = !$model->queryData->unknownPublication;
                    }else{
                        $status = true;
                    }
                break;
                case SellCall::KEY_SELL_CALL_5M:
                    $status = $model->usersData->sell_date_call === null;
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

