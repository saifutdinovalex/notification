<?php

namespace backend\modules\notifications\controllers;

use backend\modules\notifications\models\Notification;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use yii\db\Expression;

class NotificationsController extends Controller
{
    const MAX_SHOW_NOTIFICATION_BELL = 10;
    /**
     * @var integer The current user id
     */
    private $user_id;

    /**
     * @var string The notification class
     */
    private $notificationClass;

    private $_keys = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->user_id = $this->module->userId;
        $this->notificationClass = $this->module->notificationClass;
        parent::init();
    }

    /**
     * Poll action
     *
     * @param int $seen Whether to show already seen notifications
     * @return array
     */
    public function actionPoll($seen = 0)
    {
        $seen = $seen ? 'true' : 'false';

        /** @var Notification $class */
        $class = $this->notificationClass;
        $find = $class::find()
            ->where([$class::tableName().'.user_id' => $this->user_id])
            ->andWhere(['seen' => $seen])
            ->andWhere(['<=', $class::tableName().'.created_at', date('Y-m-d H:i:s')])
            ->andWhere(['key' => \backend\components\Notification::getListKeyNotification()])
            ->joinWith([
                            'usersData us', 
                            'queryData' => function($q){
                                $q->joinWith(['usersData uq']);
                            }, 
                            'queryData.pFSmiData',
                            'userSmiData usmi',
                        ])
            ->orderBy($class::tableName().'.created_at DESC');
        

        $models = $find->all();

        $results = [];
        $i = 0;
        foreach ($models as $model) {
            
            if ($this->isCheck($model)) continue;
            if ($this->isLimitedNotification($model->key)) continue;
            // give user a chance to parse the date as needed
            $date = \DateTime::createFromFormat($this->module->dbDateFormat, $model->created_at)
                ->format('Y-m-d H:i:s');

            /** @var Notification $model */
            $results['data'][] = [
                'id' => $model->id,
                'type' => $model->type,
                'title' => $model->getTitle(),
                'description' => $model->getDescription(),
                'url' => $model->getUrl(),
                'key' => $model->key,
                'flashed' => $model->flashed,
                'date' => $date,
            ];
            $i++;
        }
        
        foreach (\backend\components\Notification::getClassNotifications() as $k => $value) {
            if (is_subclass_of($value, '\backend\models\manager_notification\BellsNotification')) {
                $results['counts'][$k]['class_name'] = $value::getNameBell();
                $results['counts'][$k]['count'] = isset($this->_keys[$k])?$this->_keys[$k]:0;
            }
        }
       
        return $results;
    }

    /**
     * Marks a notification as read and redirects the user to the final route
     *
     * @param int $id The notification id
     * @return Response
     * @throws HttpException Throws an exception if the notification is not
     *         found, or if it don't belongs to the logged in user
     */
    public function actionRnr($id)
    {
        $notification = $this->actionRead($id);
        return $this->redirect(Url::to($notification->getRoute()));
    }

    /**
     * Marks a notification as read
     *
     * @param int $id The notification id
     * @return Notification The updated notification record
     * @throws HttpException Throws an exception if the notification is not
     *         found, or if it don't belongs to the logged in user
     */
    public function actionRead($id)
    {
        $notification = $this->getNotification($id);

        $notification->seen = 1;
        $r = $notification->save();
        
        return $notification;
    }

    /**
     * Marks all notification as read
     *
     * @throws HttpException Throws an exception if the notification is not
     *         found, or if it don't belongs to the logged in user
     */
    public function actionReadAll()
    {
        $notificationsIds = Yii::$app->request->post('ids', []);

        foreach ($notificationsIds as $id) {
            $notification = $this->getNotification($id);

            $notification->seen = 1;
            $notification->save();
        }

        return true;
    }

    /**
     * Delete all notifications
     *
     * @throws HttpException Throws an exception if the notification is not
     *         found, or if it don't belongs to the logged in user
     */
    public function actionDeleteAll()
    {
        $notificationsIds = Yii::$app->request->post('ids', []);

        foreach ($notificationsIds as $id) {
            $notification = $this->getNotification($id);

            $notification->delete();
        }

        return true;
    }

    /**
     * Deletes a notification
     *
     * @param int $id The notification id
     * @return int|false Returns 1 if the notification was deleted, FALSE otherwise
     * @throws HttpException Throws an exception if the notification is not
     *         found, or if it don't belongs to the logged in user
     */
    public function actionDelete($id)
    {
        $notification = $this->getNotification($id);
        return $notification->delete();
    }

    public function actionFlash($id)
    {
        $notification = $this->getNotification($id);

        $notification->flashed = 1;
        $notification->save();

        return $notification;
    }


    /**
     * Gets a notification by id
     *
     * @param int $id The notification id
     * @return Notification
     * @throws HttpException Throws an exception if the notification is not
     *         found, or if it don't belongs to the logged in user
     */
    private function getNotification($id)
    {
        /** @var Notification $notification */
        $class = $this->notificationClass;
        $notification = $class::findOne($id);
        if (!$notification) {
            throw new HttpException(404, "Unknown notification");
        }

        if ($notification->user_id != $this->user_id) {
            throw new HttpException(500, "Not your notification");
        }

        return $notification;
    }

    private function isCheck($model)
    {
        if(!$model['is_check']) return false;
        return \backend\modules\notifications\models\NotificationCheck::getDefinition($model);
                
    }

    private function isLimitedNotification($key)
    {
        $_k = $this->getIdBell($key);
        if (isset($this->_keys[$_k])) {
            $this->_keys[$_k]++;    
        } else {
            $this->_keys[$_k] = 1;    
        }
        

        if ($this->_keys[$_k] > self::MAX_SHOW_NOTIFICATION_BELL) {
            return true;
        }
        return false;
    }

    private function getIdBell($key)
    {
        foreach (\backend\components\Notification::getClassNotifications() as $k => $value) {
            if (in_array($key, $value::getKeys())) {
                return $k;
            }
        }
    }
}
