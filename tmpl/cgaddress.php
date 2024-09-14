<?php
/*
; Fields CG Address
; Recuperation des donnees GPS, nom d'une ville depuis geo.api.gouv.fr
; Version			: 1.0.0
; Package			: Joomla 4.x/5.x
; copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
; license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
; adaptation pour récupérer les codes GPS de la ville et vérification du nombre de reponses
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

$def_form = '<span class="cglibs d-none">';
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
$wa = Factory::getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('leaflet','https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$wa->registerAndUseStyle('cgaddress',$base.'css/cgaddress.css');
$wa->registerAndUseScript('leaflet','https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
if ((bool)Factory::getApplication()->getConfig()->get('debug')) { // Mode debug
    Factory::getApplication()->getDocument()->addScript(''.URI::root().'/media/plg_fields_cgaddress/js/cgaddress.js');
} else {
    $wa->registerAndUseScript('cgaddress',$base.'js/cgaddress.js');
}


