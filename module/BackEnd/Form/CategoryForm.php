<?php
/**
 * CategoryForm.php
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
 * @version CVS: Id: UserForm.php,v 1.0 2013-9-16 下午10:12:03 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
class CategoryForm extends Form
{
	protected $inputFilter;
    function __construct() {
        parent::__construct ( 'category-form' );
        $this->setAttribute ( 'method', 'post' );
        $this->setAttribute ( 'enctype', 'multipart/form-data' );

        $this->add ( array (
        		'name' => 'id',
        		'options' => array (
        				'label' => 'ID'
        		)
        ) );

        $this->add ( array (
            'name' => 'name',
            'options' => array (
                'label' => '分类名称'
            ),
        	'attributes' => array(
        		'size' => 11,
        		'maxlength' => 200,
        		'required' => true,
        		'must' => '*'
        	)
        ) );

        $this->add ( array (
            'name' => 'alias',
            'options' => array (
                'label' => '别名'
            )
        ) );

        $this->add ( array (
        		'name' => 'pic',
        		'options' => array (
        				'label' => '分类图片'
        		)
        ) );
        $this->add ( array (
        		'name' => 'keyword',
        		'options' => array (
        				'label' => '关键字'
        		)
        ) );
        $this->add ( array (
        		'name' => 'parent',
        		'type' => '\Zend\Form\Element\Select',
        		'options' => array (
        				'label' => '上级分类'
        		)
        ) );
        $this->add ( array (
        		'name' => 'type',
        		'type' => '\Zend\Form\Element\Select',
        		'attributes' => array(
        				'required' => 'required',
        				'value' => '0',
        		),
        		'options' => array (
        				'label' => '类型',
        				'value_options' => array('DEFAULT'=>'DEFAULT'),
        		)
        ) );
        $this->add ( array (
        		'name' => 'order',
        		'options' => array (
        				'label' => '排序'
        		),
        		'attributes' => array(
        				'pattern' => '^[0-9]+$',
        				'notemsg' => '请输入非负整数',
        		),
        ) );
        $this->add ( array (
            'name' => 'submit',
            'attributes' => array (
                'value' => '提交',
                'type' => 'submit',
             )
        ) );
        $this->setInputFilter($this->getInputFilter());
    }
    public function getInputFilter(){
    	if(!$this->inputFilter){
    		$inputFilter = new InputFilter();
    		$factory = new InputFactory();
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'id',
    				'required' => false,
    		)));
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'name',
    				'required' => true,
    				'validators' => array(
    						array('name' => 'StringLength' , 'options' => array('min' => 1 , 'max' => 200)),
    				),
    		)));
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'alias',
    				'required' => false,
    				'validators' => array(
    						array('name' => 'StringLength' , 'options' => array('min' => 1 , 'max' => 200)),
    				),
    		)));
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'pic',
    				'required' => false,
    		)));
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'keyword',
    				'required' => false,
    		)));
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'parent',
    				'required' => false,
    		)));
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'type',
    				'required' => false,
    		)));
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'order',
    				'required' => true,
    				'filters'  => array(
    						array('name' => 'StripTags'),
    						array('name' => 'StringTrim'),
    				),
    		)));
    		$this->inputFilter = $inputFilter;
    	}

    	return $this->inputFilter;
    }
}