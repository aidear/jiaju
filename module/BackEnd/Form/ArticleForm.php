<?php
namespace BackEnd\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
class ArticleForm extends Form
{
	protected $inputFilter;
    function __construct() {
        parent::__construct ( 'article-form' );
        $this->setAttribute ( 'method', 'post' );
        $this->setAttribute ( 'enctype', 'multipart/form-data' );

        $this->add ( array (
        		'name' => 'id',
        		'options' => array (
        				'label' => 'ID'
        		)
        ) );

        $this->add ( array (
            'name' => 'title',
            'options' => array (
                'label' => '标题'
            ),
        	'attributes' => array(
        		'size' => 11,
        		'maxlength' => 200,
        		'required' => true,
        		'must' => '*'
        	)
        ) );

        $this->add ( array (
            'name' => 'subhead',
            'options' => array (
                'label' => '副标题'
            )
        ) );


        $this->add ( array (
        		'name' => 'keyword',
        		'options' => array (
        				'label' => '关键字'
        		)
        ) );
        $this->add ( array (
        		'name' => 'summary',
        		'type' => 'Zend\Form\Element\Textarea',
        		'options' => array (
        				'label' => '摘要'
        		),
        		'attributes' => array(
        				'cols' => 105,
        				'rows'  => 5
        		),
        ) );
        $this->add ( array (
        		'name' => 'content',
        		'type' => 'Zend\Form\Element\Textarea',
        		'options' => array (
        				'label' => '内容'
        		),
        		'attributes' => array(
        			'id' => 'myEditor',
        			'class' => 'cleditor',
        		),
        ) );
        $this->add ( array (
        		'name' => 'img',
        		'options' => array (
        				'label' => '图片'
        		)
        ) );
        $this->add ( array (
        		'name' => 'status',
        		'type' => '\Zend\Form\Element\Select',
        		'options' => array (
        				'label' => '状态'
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
    				'name' => 'title',
    				'required' => true,
    				'validators' => array(
    						array('name' => 'StringLength' , 'options' => array('min' => 1 , 'max' => 200)),
    				),
    		)));
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'subhead',
    				'required' => false,
    		)));
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'summary',
    				'required' => false,
    				'validators' => array(
    						array('name' => 'StringLength' , 'options' => array('min' => 1 , 'max' => 200)),
    				),
    		)));
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'keyword',
    				'required' => false,
    		)));
    		$inputFilter->add($factory->createInput(array(
    				'name' => 'img',
    				'required' => false,
    		)));

    		$inputFilter->add($factory->createInput(array(
    				'name' => 'content',
    				'required' => true,
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