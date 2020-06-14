<?php
/**
 * Created by PhpStorm.
 * User: Sergej
 * Date: 08.10.2018
 * Time: 11:42
 */

namespace backend\components;

use Yii;
use backend\modules\notifications\models\Notification as BaseNotification;
use yii\helpers\Url;
use backend\models\manager_notification\notifications\ProfAccount;
use backend\models\manager_notification\notifications\QueryJournalist;
use backend\models\manager_notification\notifications\PitchRestriction;
use backend\models\manager_notification\notifications\AppointedManager;
use backend\models\manager_notification\notifications\SellCall;

use backend\models\manager_notification\relation_ar\ManagerRelation;

class Notification extends BaseNotification
{
    use ManagerRelation;

    public static function getKeys()
    {
        $result = [];
        foreach (self::getClassNotifications() as $key => $value) {
            $result = array_merge($value::getKeys(), $result);
        }

        return $result;
    }
    //все классы
    public static function getClassNotifications()
    {
        return [new ProfAccount, new QueryJournalist, new PitchRestriction , new AppointedManager, new SellCall];
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return self::_getTitle($this);
    }

    public static function _getTitle($model)
    {
        return self::_getDescription($model);
    }

    public static function _getTitleName($key)
    {   
        foreach (self::getClassNotifications() as  $value) {
            if ($pr_ac = $value::_getTitleName($key)) {
                return $pr_ac;
            }    
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getDescription() {
        
        return self::_getDescription($this);
    }

    public static function _getDescription($model)
    {
        foreach (self::getClassNotifications() as  $value) {
            if ($pr_ac = $value::_getDescription($model)) {
                return $pr_ac;
            }    
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getRoute()
    {
        return self::_getRoute($this->key, $this->key_id, $this->id);
    }

    public static function _getRoute($key, $key_id, $id)
    {
        foreach (self::getClassNotifications() as  $value) {
            if (is_subclass_of($value, '\backend\models\manager_notification\BellsNotification') && $pr_ac = $value::_getRoute($key, $key_id, $id)) {
                return $pr_ac;
            }    
        }
        return '';
    }

    public function getUrl()
    {
        return Url::to(['notifications/rnr', 'id' => $this->id]);
    }

    public static function getFindAll($ids)
    {
        return self::findAll($ids);
    }

    public static function getFindOne($user_id, $key)
    {
        $model = self::find()
            ->where(['user_id' => $user_id])
            ->andWhere(["seen" => 0])
            ->andWhere(['<=', 'created_at', date('Y-m-d H:i:s')])
            ->orderBy('created_at DESC');

        if ($key) {
            $model->andWhere(['key' => $key]);
        }
        
        return $model->one();
    }

    public static function getFindCheckOne($user_id, $key)
    {
        $model = self::getFindOne($user_id, $key);

        if ($model && !empty($model['is_check'])) {
            $result = \backend\modules\notifications\models\NotificationCheck::getDefinition($model);
            if ($result) return [];
        }
        return $model;
    }

    public static function getListKeyNotification()
    {
        $result = [];
        foreach (self::getClassNotifications() as $key => $value) {
            if (is_subclass_of($value, '\backend\models\manager_notification\BellsNotification')) {
                $result = array_merge($value::getKeys(), $result);
            }
        }
        return $result;
    }

    public static function getNameNotification()
    {
        $name = [];
        foreach (static::getKeys() as $key => $value) {
            if (in_array($value, self::exceptionKey())) continue;
            $name[$value] = static::_getTitleName($value);
        }
        return $name;
    }
    //Ключи которые не нужно выводить в коколокольчике
    public static function exceptionKey()
    {
        return [
           
        ];
    }

    //ключи которые работают с модалкой, подниается модальное окно
    public static function getKeysModal()
    {
        $result = [];
        foreach (self::getClassNotifications() as $key => $value) {
            if (is_subclass_of($value, '\backend\models\manager_notification\ModalNotification')) {
                $result = array_merge($value::getKeys(), $result);
            }
        }
        return $result;  
    }

    public static function getCountBells()
    {
        $result = 0;
        foreach (self::getClassNotifications() as $key => $value) {
            if (is_subclass_of($value, '\backend\models\manager_notification\BellsNotification')) {
                $result++;
            }
        }
        return $result;       
    }

    public function getQueryUserName()
    {
       return ($this->queryData->usersData)?$this->queryData->usersData->fullname:'';
    }

    public function getQuerySmiName()
    {
        return ($this->queryData->pFSmiData)?$this->queryData->pFSmiData->name:'';
    }

    public function getNameBell()
    {
        foreach (self::getClassNotifications() as $key => $value) {
            if (is_subclass_of($value, '\backend\models\manager_notification\BellsNotification')) {
                if (in_array($this->key, $value::getKeys())) {
                    return $value::getNameBell();
                    
                }
            }
        }
        return '';
    }

    public static function getCurrentNotification($user_id)
    {
        $result_key = [];
        $priority_keys = [];
        $object = null;

        foreach (self::getClassNotifications() as $key => $value) {
            if (is_subclass_of($value, '\backend\models\manager_notification\ModalNotification')) {
                if (is_subclass_of($value, '\backend\models\manager_notification\ModalPriority')) {
                    $priority_keys = array_merge($value::getKeys(), $priority_keys);
                } else {
                    $result_key = array_merge($value::getKeys(), $result_key);
                }
            }
        }

        if ($priority_keys) {
            $priority = new \backend\models\manager_notification\PriorityNotification;
            $priority->setUserId($user_id);
            $priority->setKey($priority_keys);
            $priority->build();
            return $priority->getNotification();
        }

        if ($result_key) {
            return self::getFindCheckOne($user_id, $result_key);
        }

        return [];
    }
}
