<?php
/**
 * AjaxController.php
 *------------------------------------------------------
 *
 *
 *
 * PHP versions 5
 *
 *
 *
 * @author Willing Peng<pcq2006@gmail.com>
 * @copyright (C) 2013-2018
 * @version CVS: Id: AjaxController.php,v 1.0 2013-9-20 上午11:07:06 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Controller;

use Zend\View\Model\JsonModel;
use Zend\Config\Config;
use Zend\Config\Writer\PhpArray;
use Custom\Util\Util;
use BackEnd\Model\Nav\Position;
use BackEnd\Model\User\Acl;
use Custom\Mvc\ActionEvent;
class AjaxController extends AbstractController
{
	public function regionAction()
	{
		$pid = $this->params()->fromQuery('p');
		$regionTable = $this->_getTable('RegionTable');
		$assign = $regionTable->getRegionByPid($pid);
		print_r(json_encode($assign));die;
	}
	public function memberListAction()
	{
		$member = $this->_getTable('MemberTable');
		$rs = $member->getAll();
		$data = array();
		if ($rs) {
			foreach ($rs as $v) {
				$data[] = array(
					'UserID' => $v->UserID,
					'UserName' => $v->UserName,
					'Email' => $v->Email,
					'Mobile' => $v->Mobile,
				);
			}
		}
		return new JsonModel($data);
	}
	public function approvedAction()
	{
		$table = $this->_getTable('IdentityTable');
		$rs = array();
		$ids = $this->params()->fromPost('ids');
		$approved = $this->params()->fromPost('key');
		$approved = $approved == 1 ? 1 : 0;
		$ids = explode(',', trim($ids, ','));

		if($table->updateStatus($approved, $ids)) {
			$rs = array('code' => 0, 'msg' => '设置成功', 'data' => $ids);
		} else {
			$rs = array('code' => -1, 'msg' => '数据错误');
		}
		return new JsonModel($rs);
	}
	public function smsReplyApprovedAction() {
		$id = $this->params()->fromQuery('id');
		$result = $this->params()->fromQuery('result');
		$table = $this->_getTable('SmsReplyTable');
		$rs = array();
		if ($id) {
			$container = $this->_getSession();
			$up = array(
					'result' => $result,
					'deal_time' => date('Y-m-d H:i:s'),
					'deal_user' => $container->Name
			);
			$table->update($up, array('id' => $id));
			$rs = array('code' => 0, 'msg' => '已确认处理！', 'data' => $id);
		}
		return new JsonModel($rs);
	}
	public function saveCommonLinksAction()
	{
	    $category = $this->params()->fromPost('cate');
	    $html = $this->params()->fromPost('html');
	    $cache = array(
	    	'category' => $category,
	        'html' => $html,
	    );
	    $config = new Config($cache);


	    $writer = new PhpArray();

	    //echo $writer->toString($conf);die;
	    //@file_put_contents(APPLICATION_PATH.'/data/sys_config.php', $writer->toString($conf));
	    $writer->toFile(APPLICATION_PATH.'/data/commonLinks/'.$category.'.php', $config);
	    return new JsonModel(array('code' => 0, 'msg' => 'ok'));
	}
	public function delIconAction() {
		$linkTable = $this->_getTable('LinkTable');
		$id = $this->params()->fromPost('id');
		$icon = $this->params()->fromPost('icon');
		$up[$icon] = '';
		$link = $linkTable->getOneById($id);
		if ($link && $linkTable->updateFieldsByID($up, $id)) {
			$file = APPLICATION_PATH . '/public' . $link->{$icon};
			unlink($file);
			$rs = array('code' => 0, 'msg' => '已删除');
		} else {
			$rs = array('code' => -1, 'msg' => '删除失败');
		}
		return new JsonModel($rs);
	}
	public function getResourceAction() {
		$type = $this->params()->fromPost('type');
		$type = $type ? $type : 'add';
		$role = $this->params()->fromPost('role');
		$data = array();
		$table = $this->_getTable('ResourceTable');
		if (!$role) {
			$rs = array('code' => -200, 'msg' => '参数错误');
		} else {
			$acl = $this->getServiceLocator()->get('acl');
			$resources = $acl->acl->getResources();

			$hasResources = array();
			$noneResources = array();
			foreach($resources as $v){
				if($acl->acl->isAllowed($role , $v)){
					$hasResources[] = $v;
				}else{
					$noneResources[] = $v;
				}
			}
			if ($type == 'add') {
				$data = $table->getSourceByNames($noneResources);
			} else {
				$data = $table->getSourceByNames($hasResources);
			}
			$rs = array('code' => 0, 'msg' => 'sucess', 'data' => $data);
		}
		return new JsonModel($rs);
	}
	public function saveAclAction() {
		$type = $this->params()->fromPost('type');
		$role = $this->params()->fromPost('role');
		$resources = $this->params()->fromPost('resources');
		if ((!$type) || (!$role) || (empty($resources))) {
			$rs = array('code' => -200, 'msg' => '参数错误');
		} elseif (in_array($type, array('add', 'del'))) {
			if($resources){
				$acl = $this->getServiceLocator()->get('acl');
				$table = $this->_getTable('AclTable');
				if ($type == 'add') {
					$acl->acl->allow($role , $resources);
					foreach($resources as $resource){
						$myacl = new Acl();
						$myacl->ResourceName = $resource;
						$myacl->RoleName = $role;
						$myacl->AddTime = Util::getDateTime('Y-m-d h:i:s');
						$table->save($myacl);
					}
					$msg = "添加成功！";
				} else {
					$acl->acl->removeAllow($role , $resources);
					$table->remove($role , $resources);
					$msg = "删除成功！";
				}
				$acl->saveAcl();

				$this->trigger(ActionEvent::ACTION_INSERT);
				$this->trigger(ActionEvent::ACTION_DELETE);
				$rs = array('code' => 0, 'msg' => $msg);
			}
		}
		return new JsonModel($rs);
	}
	private function _addLink($id, $pos, $table) {
		$flg = 0;
		if (is_numeric($id)) {
			$position = new Position();
			$position->exchangeArray(array('link_id' => $id, 'pos' => $pos));
			$flg = $table->addLinkToPos($position);
		}
		return $flg;
	}
}
