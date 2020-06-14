<?php
namespace backend\components\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use backend\modules\notifications\models\Notification;

class NotificationAddBehavior extends Behavior
{
    
    /*
        field - models field add notification
        key - key notification
        time_before - вычесть кол-во секунд из текущей даты сохранения и сохранить данное значение в напоминание
        is_check - если надо проверять существование события в момент достижения времени события
    */
    public $argument = [];

    protected $_key = [];
    protected $_field = [];
    protected $_time_before = [];
    protected $_is_check = [];
    protected $_changeAttr = [];
    protected $duplicate = [];
    protected $oldAttributes;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'toGo',
            ActiveRecord::EVENT_AFTER_UPDATE =>'toGo'
        ];
    }

    public function toGo($event)
    {

        $this->initData();
        if ($this->changeAttribute($event)) return;
        foreach ($this->_key as $key => $value) 
        {
            if (!in_array($this->_field[$key], $this->_changeAttr)) continue;

            if ($this->duplicate[$key]) {
                \Yii::$app->getModule('notifications')->allowDuplicate = true;
            }

            if (!$user_id = $this->userId($value)){
                \Yii::debug('KEY id is not exist '.get_class($this->owner));
                continue;  
            }
            if (!$this->exceptions($key)) {
                if ($managers = $this->managerId($value)) {
                    foreach ($managers as $manager_id) {
                        \backend\components\Notification::notify($value, $manager_id, $user_id, Notification::TYPE_DEFAULT, $this->_time_before[$key], $this->_is_check[$key]);
                    }
                }
            }
        }
    }

    protected function initData()
    {
        foreach ($this->argument as $key => $value) {
            $this->_key[$key] = $value['key'];
            $this->_field[$key] = $value['field'];
            $this->_time_before[$key] = $this->calculateTimeBefore($value);
            $this->_is_check[$key] = (isset($value['is_check']))?1:0;
            $this->duplicate[$key] = isset($value['duplicate'])?1:0;
        }
    }

    protected function calculateTimeBefore($value)
    {
        if (isset($value['time_before']) && $value['time_before'] > 0 && isset($this->owner->{$value['field']})) {
            $date = new \DateTime($this->owner->{$value['field']});
            $date->sub(new \DateInterval('PT' . $value['time_before'] . 'S'));
            return $date->format('Y-m-d H:i:s');
        }

        return null;
    }

    protected function changeAttribute($event)
    {

        $old_array = $event->changedAttributes;
        $this->oldAttributes = $old_array;
        if (empty($old_array)) return true;

        $old = array_keys($old_array);
        
        $this->_changeAttr = array_intersect($this->_field, $old);
        if (empty($this->_changeAttr)) return true;
        
        return false;
    }

    protected function userId($key)
    {
        if (method_exists($this->owner, 'getUserIdNotification')) {
            return $this->owner->getUserIdNotification($key);
        }

        return 0;
    }
    
    protected function managerId($key)
    {
        if (method_exists($this->owner, 'getManagerId')) {
            $m = $this->owner->getManagerId($key);
            if (is_array($m)) return $m;
            return [$m];
        }

        return 0;
    }

    protected function exceptions($key)
    {
        switch ($this->_field[$key]) {
            case 'sell_date_call':
                return $this->owner->{$this->_field[$key]} == null;
                break;
            
            default:
                return false;
                break;
        }
    }
}
