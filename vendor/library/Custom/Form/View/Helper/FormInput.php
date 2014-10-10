<?php
/**
 * FormInput.php
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
 * @version CVS: Id: FormInput.php,v 1.0 2013-10-6 下午10:08:56 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\Exception;

class FormInput extends \Zend\Form\View\Helper\FormInput
{
    public function render(ElementInterface $element)
    {
        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes          = $element->getAttributes();
        $attributes['name']  = $name;
        $attributes['type']  = $this->getType($element);
        $attributes['value'] = $element->getValue();
        $attributes['label'] = $element->getLabel();
        $errmsg = $element->getMessages();

        $re = sprintf(
            '<label class="col-sm-2 control-label" for="%s">%s</label><div class="col-sm-6"><input autocomplete="off" class="form-control" %s%s',
            $attributes['name'],
            $attributes['label'],
            $this->createAttributesString($attributes),
            $this->getInlineClosingBracket()
        );
        if (isset($attributes['colorSelect'])) {
        	$re .= "&nbsp;<input type=\"color\" name='c_{$name}' />";
        }
        $span = '';
        $errClass = '';
        if ($errmsg) {
        	$errClass = ' error';
        	foreach($errmsg as $msg){
        		$span .= "<span class='help-inline'>{$msg}</span>";
        	}
        } elseif (isset($attributes['notemsg'])){
            $span .= "<span class='help-inline note'><i class='icon-info-sign'></i>{$attributes['notemsg']}</span>";
        }
        if (!$errmsg && isset($attributes['must'])) {
        	$span .= "<span style='color:red;'> {$attributes['must']} </span>";
        }

        $re = '<div class="form-group'.$errClass.'">' . $re;
		if ($span) {
			$span = "<div class='col-sm-3'>". $span . "</div>";
		}
        $re .= '</div>'.$span.'</div>';

        return $re;
    }
}