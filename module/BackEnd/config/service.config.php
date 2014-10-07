<?php
use Zend\Session\Container;

use Zend\Session\SessionManager;

use BackEnd\Model;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
return array(
		'factories' => array(
				'UserTable' =>  function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\User\User());
					return new Model\User\UserTable('adminuser', $dbAdapter, null, $resultSetPrototype);
				},
				'RoleTable' =>  function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\User\Role());
					return new Model\User\RoleTable('sys_role', $dbAdapter, null, $resultSetPrototype);
				},
				'ResourceTable' =>  function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\User\Resource());
					return new Model\User\ResourceTable('sys_resource', $dbAdapter, null, $resultSetPrototype);
				},
				'AclTable' =>  function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\User\Acl());
					return new Model\User\AclTable('sys_acl', $dbAdapter, null, $resultSetPrototype);
				},
				'RegionTable' =>  function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\User\Region());
					return new Model\User\RegionTable('region', $dbAdapter, null, $resultSetPrototype);
				},
				'ConfigTable' =>  function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\System\Config());
					return new Model\System\ConfigTable('sys_config', $dbAdapter, null, $resultSetPrototype);
				},
				'MemberTable' =>  function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\User\Member());
					return new Model\User\MemberTable('member', $dbAdapter, null, $resultSetPrototype);
				},
				'AdminLogTable' =>  function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\System\AdminLog());
					return new Model\System\AdminLogTable('admin_log', $dbAdapter, null, $resultSetPrototype);
				},
				'cache' => function($sm){
					$config = $sm->get('Config');
					$path = $config['cache']['adapter']['options']['cache_dir'];
					if(!is_dir($path)){
						if(!mkdir($path , 0777 , true)){
							throw new \Exception('无法创建' . $path);
						}
					}
					$cache = \Zend\Cache\StorageFactory::factory($config['cache']);
					return $cache;
				},
				'acl' => function($sm){
					$dbAdapter = $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

					return new BackEnd\Model\User\MyAcl($dbAdapter, $sm->get('cache'));
				},
				'CategoryTable' => function($sm){
					$dbAdapter = $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

					return new BackEnd\Model\Category\CategoryTable('category', $dbAdapter);
				},
				'backendNav' => '\Custom\Navigation\Service\BackendNavigation',
				'Zend\Session\SessionManager' => function ($sm) {
	            $config = $sm->get('config');
	            if (isset($config['session'])) {
	                $session = $config['session'];

	                $sessionConfig = null;
	                if (isset($session['config'])) {
	                    $class = isset($session['config']['class'])  ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
	                    $options = isset($session['config']['options']) ? $session['config']['options'] : array();
	                    $sessionConfig = new $class();
	                    $sessionConfig->setOptions($options);
	                }

	                $sessionStorage = null;
	                if (isset($session['storage'])) {
	                    $class = $session['storage'];
	                    $sessionStorage = new $class();
	                }

	                $sessionSaveHandler = null;
	                if (isset($session['save_handler'])) {
	                    // class should be fetched from service manager since it will require constructor arguments
	                    $sessionSaveHandler = $sm->get($session['save_handler']);
	                }

	                $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);

	                if (isset($session['validator'])) {
	                    $chain = $sessionManager->getValidatorChain();
	                    foreach ($session['validator'] as $validator) {
	                        $validator = new $validator();
	                        $chain->attach('session.validate', array($validator, 'isValid'));

	                    }
	                }
	            } else {
	                $sessionManager = new SessionManager();
	            }
	            Container::setDefaultManager($sessionManager);
	            return $sessionManager;
	        },
		),
);
?>
