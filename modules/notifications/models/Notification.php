<?php

namespace backend\modules\notifications\models;

use backend\models\sql\SqlQueries;
use backend\modules\notifications\NotificationsModule;
use Yii;
use yii\data\SqlDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property string $key_id
 * @property string $key
 * @property string $type
 * @property boolean $seen
 * @property boolean $flashed
 * @property string $created_at
 * @property integer $user_id
 */
abstract class Notification extends ActiveRecord
{

    /**
     * Default notification
     */
    const TYPE_DEFAULT = 'default';
    /**
     * Error notification
     */
    const TYPE_ERROR   = 'error';
    /**
     * Warning notification
     */
    const TYPE_WARNING = 'warning';
    /**
     * Success notification type
     */
    const TYPE_SUCCESS = 'success';

    /**
     * @var array List of all enabled notification types
     */
    public static $types = [
        self::TYPE_WARNING,
        self::TYPE_DEFAULT,
        self::TYPE_ERROR,
        self::TYPE_SUCCESS,
    ];

    /**
     * Gets the notification title
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Gets the notification description
     *
     * @return string
     */
    abstract public function getDescription();

    /**
     * Gets the notification route
     *
     * @return string
     */
    abstract public function getRoute();

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ManagerNotification}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'user_id', 'key', 'created_at'], 'required'],
            [['id', 'key_id', 'created_at'], 'safe'],
            [['user_id'], 'integer'],
            [['key_id'], 'string'],
        ];
    }

    public function behaviors()
    {
        return [
            'notification_manager_email' => [
                'class' => \common\behaviors\NotificationEmailBehavior::class,
            ],
        ];
    }

    /**
     * Creates a notification
     *
     * @param string $key
     * @param integer $user_id The user id that will get the notification
     * @param string $key_id The foreign instance id
     * @param string $type
     * @return bool Returns TRUE on success, FALSE on failure
     * @throws \Exception
     */
    public static function notify($key, $user_id, $key_id = null, $type = self::TYPE_DEFAULT, $date_notify = null, $is_check = 0)
    {
        $class = self::className();
        return NotificationsModule::notify(new $class(), $key, $user_id, $key_id, $type, $date_notify, $is_check);
    }

    /**
     * Creates a warning notification
     *
     * @param string $key
     * @param integer $user_id The user id that will get the notification
     * @param string $key_id The notification key id
     * @return bool Returns TRUE on success, FALSE on failure
     */
    public static function warning($key, $user_id, $key_id = null)
    {
        return static::notify($key, $user_id, $key_id, self::TYPE_WARNING);
    }


    /**
     * Creates an error notification
     *
     * @param string $key
     * @param integer $user_id The user id that will get the notification
     * @param string $key_id The notification key id
     * @return bool Returns TRUE on success, FALSE on failure
     */
    public static function error($key, $user_id, $key_id = null)
    {
        return static::notify($key, $user_id, $key_id, self::TYPE_ERROR);
    }


    /**
     * Creates a success notification
     *
     * @param string $key
     * @param integer $user_id The user id that will get the notification
     * @param string $key_id The notification key id
     * @return bool Returns TRUE on success, FALSE on failure
     */
    public static function success($key, $user_id, $key_id = null)
    {
        return static::notify($key, $user_id, $key_id, self::TYPE_SUCCESS);
    }

  
}
