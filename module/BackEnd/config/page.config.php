<?php
/**
 * page.config.php
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
 * @version CVS: Id: page.config.php,v 1.0 2013-9-20 下午9:05:49 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */

return array (
	array(
		'label' => '用户',
		'id' => 5,
		'uri' => '',
		'pages' => array(
	        array(
        		'label' => '帐号权限',
	        	'id' => 50,
        		'uri' => '',
	        	'order' => 1,
        		'pages' => array(
        				array (
        						'label' => '角色管理',
        						'id' => 500,
        						'controller' => 'role',
        				        'action' => 'index',
        						'resource' => 'role_index'
        				),
        				array (
        						'label' => '资源管理',
        						'id' => 501,
        						'controller' => 'resource',
        				        'action' => 'index',
        						'resource' => 'resource_index'
        				),
        				array (
        						'label' => '帐号管理',
        						'id' => 502,
        						'controller' => 'user',
        						'action' => 'index',
        						'resource' => 'user_index'
        				),
        		)
			),
			array(
					'label' => '会员管理',
					'id' => 51,
					'uri' => '',
					'order' => 10,
					'pages' => array(
							array(
									'label' => '会员列表',
									'id' => 510,
									'controller' => 'member',
									'action' => 'index',
									'resource' => 'member_index',
							),
							array(
									'label' => '新增会员',
									'id' => 514,
									'controller' => 'member',
									'action' => 'save',
									'resource' => 'member_save',
									'visible' => false,
							),
							array(
									'label' => '编辑会员资料',
									'id' => 515,
									'controller' => 'member',
									'visible' => false,
							)
					)
			)
	    ),
	),
	array(
			'label' => '设置',
			'uri' => '',
			'id' => 1,
			'order' => 30,
			'pages' => array(
					array(
							'label' => '系统设置',
							'id' => 10,
							'uri' => '',
							'pages' => array(
									array(
											'label' => '基本设置',
											'id' => 101,
											'controller' => 'config',
											'action' => 'index',
											'resource' => 'config_index',
									),
									array(
											'label' => '邮件服务器',
											'id' => 102,
											'controller' => 'config',
											'action' => 'mail',
											'resource' => 'config_mail',
									),
									array(
											'label' => '新增配置',
											'id' => 103,
											'controller' => 'config',
											'action' => 'add',
											'resource' => 'config_add',
											'visible' => false,
									)
							),
					),
			)
	),
	array(
		'label' => '内容管理',
		'id' => 2,
		'uri' => '',
		'order' => 1,
		'pages' => array(
				array(
						'label' => '文章管理',
						'id' => 20,
						'uri'	 => '',
						'pages' => array(
								array(
										'label' => '文章列表',
										'id' => 201,
										'controller' => 'article',
										'action'	 => 'index',
										'resource'   => 'article_index',
								),
								array(
										'label' => '分类列表',
										'id' => 202,
										'controller' => 'category',
										'action'	 => 'index',
										'resource'   => 'category_index',
								),
								array(
										'label' => '分类编辑',
										'id' => 203,
										'controller' => 'category',
										'action'	 => 'save',
										'resource'   => 'category_save',
										'visible'	=> false,
								),
						),
				),
		),
	),
	array(
			'label' => '网站管理',
			'id' => 4,
			'controller' => 'site',
			'action' => 'index',
			'resource' => 'site_index',
			'order' => 10,
			'pages' => array(
					array(
							'label' => '网站统计',
							'controller' => 'site',
							'action' => 'index',
							'resource' => 'site_index',
					),
					array(
							'label' => '广告申请',
							'controller' => 'site',
							'action' => 'advApply',
							'resource' => 'site_advApply',
					),
					array(
							'label' => '意见建议',
							'controller' => 'site',
							'action' => 'feedback',
							'resource' => 'site_feedback',
					),
					array(
							'label' => '短信回复列表',
							'controller' => 'site',
							'action' => 'smsReply',
							'resource' => 'site_smsReply',
					),
					array(
							'label' => '满意度调查',
							'controller' => 'site',
							'action' => 'survey',
							'resource' => 'site_survey',
					),
			)
	),
);