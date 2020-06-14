<?php
namespace backend\models\manager_notification\sql;

use Yii;
use backend\models\invoices\PeriodTypes;
use backend\models\manager_notification\conditions\PrAccount;
use backend\models\manager_notification\notifications\ProfAccount;

class Escort 
{
	const PERIOD_DAY_EXCEPTION = 30;
	private $_user_ids = []; //исключаем из третьего sql3 запроса пользователей, которые прошили по условию первого запроса sql1

	public function getRun()
	{
		$n = $this->getSql1();
		$m = $this->getSql2();
		$p = $this->getSql3();
		
		echo "add new notification: ".($n+$m+$p).'\n';
	}

	private function getSqlBase()
	{
		$sql = (new \yii\db\Query)
		->from(['User u'])
		->leftJoin('Invoice i', 'i.id = u.invoice_id')
		->leftJoin("Manager m", 'm.user_id = u.manager_id')
		->andWhere(['>', 'i.stop_at', new \yii\db\Expression('NOW()')])
		->andWhere(['i.is_deleted' => 0])
		->andWhere(['u.deleted' => 0, 'u.is_speaker' => 0])
		->andWhere(['OR', ['IS','mn.id', NULL], ['<', 'mn.created_at', new \yii\db\Expression('DATE_SUB(CURDATE(), INTERVAL 30 DAY)')]])
		->andWhere(['m.position' => \backend\models\manager\Position::ESCORT])
		->groupBy('u.user_id');
		return $sql;
	}

	protected function getSql1()
	{
		$sql = $this->getSqlBase()
		->select('u.user_id, u.visited_at, u.manager_id, i.period, i.start_at')
		->leftJoin('ManagerNotification mn', "mn.key_id = u.user_id and mn.key in ('".ProfAccount::USER_NOT_VISITED_6_12."', '". ProfAccount::USER_NOT_VISITED_1_3."')")
		->all();
		
		$obj = new PrAccount;
		$i = 0;
		foreach ($sql as $key => $value) {
			
			$obj->setPeriodType($value['period']);
			if ($this->setVisited($obj, $value['visited_at'], $value['start_at'])) {
				$this->addNotification($value['user_id'], $value['manager_id'], $value['period'], PrAccount::VISITED_AT);
				$this->_user_ids[] = $value['user_id'];
				$i++;
			}
		}
		$obj = null;
		return $i;
	}

	protected function getSql2()
	{
		$sql = $this->getSqlBase()
		->select('u.user_id,  max(dp.pitch_status_at) p_status_at, u.manager_id, i.period, i.start_at')
		->leftJoin('Pitches p', 'p.user_id =u.user_id')
		->leftJoin('data_pitches dp', 'dp.pitch_id = p.pitch_id')
		->leftJoin('ManagerNotification mn', "mn.key_id = u.user_id and mn.key in ('".ProfAccount::USER_NOT_ACCEPTED_PITCH_6_12."', '". ProfAccount::USER_NOT_ACCEPTED_PITCH_1_3."')")
		->andWhere(['p.pitch_status' => 'accepted'])
		->all();
		
		$obj = new PrAccount;
		$i = 0;
		foreach ($sql as $key => $value) {
			$obj->setPeriodType($value['period']);
			if ($value['p_status_at'] && $this->setAcceptedPitch($obj, $value['p_status_at'], $value['start_at'])) {
				$this->addNotification($value['user_id'], $value['manager_id'], $value['period'], PrAccount::ACCEPTED_PITCH_AT);	
				$i++;
			}
		}
		$obj = null;
		return $i;
	}

	protected function getSql3()
	{
		$sql = $this->getSqlBase()
		->select('u.user_id, u.visited_at, p.pitch_id, p_date, u.manager_id, i.period, i.start_at')
		->leftJoin('(Select pitch_id, max(pitch_date) p_date, user_id From Pitches group by user_id) p', 'p.user_id = u.user_id')
		->leftJoin('ManagerNotification mn', "mn.key_id = u.user_id and mn.key in ('".ProfAccount::USER_NOT_CREATE_PITCH_6_12."', '". ProfAccount::USER_NOT_CREATE_PITCH_1_3."','".ProfAccount::USER_NOT_VISITED_6_12."', '". ProfAccount::USER_NOT_VISITED_1_3."')")
		->all();
		
		$obj = new PrAccount;
		$i = 0;
		foreach ($sql as $key => $value) {
			$obj->setPeriodType($value['period']);
			if (!in_array($value['user_id'], $this->_user_ids) && $this->setCreatePitch($obj, $value['p_date'], $value['start_at'])) {
				$this->addNotification($value['user_id'], $value['manager_id'], $value['period'], PrAccount::CREATE_PITCH_AT);
				$i++;
			}
		}
		$obj = null;
		return $i;
	}

	protected function setVisited($obj, $date, $start_at)
	{
		$obj->setDate($date);
		$obj->setStartAt($start_at);
		return $obj->getResult(PrAccount::VISITED_AT);
	}

	protected function setCreatePitch($obj, $date, $start_at)
	{
		$obj->setDate($date);
		$obj->setStartAt($start_at);
		return $obj->getResult(PrAccount::CREATE_PITCH_AT);	
	}

	protected function setAcceptedPitch($obj, $date, $start_at)
	{
		$obj->setDate($date);
		$obj->setStartAt($start_at);
		return $obj->getResult(PrAccount::ACCEPTED_PITCH_AT);		
	}

	protected function addNotification($user_id, $manager_id, $period, $type_notification)
	{
		if (!$manager_id) return;

		$key = ProfAccount::getKeyEscort($period, $type_notification);
		\backend\components\Notification::notify($key, $manager_id, $user_id);
	}	


}
