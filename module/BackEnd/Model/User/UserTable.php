<?php
namespace BackEnd\Model\User;


use Zend\Paginator\Adapter\DbSelect;

use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use BackEnd\Model\User\User;

class UserTable extends TableGateway
{

    function getUserByName($name)
    {
		$select = $this->getSql()->select();
		$select->where(array('Status' => 1, 'UserName' => $name));//echo str_replace('"', '', $select->getSqlString());die;
		$resultSet = $this->selectWith($select);

		return $resultSet->current();
    }
    function getAllToPage(){
        $select = $this->getSql()->select();
        $adapter = new DbSelect($select, $this->getAdapter());
        return $adapter;
    }

    function getAll(){
        return $this->select();
    }

    function getOneForEmail($email){
        $rowset = $this->select(array('Mail' => $email));
        $row = $rowset->current();
        return $row;
    }

    function getOneForId($id){
        $rowset = $this->select(array('ID' => $id));
        $row = $rowset->current();
        return $row;
    }

    function getUserForName($name){
        $select = $this->getSql()->select();
        $where = function (Where $where) use($name){
            $where->like('UserName' , "%{$name}%");
        };
        $select->where($where);
        $adapter = new DbSelect($select, $this->getAdapter());
        return $adapter;
    }

    function delete($id){
        return parent::delete(array("ID" => $id));
    }
    function checkUserIsExist($name, $id = NULL) {
    	$select = $this->getSql()->select();
    	if ($id) {
    		$select->where("ID<>{$id}");
    	}
    	$select->where(array('UserName' => $name));
    	$resultSet = $this->selectWith($select);
    	return $resultSet->count();
    }
    function save(User $user){
        $user = (array)$user;
        if(empty($user['ID'])){
            unset($user['ID']);
            $this->insert($user);
        }else{
            $id = $user['ID'];
            if($this->getOneForId($id)){
                unset($user['ID']);
                return $this->update($user , array('ID' => $id));
            }else{
                return false;
            }
        }
    }
}
