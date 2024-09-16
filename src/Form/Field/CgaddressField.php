<?php
/*
* CG Address Plugin  - Joomla 4.x/5.x 
; copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
; license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
namespace ConseilGouz\Plugin\Fields\Cgaddress\Form\Field;

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
class CgaddressField extends FormField
{
    protected $type = 'cgaddress';

    public function getInput()
    {
        $base	= 'media/plg_fields_cgaddress/';
        $def_form = '<div class="cgaddress_field">';
        $minlength = (int)$this->getAttribute('minlength');
        $mapheight = $this->getAttribute('mapheight');
        $mapwidth = $this->getAttribute('mapwidth');
        $mapzoom = (int)$this->getAttribute('mapzoom');
        $showpopup = $this->getAttribute('showpopup');
        $showiti = $this->getAttribute('showiti');
		$long = ""; $lat = "";$cgaddress = "";
		if ($this->value != '') {
			$val = explode('|',$this->value);
			$cgaddress=$val[0];
			$long = $val[1]; $lat = $val[2];
		}
		/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->registerAndUseStyle('leaflet','https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
        $wa->registerAndUseStyle('cgaddress',$base.'css/cgaddress.css');
        $wa->registerAndUseScript('leaflet','https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
        if ((bool)Factory::getApplication()->getConfig()->get('debug')) { // Mode debug
            Factory::getApplication()->getDocument()->addScript(''.URI::root().'/media/plg_fields_cgaddress/js/cgaddress.js');
        } else {
            $wa->registerAndUseScript('cgaddress',$base.'js/cgaddress.js');
        }
		$def_form .= '<input type="hidden" name="'.$this->name.'" id="'.$this->id.'" value="' . $this->value . '" />';
		$def_form .= '<input type="hidden" name="cgaddressfid" id="cgaddressfid" value="'.$this->id.'" data-id="'.$this->id.'" data-minlength="'.$minlength.'" data-mapzoom="'.$mapzoom.'" data-popup="'.$showpopup.'" data-iti="'.$showiti.'"/>';
		$def_form .= '<input type="text" name="cgaddress" value="'.$cgaddress.'" class="cgaddress" />';
        $def_form .= '<button type="button" class="btn btn-primary" id="cgaddress_search"><span class="icon-search" aria-hidden="true"></span></button>';
		$def_form .= HTMLHelper::_('select.genericlist',array(), 'cgaddress', "class=\"inputbox\" style=\"margin:0;display:none\"", 'value', 'text', null,'cgaddress_select'); 
		$cgstyle = "";
		if ($cgaddress == '') $cgstyle= " style='display:none'";
		$def_form .= '<span class="cglibs" '.$cgstyle.'>';
		if ($this->getAttribute('showgps')) $def_form .= ', GPS Long. <span class="cglong">'.$long.'</span> - Lat. <span class="cglat" >'.$lat.'</span></span>';
		$def_form .= '</span><div id="cg_result"></div>';
        $def_form .= '<div class="cg_map" style="width:'.$mapwidth.';height:'.$mapheight.'"></div>';
        $def_form .= '</div>'; // end of cgaddress_field div
        return $def_form;
    }
}