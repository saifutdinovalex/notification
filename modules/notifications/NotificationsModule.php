<?php

namespace backend\modules\notifications;

use Yii;
use Exception;
use backend\modules\notifications\models\Notification;
use yii\base\Module;
use yii\db\Expression;

class NotificationsModule extends Module
{
    /**
     * @var string The controllers namespace
     */
    public $controllerNamespace = 'backend\modules\notifications\controllers';

    /**
     * @var Notification The notification class defined by the application
     */
    public $notificationClass;

    /**
    * @var boolean Whether notification can be duplicated (same user_id, key, and key_id) or not
    */
    public $allowDuplicate = false;

    /**
     * @var string Database created_at field format
     */
    public $dbDateFormat = 'Y-m-d H:i:s';

    /**
     * @var callable|integer The current user id
     */
    public $userId;

	/**
	 * @var callable|integer The current user id
	 */
	public $expirationTime = 0;

	/**
     * @inheritdoc
     */
    public function init() {
        if (is_callable($this->userId)) {
            $this->userId = call_user_func($this->userId);
        }
        parent::init();

	    if (Yii::$app instanceof \yii\console\Application) {
		    $this->controllerNamespace = 'backend\modules\notifications\commands';
	    }
    }

    /**
     * Creates a notification
     *
     * @param Notification $notification The notification class
     * @param string $key The notification key
     * @param integer $user_id The user id that will get the notification
     * @param string $key_id The key unique id
     * @param string $type The notification type
     * @return bool Returns TRUE on success, FALSE on failure
     * @throws Exception
     */
    public static function notify($notification, $key, $user_id, $key_id = null, $type = Notification::TYPE_DEFAULT, $date_notify = null, $is_check = 0)
    {

        if (!in_array($key, $notification::getKeys())) {
            throw new Exception("Not a registered notification key: $key");
        }

        if (!in_array($type, Notification::$types)) {
            throw new Exception("Unknown notification type: $type");
        }

        /** @var Notification $instance */
        $instance = $notification::findOne(['user_id' => $user_id, 'key' => $key, 'key_id' => (string)$key_id]);
        if (!$instance || \Yii::$app->getModule('notifications')->allowDuplicate) {
            $instance = new $notification([
                'key' => $key,
                'type' => $type,
                'seen' => 0,
                'flashed' => 1,
                'user_id' => $user_id,
                'key_id' => (string)$key_id,
                'created_at' => ($date_notify === null)? new Expression('NOW()'):$date_notify,
                'is_check' => $is_check,
            ]);
            if ($instance->save()) {
                self::deleteOldNotify($notification, $key, $user_id, $instance->id, $key_id);
                return true;
            }
        }
        return true;
    }

    public static function deleteOldNotify($notification, $key, $user_id, $id, $key_id = null)
    {
        $models = $notification::find()->andWhere(['AND',['user_id' => $user_id, 'key' => $key, 'key_id' => (string)$key_id, 'seen' => 0],['!=', 'id', $id]])->all();
        if (!$models) return;
        foreach ($models as $key => $value) {
            $value->seen = 1;
            $value->save();
        }
    }
}
