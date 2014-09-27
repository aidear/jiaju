<?php
namespace BackEnd\Controller;

use Zend\Session\Container;
use BackEnd\Form\LoginForm;
use Zend\Mvc\MvcEvent;
class LoginController extends AbstractController
{
	public function postDispatch(MvcEvent $e) {
		parent::postDispatch($e);
		$this->layout('layout/basic');
	}
	public function indexAction()
	{
		$container = $this->_getSession();
		if ($container->UserID) {
			return $this->redirect()->toUrl('/');
		}
		$url = $this->params()->fromQuery('url' , '');
		$form = new LoginForm();
		$request = $this->request;
		$msg = array();
		if($this->request->isPost()){
			$username = $this->params()->fromPost('username');
			$pwd = $this->params()->fromPost('password');
			$validate = $this->params()->fromPost('validateCode');
			$data = $request->getPost();
// 			$form = new LoginForm();
			$form->setData($data);
			if($username && $pwd){
				$loginFailCount = isset($_COOKIE['AdminFailCount']) ? $_COOKIE['AdminFailCount'] : 0;
				if (isset($_POST['validateCode']) && strtolower($_SESSION['captcha_login_code']) != strtolower($validate)) {unset($_SESSION['captcha_login_code']);
// 					$this->_message('验证码错误', 'error');
					$msg['error'][] = '验证码错误';
// 					return $this->redirect()->toUrl('/login');
				} else {
					$sm = $this->getServiceLocator();
					$userTable = $sm->get('UserTable');
					$user = $userTable->getUserByName($username);

					if ($user && $user->Password == md5($pwd)) {
						$container = $this->_getSession();
						$container->UserID = $user->ID;
						$container->Name = $user->UserName;
						$container->Role = $user->Role;

						$adminLogTable = $this->_getTable('AdminLogTable');
						$logInfo = array('user_id' => $user->ID, 'user_name' => $user->UserName, 'opt_type' => 1, 'info' => 'login');
						$adminLogTable->addLogInfo($logInfo);
						setcookie('AdminFailCount', '', time()-3600);
						if($url = $this->params()->fromQuery('url')){
							return $this->redirect()->toUrl($url);
						}else{
							return $this->redirect()->toRoute('home');
						}
					} elseif ($user) {
// 						$this->_message('用户名或密码错误', 'error');
						setcookie('AdminFailCount', $loginFailCount + 1);
						$msg['error'][] = '用户名或密码错误';
// 						return $this->redirect()->toUrl('/login');
					} else {
// 						$this->_message('没有这个用户:' . $username, 'error');
						$msg['error'][] = '没有这个用户:' . $username;
// 						return $this->redirect()->toUrl('/login');
					}
				}
			}else{
				throw new \Exception('error');
// 				$this->_message('用户名或密码不能为空', 'error');
				return $this->redirect()->toUrl('/login');
			}
		}

		$return = array('url' => $url , 'form' => $form, 'msg' => $msg);
		return $return;
	}
	function captchaAction()
	{
		unset($_SESSION['captcha_login_code']);
		$captcha = new \Custom\Captcha\Image(array(
				'Expiration' => '300',
				'wordlen' => '4',
				'Height' => '28',
				'Width' => '77',
				'writeInFile'=>false,
				'Font' => APPLICATION_PATH.'/data/AdobeSongStd-Light.otf',
				'FontSize' => '18',
				'DotNoiseLevel' => 0,
				'ImgDir' => '/images/BackEnd'
		));
		$imgName = $captcha->generate();
		$_SESSION['captcha_login_code'] = $captcha->getWord();
		die;
	}
	/**
	 * 登出
	 */
	public function logoutAction(){
		$container = new Container('user');
		$adminLogTable = $this->_getTable('AdminLogTable');
		$logInfo = array('user_id' => $container->UserID, 'user_name' => $container->Name, 'opt_type' => 2, 'info' => 'logout');
		$adminLogTable->addLogInfo($logInfo);
		$container->getManager()->destroy();
		return $this->redirect()->toRoute('backend' , array('controller' => 'login' , 'action' => 'index'));
	}
}