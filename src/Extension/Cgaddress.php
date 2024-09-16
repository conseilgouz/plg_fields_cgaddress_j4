<?php
/*
* CG Address Plugin  - Joomla 4.x/5.x 
; copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
; license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
namespace ConseilGouz\Plugin\Fields\Cgaddress\Extension;
defined('_JEXEC') or die;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\Component\Fields\Administrator\Plugin\FieldsPlugin;
/**
 * Fields Text Plugin
 *
 */
class Cgaddress extends FieldsPlugin
{
	public function onCustomFieldsPrepareDom($field, \DOMElement $parent, Form $form)
    {
        $fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);
        
        if (!$fieldNode)
        {
            return $fieldNode;
        }
        
        $fieldNode->setAttribute('country', $field->fieldparams->get('country','fr'));
        $fieldNode->setAttribute('maxlength', $field->fieldparams->get('maxlength','5'));
        $fieldNode->setAttribute('showcity', $field->fieldparams->get('showcity','true') == 'true' ? true : false);
        $fieldNode->setAttribute('showinsee', $field->fieldparams->get('showinsee','true') == 'true' ? true : false);
        $fieldNode->setAttribute('showgps', $field->fieldparams->get('showgps','true') == 'true' ? true : false );
		$fieldNode->setAttribute('type', 'cgaddress');
		$fieldNode->setAttribute('filter', 'none');  

		FormHelper::addFieldPrefix('ConseilGouz\Plugin\Fields\Cgaddress\Form\Field');
        return $fieldNode;
    }

}
