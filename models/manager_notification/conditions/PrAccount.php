<?php
namespace backend\models\manager_notification\conditions;

use Yii;
use backend\models\invoices\PeriodTypes;

class PrAccount 
{
	const VISITED_AT = 'visited_at';
	const CREATE_PITCH_AT = 'create_pitch_at';
	const ACCEPTED_PITCH_AT = 'accepted_pitch_at';
	protected $model;
	protected $period;
	private $_time;
	private $date;
	private $start_at;
	private $type;
	private $_temp;

	public function __construct()
	{
		$this->_time = time();
	}
	//тип профессионального аккаунта PaymentTypes
	//false
	// $type = 	1,3,6,12
	public function setPeriodType($type)
	{
		$this->period = $type;
	}

	public function setDate($value)
	{
		$this->date = $value;
	}

	public function setStartAt($value)
	{
		$this->start_at = $value;
	}

	public function getResult($value)
	{
		$this->type = $value;
		if ($this->checkPeriod() && $this->checkDate() && $this->checkStartAt()) {
			return $this->getResultAccount();
		}
		return false;
	}
	
	//period day
	protected function getDataVisited()
	{
		return [
			PeriodTypes::ONE => [
				static::VISITED_AT => 14,
				static::CREATE_PITCH_AT => 7,
				static::ACCEPTED_PITCH_AT => 14 
			],
			PeriodTypes::THREE => [
				static::VISITED_AT => 14,
				static::CREATE_PITCH_AT => 7,
				static::ACCEPTED_PITCH_AT => 14 
			],
			PeriodTypes::SIX => [
				static::VISITED_AT => 30,
				static::CREATE_PITCH_AT => 14,
				static::ACCEPTED_PITCH_AT => 30 
			],
			PeriodTypes::TWELVE => [
				static::VISITED_AT => 30,
				static::CREATE_PITCH_AT => 14,
				static::ACCEPTED_PITCH_AT => 30 
			]
		];
	}

	protected function checkPeriod()
	{
		return in_array($this->period, array_keys($this->getDataVisited()));
	}

	protected function checkDate()
	{
		return !empty($this->date) && $this->date != '0000-00-00 00:00:00';
	}
	// проверяем что счет актирован более 1 месяца назад
	protected function checkStartAt()
	{
		return $this->calculatorPeriod() >= \Yii::$app->formatter->asTimestamp($this->start_at);
	}

	private function calculatorPeriod()
	{
		if (isset($this->_temp[$this->period][$this->type])) {
			return $this->_temp[$this->period][$this->type];
		}
		$this->_temp[$this->period][$this->type] = $this->_time - 86400 * $this->getDataVisited()[$this->period][$this->type];
		return $this->_temp[$this->period][$this->type];
	}

	private function getResultAccount()
	{
		return $this->calculatorPeriod() > \Yii::$app->formatter->asTimestamp($this->date);
	}
}
