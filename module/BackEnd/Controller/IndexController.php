<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BackEnd\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

class IndexController extends AbstractController
{
	public function postDispatch(MvcEvent $e) {
		if ($this->layout()->terminate()) {
			return;
		}
		parent::postDispatch($e);
		$this->layout('layout/main');
	}
    public function indexAction()
    {
        return new ViewModel();
    }
    public function menuAction(){
    	$id = $this->params()->fromPost('id');
    	$assign['id'] = $id;
    	$v = new ViewModel($assign);
    	$v->setTemplate('layout/leftSidebar');
    	$v->setTerminal(true);
    	return $v;
    }
}
