<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BackEnd;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->bootstrapSession($e);
        $eventManager->attach(MvcEvent::EVENT_ROUTE, function($e) use ($eventManager){
        	$sm = $e->getApplication()->getServiceManager();
        	$route = $e->getRouteMatch();
        	$controller = $route->getParam('controller');
        	$controller = strtolower(substr($controller, strrpos($controller, '\\')+1));
        	$action = $route->getParam('action');
        	$session = new Container('user');
        	if($controller && $action){
        		//验证是否登录
        		if($controller == 'login' || ($controller == 'acl' && $action == 'clearCache')
        		|| $controller == 'ajax'
        				|| (isset($session->UserID) && $session->Role == 'admin')){
        			return;
        		}
        		//远程调用系统方法
        		if ($controller == 'system') {
        			return;
        		}
        		if(!isset($session->UserID)){
        			header('Location:/login');
        			exit;
        		}
        		//admin log



        		$adminLogTable = $sm->get('AdminLogTable');
        		$params = $e->getRequest()->getQuery()->toArray();
        		$log_info = "Controller:[{$controller}];Action:[{$action}];Query:[".http_build_query($params)."]";
        		$logInfo = array('user_id' => $session->UserID, 'user_name' => $session->Name,
        				'opt_type' => 3, 'info' => $log_info,
        				'controller' => $controller, 'action' => $action);
        		$adminLogTable->addLogInfo($logInfo);
        		//验证ACL
        		$controller = strtolower($controller);
        		$resource = $controller . '_' . $action;
        		$myAcl = $sm->get('acl');
        		try{
        			if(empty($session->Role) || !$myAcl->acl->isAllowed($session->Role
        					, $resource)){
        				throw new \Exception('Not Allow');
        			}
        		}catch(\Exception $err){
        			$e->setError('error');
        			$e->setParam('exception', $err);
        			$eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $e);
        		}

        	}
        });
        $translator = $e->getApplication()
		->getServiceManager()->get('translator');
        \Zend\Validator\AbstractValidator::setDefaultTranslator($translator);
        $sys_config = array();
        if (file_exists('./data/sys_config.php')) {
        	$sys_config = include'./data/sys_config.php';
        }
        define("__SHOP_URL", isset($sys_config['shop_url']) ? $sys_config['shop_url'] : '');
        define('__LIST_ORDER', 'ASC');
        define('__IMG_URL', '');
    }
    public function bootstrapSession($e)
    {
    	$session = $e->getApplication()
    	->getServiceManager()
    	->get('Zend\Session\SessionManager');
    	$session->start();

    	$container = new Container('initialized');
    	if (!isset($container->init)) {
    		$session->regenerateId(true);
    		$container->init = 1;
    	}
    }
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    public function getServiceConfig()
    {
    	return include __DIR__ . '/config/service.config.php';
    }
    public function getViewHelperConfig(){
    	return include __DIR__ . '/config/viewhelper.config.php';
    }
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/',
                	'Custom' => dirname(dirname(__DIR__)) . '/vendor/library/Custom/'
                ),
            ),
        );
    }
}
