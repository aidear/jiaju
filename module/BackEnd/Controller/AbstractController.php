<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace BackEnd\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\EventManager\EventManager;
use Custom\Mvc\ActionEvent;
abstract class AbstractController extends AbstractActionController {
	const LIMIT = 15;
	const MSG_SUCCESS = 'success';    //message
	const MSG_ERROR = 'error';    //message
	protected $actionEvent;
	protected $actionEvents;
	protected $action;
    /**
     * @var 请求参数，包含  get 和 route 参数
     */
    protected $params = array();

    /**
     * (non-PHPdoc)
     * @see Zend\Mvc\Controller\AbstractController::attachDefaultListeners()
     */
    protected function attachDefaultListeners() {
		parent::attachDefaultListeners();
		$events = $this->getEventManager();
		$this->events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 1000);
		$this->events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'postDispatch'), -1000);
	}

	/**
	 * Dispatch 前，将需要的数据获取到，附加到 protected 属性中
	 *
	 * @param MvcEvent $e
	 */
	public function preDispatch(MvcEvent $e) {
	    // 注册 get + route 参数
	    $this->params = array_merge($this->params()->fromQuery(), $this->params()->fromRoute());
	}

	/**
	 * Dispatch 后，将需要的数据附加到 viewModel 中
	 *
	 * @param MvcEvent $e
	 */
	public function postDispatch(MvcEvent $e) {
	    $layout = $e->getViewModel();
	    if (!$layout instanceof ViewModel) {
	        return;
	    }
	}
	/**
	 * 根据tableName获取table 实列
	 * @param string $tableName
	 */
	protected function _getTable($tableName)
	{
		if (empty($tableName)) {
			throw new \Exception('tableName is Empty');
		}
		if (!isset($this->$tableName)) {
			$this->$tableName = $this->getServiceLocator()->get($tableName);
		}
		return $this->$tableName;
	}
	/**
	 * 读取配置文件
	 * @param string $configName
	 * @throws \Exception
	 * @return Ambigous <multitype:, \Zend\Config\Config, unknown>
	 */
	protected function _getConfig($configName){
		if(empty($configName)){
			throw new \Exception('configName is Empty');
		}

		$filename = APPLICATION_PATH . '/module/BackEnd/config/' . $configName . '.config.php';
		if(!file_exists($filename)){
			throw new \Exception('not found ' . $filename);
		}

		return \Zend\Config\Factory::fromFile($filename);
	}
	/**
	 * 获取Session
	 * @param string $nameSpace
	 * @return \Zend\Session\Container
	 */
	protected function _getSession($nameSpace = 'user'){
		return new Container($nameSpace);
	}
	/**
	 * 发送信息
	 * @param string $message
	 * @param string $type
	 * @return \Custom\Mvc\Controller\AbstractActionController
	 */
	protected function _message($message , $type = self::MSG_SUCCESS){
		$this->flashMessenger()->setNamespace($type);
		$this->flashMessenger()->addMessage($message);
		return $this;
	}
	function setActionEvent(ActionEvent $event)
	{
		$this->actionEvent = $event;
		$route = $this->getEvent()->getRouteMatch();

		$this->actionEvent->setController($route->getParam('controller'));

		$request = $this->getRequest();
		if($request->isPost()){
			$params = $request->getPost();
		}else{
			$params = $request->getQuery();
		}

		$this->actionEvent->setParams($params);
		$this->actionEvent->setSession(new Container('user'));
	}
	function getActionEvent()
	{
		if(!$this->actionEvent){
			$this->setActionEvent(new ActionEvent());
		}

		return $this->actionEvent;
	}

	function setActionEvents(EventManager $events)
	{
		$this->actionEvents = $events;
		$this->actionEvents->setIdentifiers('action');
	}

	function getActionEvents()
	{
		if(!$this->actionEvents){
			$this->setActionEvents(new EventManager());
		}

		return $this->actionEvents;
	}

	function trigger($action)
	{
		$e = $this->getActionEvent();
		$e->setAction($action);
		$this->getActionEvents()->trigger(ActionEvent::EVENT_ACTION , $e);
	}
	protected  function _getOrder($prefixUrl, $orderList, $removePageParams)
	{
		$order = array();
		foreach ($orderList as $k=>$v) {
			$order[$v] = array();
			$tParams = array();
			$removePageParams;
			$href = $prefixUrl;
			$class = 'order_down';
			$tParams['orderField'] = $v;
			$tParams['orderType'] = 'DESC';
			if (isset($removePageParams['orderField']) && $removePageParams['orderField'] == $v) {
				if (isset($removePageParams['orderType']) && strtolower($removePageParams['orderType']) == 'desc') {
					$tParams['orderType'] = 'ASC';
					$class = 'order_up';
				}
			}
			$href = $prefixUrl.'?'.http_build_query(array_merge($removePageParams, $tParams));
			$order[$v] = array('href' => $href, 'class' => $class);
		}

		return $order;
	}
}