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

    const KEY_LIMIT_PITCH = 'exhausted_limit_pitch';
    const KEY_CHANGE_SMI = 'change_smi';
    const KEY_CHANGE_COMPANY = 'change_company';
    const KEY_NEW_BRIEF = 'new_brief';
    const KEY_NEW_REQUEST = 'new_request';
    const KEY_COMMENT_INVOICE_VP = 'com_invoice_vp';
    const KEY_PAY_INVOICE = 'pay_invoice';
    const KEY_CHANGE_REQUEST = 'change_request';
    
    const KEY_MANAGER_VP_PROJECT = 'man_vp_project';
    const KEY_PR_VP_CLOSED = 'pr_vp_closed';
    const KEY_PR_VP_OPEN_ARCHIVE = 'meta_company_archive';
    const KEY_PR_MANAGER_VP_2 = 'pr_manager_vp_2';
    const KEY_COMMENT_PROJECT_VP = 'com_project_vp';
    const KEY_INVOICE_VP_CLOSED = 'invoice_vp_closed';
    const KEY_VP_INVOICE_PAY = 'vp_invoice_pay';
    const KEY_COMMENT_CONTRACT_BRIEF = 'com_brief';
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
            case self::KEY_LIMIT_PITCH:
                return \Yii::t('admin','Ended pitches');
            break;
            case self::KEY_CHANGE_SMI:
                return \Yii::t('admin','Smi change');
            break;

            case self::KEY_CHANGE_COMPANY:
                return \Yii::t('admin','Company change');
            break;
            case self::KEY_NEW_BRIEF:
                return \Yii::t('admin', 'Posted by Brif VP');
            break;
            case self::KEY_NEW_REQUEST:
                return \Yii::t('admin', 'Application for VP \ paid subscription from');
            break;

            case self::KEY_COMMENT_INVOICE_VP:
                return \Yii::t('admin', 'Add comment invoice vp');

            case self::KEY_PAY_INVOICE:
                return \Yii::t('admin', 'Invoice paid');
            break;
            case self::KEY_CHANGE_REQUEST:
                return \Yii::t('admin', 'You have a request');
            break;
            case self::KEY_MANAGER_VP_PROJECT:
                return \Yii::t('admin', 'Project Assigned to Manager');
            break;
            case self::KEY_PR_VP_OPEN_ARCHIVE:
                return \Yii::t('admin', 'The project is open again');
            break;
            case self::KEY_PR_MANAGER_VP_2:
                return \Yii::t('admin', 'You are appointed as a project co-executor');
            break;
            case self::KEY_COMMENT_PROJECT_VP:
                return \Yii::t('admin', 'New project comment');
            break;
            case self::KEY_INVOICE_VP_CLOSED:
                return \Yii::t('admin', 'Account closed');
            break;
            case self::KEY_VP_INVOICE_PAY:
                return \Yii::t('admin', 'Invoice vp paid');
            break;
            case self::KEY_COMMENT_CONTRACT_BRIEF:
                return \Yii::t('admin', 'New Brief Commentary');
            break;
            case self::KEY_NEW_INVOICE_VP:
                return \Yii::t('admin', 'New project invoice created');
            break;
            case self::KEY_NEW_AKT_VP:
                return \Yii::t('admin', 'New Act Created');
            break;
            case self::KEY_PR_VP_CLOSED:
                return \Yii::t('admin', 'Project vp closed');
            break;
        }
        return '';
    }

    public static function _getDescription($model)
    {
        switch ($model->key) {
            case self::KEY_LIMIT_PITCH:
                return Yii::t('admin', '{user} has run out of free pitches', [
                    'user' => $model->usersData->fullname
                ]);
            break;
            case self::KEY_CHANGE_SMI:
                $name = ($model->userSmiData)?' '.$model->userSmiData->getName():'';
                return \Yii::t('admin','Smi change').$name;
            break;

            case self::KEY_CHANGE_COMPANY:
                $user = \backend\models\ar\ArCompany::getById($model->key_id);
                return \Yii::t('admin','Company change').' '.$user->name;
            break;

            case self::KEY_NEW_BRIEF:
                $vp_request = \backend\models\ar\ArVpRequest::findOne($model->key_id);
                return \Yii::t('admin', 'Posted by Brif VP').' '. (($vp_request->user_id)?$vp_request->usersData->fullname:'');
            break;

            case self::KEY_NEW_REQUEST:
                $vp_request = \backend\models\ar\ArVpRequest::findOne($model->key_id);
                return \Yii::t('admin', 'Application for VP \ paid subscription from').' '. (($vp_request->user_id)?$vp_request->usersData->fullname:'');
            break;

            case self::KEY_COMMENT_INVOICE_VP:
                $number = \backend\models\ar\ArInvoiceVp::find()->where(['id' => $model->key_id])->one();
                return \Yii::t('admin', 'Added comment to account').' №'.$number->number;    
            case self::KEY_PAY_INVOICE:
                $invoice = \backend\models\ar\ArInvoice::findOne($model->key_id);
                return \Yii::t('admin', 'Invoice paid').' №'.$invoice->number;
            break;
            case self::KEY_CHANGE_REQUEST:
                return \Yii::t('admin', 'You have a request').' №'.$model->key_id;
            break;
            case self::KEY_MANAGER_VP_PROJECT:
                $model = \backend\models\ar\ArMetaCompany::findOne($model->key_id);
                return \Yii::t('admin', 'Project Assigned to Manager').' '.$model->name;
            break;
            case self::KEY_PR_VP_OPEN_ARCHIVE:
                $model = \backend\models\ar\ArMetaCompany::findOne($model->key_id);
                return \Yii::t('admin', 'The project is open again'). ' '. $model->name;    
            break;
            case self::KEY_PR_MANAGER_VP_2:
                $model = \backend\models\ar\ArMetaCompany::findOne($model->key_id);
                if ($model->manager_vp_id2) {
                    return \Yii::t('admin', 'You are appointed as a project co-executor');    
                } else {
                     return \Yii::t('admin', 'You are excluded from task performers');
                }
            break;
            case self::KEY_COMMENT_PROJECT_VP:
                $model = \backend\models\ar\ArMetaCompany::findOne($model->key_id);
                return \Yii::t('admin', 'New project comment').' '.$model->name;
            break;
            case self::KEY_INVOICE_VP_CLOSED:
                $model = \backend\models\ar\ArInvoiceVp::findOne($model->key_id);
                return \Yii::t('admin', 'Account closed').' '.$model->number;
            break;
            case self::KEY_VP_INVOICE_PAY:
                $model = \backend\models\ar\ArInvoiceVp::findOne($model->key_id);
                return \Yii::t('admin', 'Invoice vp paid').' '.$model->number;
            break;
            case self::KEY_COMMENT_CONTRACT_BRIEF:
                return \Yii::t('admin', 'New Brief Commentary');
            break;
            case self::KEY_NEW_INVOICE_VP:
                $model = \backend\models\ar\ArInvoiceVp::findOne($model->key_id);
                return \Yii::t('admin', 'New project invoice created'). ' '.$model->number;
            break;
            case self::KEY_NEW_AKT_VP:
                return \Yii::t('admin', 'New Act Created');
            break;
            case self::KEY_PR_VP_CLOSED:
                $model = \backend\models\ar\ArMetaCompany::findOne($model->key_id);
                return \Yii::t('admin', 'Project vp closed'). ' '. $model->name;
            break;
        }
    }

    public static function _getRoute($key, $key_id, $id)
    {
        switch ($key) {
            case self::KEY_CHANGE_COMPANY:
            case self::KEY_CHANGE_SMI:
                return ['/manager-notification', 'Search[id]' => $id];
            break;
            case self::KEY_LIMIT_PITCH:
                return ['/user/view', 'user_id' => $key_id];
            break;
            case self::KEY_NEW_BRIEF:
            case self::KEY_CHANGE_REQUEST:
            case self::KEY_NEW_REQUEST:
                return ['/vp-request/index', 'Search[id]' => $key_id];
            break;
            case self::KEY_COMMENT_INVOICE_VP:
                return ['/invoice-vp/update', 'id' => $key_id];
            case self::KEY_PAY_INVOICE:
                $invoice = \backend\models\ar\ArInvoice::findOne($key_id);
                return ['/invoices/list', 'ListProviderBuilder[number]' => $invoice->number];
            break;
            case self::KEY_MANAGER_VP_PROJECT:
            case self::KEY_PR_VP_OPEN_ARCHIVE:
            case self::KEY_PR_MANAGER_VP_2:
            case self::KEY_COMMENT_PROJECT_VP:
            case self::KEY_PR_VP_CLOSED:
                return ['/meta-company/view-vp', 'id' => $key_id];
            break;
            case self::KEY_INVOICE_VP_CLOSED:
            case self::KEY_NEW_INVOICE_VP:
            case self::KEY_VP_INVOICE_PAY:
                return ['/invoice-vp/update', 'id' => $key_id];
            break;
            case self::KEY_COMMENT_CONTRACT_BRIEF:
                return ['/contract/brief', 'Search[id]' => $key_id];
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
            self::KEY_MANAGER_VP_PROJECT,
            self::KEY_PR_VP_OPEN_ARCHIVE,
            self::KEY_PR_MANAGER_VP_2,
            self::KEY_COMMENT_PROJECT_VP,
            self::KEY_COMMENT_INVOICE_VP,
            self::KEY_NEW_BRIEF,
            self::KEY_INVOICE_VP_CLOSED,
            self::KEY_VP_INVOICE_PAY,
            self::KEY_COMMENT_CONTRACT_BRIEF,
            self::KEY_VP_INVOICE_PAY,
            self::KEY_NEW_AKT_VP,
            self::KEY_PR_VP_CLOSED,
        ];
    }
}
