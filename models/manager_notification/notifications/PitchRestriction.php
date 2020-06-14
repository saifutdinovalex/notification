<?php
namespace backend\models\manager_notification\notifications;

use Yii;
use backend\models\manager_notification\NotificationDTO;
use backend\models\manager_notification\KeyNotification;
use backend\models\manager_notification\BellsNotification;
use backend\models\manager_notification\EmailNotification;

class PitchRestriction implements KeyNotification, BellsNotification, EmailNotification
{
    use NotificationDTO;

    const KEY_CHANGE_COMPANY = 'change_company';
    const KEY_PAY_INVOICE = 'pay_invoice';
    const KEY_NEW_INVOICE_VP = 'new_invoice_vp';
    const KEY_NEW_AKT_VP = 'new_akt_vp';

    public static function getNameBell()
    {
        return 'exhausted_limit_pitch';
    }

    public static function getKeys()
    {
        return array_values((new \ReflectionClass(static::class))->getConstants());
    }

    public static function _getTitleName($key)
    {
        switch ($key) {
            case self::KEY_CHANGE_COMPANY:
                return \Yii::t('admin','Company change');
            break;
            
            case self::KEY_PAY_INVOICE:
                return \Yii::t('admin', 'Invoice paid');
            break;
            case self::KEY_NEW_INVOICE_VP:
                return \Yii::t('admin', 'New project invoice created');
            break;
            case self::KEY_NEW_AKT_VP:
                return \Yii::t('admin', 'New Act Created');
            break;
        }
        return '';
    }

    public static function _getDescription($model)
    {
        switch ($model->key) {
           
            case self::KEY_CHANGE_COMPANY:
                $user = \backend\models\Company::findOne($model->key_id);
                return \Yii::t('admin','Company change').' '.$user->name;
            break;

            case self::KEY_PAY_INVOICE:
                $invoice = \backend\models\Invoice::findOne($model->key_id);
                return \Yii::t('admin', 'Invoice paid').' №'.$invoice->number;
            break;
           
           
            case self::KEY_NEW_INVOICE_VP:
                $model = \backend\models\InvoiceVp::findOne($model->key_id);
                return \Yii::t('admin', 'New project invoice created'). ' '.$model->number;
            break;
            case self::KEY_NEW_AKT_VP:
                return \Yii::t('admin', 'New Act Created');
            break;
        }
    }

    public static function _getRoute($key, $key_id, $id)
    {
        switch ($key) {
            case self::KEY_CHANGE_COMPANY:
                return ['/manager-notification', 'Search[id]' => $id];
            break;
            case self::KEY_PAY_INVOICE:
                $invoice = \backend\models\Invoice::findOne($key_id);
                return ['/invoices/index', 'Search[number]' => $invoice->number];
            break;          
            case self::KEY_NEW_INVOICE_VP:
         
                return ['/invoice-vp/update', 'id' => $key_id];
            break;
            case self::KEY_NEW_AKT_VP:
                return ['/akt-vp/update', 'id' => $key_id];
            break;
        };

        return '';
    }

    public static function getBellNotification()
    {
        return self::getStringSearchNotification(static::getKeys());
    }

    public function getKeyEmail()
    {
        return [  
            self::KEY_NEW_INVOICE_VP,
            self::KEY_NEW_AKT_VP,
        ];
    }
}
