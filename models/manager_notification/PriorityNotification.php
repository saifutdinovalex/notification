<?php
namespace backend\models\manager_notification;

use Yii;
use backend\models\user\Status;

class PriorityNotification
{
    private $user_id;
    private $key;
    private $_notification;
    private $current_notification = [];

    public function setUserId($value) 
    {
        $this->user_id = $value;
    }

    public function setKey($value) 
    {
        $this->key = $value;
    }

    public function conditionPriority()
    {
        return [
                    'sell_status' => [
                        0 => Status::WAIT_PAYMENT,
                        1 => Status::WARM,
                    ]
                ];
    }

    public function build()
    {
        $models = $this->getAllNotification();
        if (!$models) return;

        foreach ($models as $key => $value) {
            if ($key == 0) {
                $this->current_notification = $value;
            }

            if ($this->isCheck($value)) {
                if ($object = $value->usersData) {
                    $this->isPriority($object, $value);
                }
            }
        }
    }

    public function getNotification()
    {
        if ($notification = $this->checkNotification()) {
            return $notification;
        }
        return $this->current_notification;
    }

    protected function getAllNotification()
    {
        $model = \backend\components\Notification::find()
            ->where(['user_id' => $this->user_id])
            ->andWhere(["seen" => 0])
            ->andWhere(['<=', 'created_at', date('Y-m-d H:i:s')])
            ->orderBy('created_at DESC');

        if ($this->key) {
            $model->andWhere(['key' => $this->key]);
        }
        return $model->all();
    }

    protected function isCheck($model)
    {
        if ($model && !empty($model['is_check'])) {
            $result = \backend\modules\notifications\models\NotificationCheck::getDefinition($model);
            return !$result;
        }
        return true;
    }

    
    protected function isPriority($user, $notif)
    {
        foreach ($this->conditionPriority() as $attr => $condition) {
            foreach ($condition as $key => $value) {
                if ($user->{$attr} == $value && !$this->_notification) {
                    if (!$this->_notification) {
                        $this->_notification[$key] = $notif;    
                    } elseif (!array_key_exists($key, $this->_notification)) {
                        $this->_notification[$key] = $notif;    
                    }
                }    
            }
        }
    }

    protected function checkNotification()
    {
        if (!$this->_notification) return false;
        foreach ($this->conditionPriority() as $attr => $condition) {
            foreach ($condition as $key => $value) {
                if (array_key_exists($key, $this->_notification)) {
                    return $this->_notification[$key];
                }        
            }    
        }
        return false;
    }
}