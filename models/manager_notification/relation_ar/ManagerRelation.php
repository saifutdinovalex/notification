<?php
namespace backend\models\manager_notification\relation_ar;

use Yii;

trait ManagerRelation
{
	  public function getUsersData()
    {
        return $this->hasOne(\backend\models\ar\ArUsers::class, ['user_id' => 'key_id']);
    }

    public function getQueryData(){
        return $this->hasOne(\backend\models\ar\ArQueries::class, ['id' => 'key_id']);
    }

    public function getUserSmiData()
    {
        return $this->hasOne(\backend\models\ar\ArUserSmi::class, ['id' => 'key_id']);
    }

    public function getVpRequestData()
    {
        return $this->hasOne(\backend\models\ar\ArVpRequest::class, ['id' => 'key_id']);
    }
	
}

?>