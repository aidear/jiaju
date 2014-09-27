<?php 
namespace BackEnd\Helper;

use Zend\View\Helper\AbstractHelper;
class Message extends AbstractHelper
{
	public function __invoke() {
		$msg = $this->view->flashMessages();
		$html = '';
		if(!empty($msg['error'])) {
			$html .= '<div class="alert alert-error fade in">
		            <button class="close" data-dismiss="alert" type="button">×</button>';
			foreach($msg['error'] as $v) {
				$html .= "<p>{$v}</p>";
			}
			$html .= '</div>';
		}
		            
		if(!empty($msg['success'])) {
			$html .= '<div class="alert alert-success fade in">
		            <button class="close" data-dismiss="alert" type="button">×</button>';
			foreach($msg['success'] as $v) {
				$html .= "<p>{$v}</p>";
			}
			$html .= '</div>';
		}
		return $html;
	}
}