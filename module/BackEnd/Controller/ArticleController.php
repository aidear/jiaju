<?php
namespace BackEnd\Controller;

use Zend\View\Model\ViewModel;
use BackEnd\Form\ArticleForm;
class ArticleController extends AbstractController
{
	public function indexAction() {
		$routeParams = array('controller' => 'article');
		$prefixUrl = $this->url()->fromRoute(null, $routeParams);
		$params = array();
		$table = $this->_getTable('ArticleTable');
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


		$paginaction = $this->_getPaginator($params, $table);


		$startNumber = 1+($params['page']-1)*$paginaction->getItemCountPerPage();
		$order = $this->_getOrder($prefixUrl, array('title', 'subhead', 'line',  'order', 'subCount', 'subLinkCount', 'add_time', 'last_update'), $removePageParams);

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
		$table = $this->_getTable('ArticleTable');
		$form = new ArticleForm();
// 		$cate = $table->getList();
// 		$fCategory = $this->_formatCategory($cate);
// 		$navOption = array();
// 		$this->op[0] = '顶级分类';
// 		$this->_returnOptionValue($fCategory);
// 		if ($catid = $this->params()->fromQuery('id')) {
// 			unset($this->op[$catid]);
// 		}
// 		$form->get('parent')->setValueOptions($this->op);
		$form->get('order')->setValue(1);
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
			$article = $table->getOneById($id);
			$form->setData($article);
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
}