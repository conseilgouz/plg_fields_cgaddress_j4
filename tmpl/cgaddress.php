<?php
/*
* CG Address Plugin  - Joomla 4.x/5.x 
; copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
; license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

$value = $field->value;
$address = ""; $long = ""; $lat = "";

if ($value == '')
{
	return;
}
echo '<div class="cgaddress_field">';
$value = explode('|',$value);
$address=$value[0]; $long = $value[1]; $lat = $value[2];
$disp = $address;
if ( $field->fieldparams->get('showgps','true') == 'true') $disp .= ",GPS Long. : ".$long.',Lat. : '.$lat;
echo $disp;

if ($context == "com_contact.mail") { // send email : ignore next information
    echo '</div>'; // close opened div
    return;
} 
$def_form = '<span class="cglibs" style="display:none">';
$def_form .= '<input type="text" name="cgaddress" value="'.$address.'" class="cgaddress" />';
$def_form .= ', GPS Long. <span class="cglong">'.$long.'</span> - Lat. <span class="cglat" >'.$lat.'</span></span>';
echo $def_form;

$mapwidth = $field->fieldparams->get('mapwidth','100%');
$mapheight = $field->fieldparams->get('mapheight','300px');
$mapzoom = $field->fieldparams->get('mapzoom','15');
$showpopup = $field->fieldparams->get('showpopup','false');
$showiti = $field->fieldparams->get('showiti','false');

echo '<div class="cg_map" style="width:'.$mapwidth.';height:'.$mapheight.'"></div>';

echo '<input type="hidden" name="cgaddressfid" id="cgaddressfid" data-minlength="'.$minlength.'" data-mapzoom="'.$mapzoom.'" data-popup="'.$showpopup.'" data-iti="'.$showiti.'"/>';

echo "</div>"; // end of cgaddress_field div
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$base	= 'media/plg_fields_cgaddress/';
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('leaflet','https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$wa->registerAndUseStyle('cgaddress',$base.'css/cgaddress.css');
$wa->registerAndUseScript('leaflet','https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
if ((bool)Factory::getApplication()->getConfig()->get('debug')) { // Mode debug
    Factory::getApplication()->getDocument()->addScript(''.URI::root().'/media/plg_fields_cgaddress/js/cgaddress.js');
} else {
    $wa->registerAndUseScript('cgaddress',$base.'js/cgaddress.js');
}


