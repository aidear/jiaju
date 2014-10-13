<?php
namespace BackEnd\Model;

use Zend\Db\TableGateway\TableGateway as Father;
class AbstractTable extends Father
{
	function getOneById($id){
		$rowset = $this->select(array('id' => $id));
		$row = $rowset->current();
		return $row;
	}
}
