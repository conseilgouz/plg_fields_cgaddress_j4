<?php
/*
* CG Address Plugin  - Joomla 4.x/5.x/6.x
; copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
; license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
namespace ConseilGouz\Plugin\Fields\Cgaddress\Extension;
defined('_JEXEC') or die;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\Component\Fields\Administrator\Plugin\FieldsPlugin;
use Joomla\Event\SubscriberInterface;
/**
 * Fields Text Plugin
 *
 */
class Cgaddress extends FieldsPlugin implements SubscriberInterface
{
    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since   5.3.0
     */
    public static function getSubscribedEvents(): array
    {
        return array_merge(parent::getSubscribedEvents(), [
            'onCustomFieldsPrepareDom' => 'prepareDom',
        ]);
    }
    public function prepareDom($event) //($field, \DOMElement $parent, Form $form)
    {
        $field = $event[0];
        $parent = $event[1];
        $form = $event[2];

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
