<?php
namespace BackEnd\Model\Article;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Paginator\Adapter\DbSelect;
class ArticleTable extends TableGateway
{
	public function getListsToPaginator(array $data, $order = array())
	{
		$select = $this->getSql()->select();
		$where = $select->where;
		if(!empty($data['title'])){
// 			$where .= " AND article.title LIKE '%{$data['title']}%'";
			$where->like('title', $data['title']);
		}

// 		$select->join('adminuser', 'category.add_user=adminuser.ID', 'UserName', 'left');
// 		$select->join(array('b' => 'category'), 'category.parent=b.id', array('parent_name' => 'name'), 'left');
		$select->where($where);
		if (!empty($order)) {
			list($k, $v) = each($order);
			$select->order(array($k=>$v));
		}
		return new DbSelect($select, $this->getAdapter());
	}
}