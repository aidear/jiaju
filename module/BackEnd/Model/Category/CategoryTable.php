<?php
namespace BackEnd\Model\Category;

use Custom\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Paginator\Adapter\DbSelect;
class CategoryTable extends TableGateway
{
	public function getListsToPaginator(array $data, $order = array())
	{
		$where = 'category.is_delete=0';
		if(!empty($data['name'])){
			$where .= " AND category.name LIKE '%{$data['name']}%'";
		}
		$select = $this->getSql()->select();
		$select->join('adminuser', 'category.add_user=adminuser.ID', 'UserName', 'left');
		$select->join(array('b' => 'category'), 'category.parent=b.id', array('parent_name' => 'name'), 'left');
		$select->where($where);
		if (!empty($order)) {
			list($k, $v) = each($order);
			$select->order(array($k=>$v));
		}
		return new DbSelect($select, $this->getAdapter());
	}
	function getOneForId($id){
		if (is_array($id)) {
			$select = $this->getSql()->select();
			$where = function(Where $where) use ($id) {
				$where->in('id', $id);
			};
			$select->where($where);
			//     		echo str_replace('"', '', $select->getSqlString());die;
			return $this->selectWith($select);
		} else {
			$rowset = $this->select(array('id' => $id));
			$row = $rowset->current();
			return $row;
		}
	}
	function save($category){
		unset($category['inputFilter']);
		if(empty($category['id'])){
			unset($category['id']);
			if ($this->insert($category)) {
				return $this->getLastInsertValue();
			}
		}else{
			$id = $category['id'];
			if($this->getOneForId($id)){
				unset($category['id']);
				return $this->update($category , array('id' => $id));
			}else{
				return false;
			}
		}
	}
	function getlist($where = array())
	{
		$select = $this->getSql()->select();

		if ($where) {
			$select->where($where);
		}
// 		$select->order($order);
		$resultSet = $this->selectWith($select);
		return $resultSet->toArray();
	}
	function getPathByParent($pid)
	{
		if (!$pid) return '';
		$rowset = $this->select(array('id' => $pid));
		$row = $rowset->current();
		return rtrim($row->path, ',') . ',' . $pid . ',';
	}
}