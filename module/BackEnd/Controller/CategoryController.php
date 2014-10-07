<?php
namespace BackEnd\Controller;

use BackEnd\Form\CategoryForm;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
class CategoryController extends AbstractController
{
	protected  $op = array();
	protected  $category = array();
	protected $unformatCate = array();
	public function indexAction() {
		$routeParams = array('controller' => 'category');
		$prefixUrl = $this->url()->fromRoute(null, $routeParams);
		$params = array();
        $table = $this->_getTable('CategoryTable');
        $name = $this->params()->fromQuery('name' , '');
        $pageSize = $this->params()->fromQuery('pageSize');

        if($name){
        	$params['name'] = $name;
        }
        if ($pageSize) {
        	$params['pageSize'] = $pageSize;
        }
        $params['orderField'] = $this->params()->fromQuery('orderField', 'order');
        $params['orderType'] = $this->params()->fromQuery('orderType', __LIST_ORDER);

        $removePageParams = $params;

        $params['page'] = $this->params()->fromQuery('page' , 1);

        $orderPageParams = $params;


        $paginaction = $this->_getCategoryPaginator($params, 1);


        $startNumber = 1+($params['page']-1)*$paginaction->getItemCountPerPage();
        $order = $this->_getOrder($prefixUrl, array('name', 'isShow', 'line',  'order', 'subCount', 'subLinkCount', 'updateTime', 'updateUser'), $removePageParams);

        $assign = array(
        		'paginaction' => $paginaction,
        		'startNumber' => $startNumber,
        		'orderQuery' => http_build_query($orderPageParams),
        		'query' => http_build_query($removePageParams),
        		'order' => $order,
        		'k' => $name,
        );

        return new ViewModel($assign);
	}
	public function saveAction() {
		$table = $this->_getTable('CategoryTable');
		$form = new CategoryForm();
		$cate = $table->getList();
		$fCategory = $this->_formatCategory($cate);
		$navOption = array();
		$this->op[0] = '顶级分类';
		$this->_returnOptionValue($fCategory);
		if ($catid = $this->params()->fromQuery('id')) {
			unset($this->op[$catid]);
		}
		$form->get('parent')->setValueOptions($this->op);
		$req = $this->getRequest();
		if ($req->isPost()) {
			$params = $req->getPost();
			$form->setData($params);
			if ($form->isValid()) {
				$data = $form->getData();
				if (!$this->params()->fromPost('id')) {
					$data['add_user'] = $this->_getSession()->UserID;
					$data['add_time'] = date('Y-m-d H:i:s');
				}
				$data['path'] = $table->getPathByParent($data['parent']);
				$id = $table->save($data);
				$pic = $this->_insertImg($id, $this->params()->fromFiles('pic'), 'category');
				if ($pic) {
					$table->update(array('pic' => $pic), array('id' => $id));
				}
				$this->_success('保存成功！');
				return $this->redirect()->toRoute('backend', array('controller' => 'category'));
			} else {
// 				print_r($form->getMessages());die;
			}
		} elseif ($id = $this->params()->fromQuery('id')) {
			$category = $table->getOneById($id);
            $form->setData($category);
		}
		$v = new ViewModel(array('form' => $form));
		return $v;
	}
	public function deleteAction() {
		$id = $this->params()->fromQuery('id');
		$table = $this->_getTable('CategoryTable');
		if ($table->update(array('is_delete' => 1), array('id' => $id))) {
			$this->_success('已删除！');
		} else {
			$this->_error('出现错误！');
		}
		return $this->redirect()->toRoute('backend', array('controller' => 'category'));
	}
	private function _getCategoryPaginator($params, $root = 0)
	{
		$page = isset($params['page']) ? $params['page'] : 1;
		$order = array();
		if ($params['orderField']) {
			$order = array($params['orderField'] => $params['orderType']);
		}
		$table = $this->_getTable('CategoryTable');
		$params['root']  = $root;
		$paginator = new Paginator($table->getListsToPaginator($params, $order));
		$paginator->setCurrentPageNumber($page)->setItemCountPerPage(isset($params['pageSize']) ? $params['pageSize'] : self::LIMIT);
		return $paginator;
	}
	private function _formatCategory($rows)
	{
		$tr = array();
		foreach ($rows as $k=>$v) {
			if (0 == $v['parent'] && empty($v['path'])) {
				$tr[$v['id']]['id'] = $v['id'];
				$tr[$v['id']]['name'] = '【' . $v['name'] . '】';
				$tr[$v['id']]['depth'] = 0;
				// 				array('id' => $v['id'], 'name' => $v['name'], 'depth' => 0);
			} else {
				$path = explode(',', trim($v['path'], ','));
				$count = count($path);
				$tmp = '$tr';
				for($i=0; $i < $count; $i ++) {
					$tmp .= "[$path[$i]]['sub']";
				}
				$tmp .= "[".$v['id']."]";//echo $tmp,'<br />';
				$val = array('id' => $v['id'], 'name' => $v['name'], 'depth' => $count);
				eval("$tmp=\$val;");
			}
		}
// 				echo '<pre>';
// 				print_r($tr);
// 				echo '</pre>';die;

		return $tr;
	}
	private function _returnOptionValue($fCategory)
	{
		foreach ($fCategory as $k=>$v) {
			$prefix = str_repeat(' ---- ', $v['depth']);
			$this->op[$k] = '| '.$prefix.$v['name'];
			$this->category[$k] = $v['name'];
			$this->unformatCate[$k] = str_replace(array('【', '】'), '', $v['name']);
			if (isset($v['sub'])) {
				$this->_returnOptionValue($v['sub']);
			}
		}
	}
}