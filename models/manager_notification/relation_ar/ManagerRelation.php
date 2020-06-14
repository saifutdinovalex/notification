<?php
namespace backend\models\manager_notification\relation_ar;

use Yii;

trait ManagerRelation
{
	public function getUsersData()
    {
        return $this->hasOne(\backend\models\Users::class, ['id' => 'key_id']);
    }
}

?>
