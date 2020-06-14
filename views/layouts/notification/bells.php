<?
use \backend\models\permissions\Permission;
use \backend\models\manager_notification\notifications\AppointedManager;
use \backend\models\manager_notification\notifications\PitchRestriction;
use \backend\models\manager_notification\notifications\QueryJournalist;
use \backend\models\manager_notification\notifications\ProfAccount;

$variable = [ 
	[
		'li_class' => 'manager_appointed',
		'icon' => 'fa fa-user-o',
		'color_count' => 'label-warning',
		'href_li' => '/manager-notification/?'.AppointedManager::getBellNotification(),
		'visible' => true,
		'button_seen_all' => true,
		'array_key' => \yii\helpers\Html::encode(json_encode(AppointedManager::getKeys())),
	],
	[
		'li_class' => 'exhausted_limit_pitch',
		'icon' => 'fa fa-bell-o',
		'color_count' => 'label-default',
		'href_li' => '/manager-notification/?'.PitchRestriction::getBellNotification(),
		'visible' => true,
		'button_seen_all' => false,
		'array_key' => \yii\helpers\Html::encode(json_encode(PitchRestriction::getKeys())),
	],
	[
		'li_class' => 'smi_deadline',
		'icon' => 'fa fa-calendar-times-o',
		'color_count' => 'label-success',
		'href_li' => '/manager-notification/?'.QueryJournalist::getBellNotification(),
		'visible' => $user->can(Permission::CAN_VIEW_NOTIFICATION_SMI_MANAGER),
		'button_seen_all' => false,
		'array_key' => \yii\helpers\Html::encode(json_encode(QueryJournalist::getKeys())),
	],
	[
		'li_class' => 'pf_account',
		'icon' => 'fa fa-binoculars',
		'color_count' => 'label-info',
		'href_li' => '/manager-notification/?'.ProfAccount::getBellNotification(),
		'visible' => $user->can(Permission::CAN_VIEW_BELL_PROF_ACCOUNT),
		'button_seen_all' => false,
		'array_key' => \yii\helpers\Html::encode(json_encode(ProfAccount::getKeys())),
	],

];

foreach ($variable as $key => $data) 
{
	if ($data['visible'])
	{
		echo $this->render('_item',['data' => $data]);	
	}
}
?>