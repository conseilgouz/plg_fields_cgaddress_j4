/**
; Fields CG Address
; Recuperation des donnees GPS, nom d'une ville depuis geo.api.gouv.fr
; Version			: 1.0.0
; Package			: Joomla 4.x/5.x
; copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
; license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
; openstreemap/leaflet doc : https://leafletjs.com/index.html
 */
var cgtimeout,callDelay=500,cgmap=[],cgmarker=[],isset=[],search=[],observer,
	cglong=[],cglat=[],cglibs=[],cgaddress=[],cgaddressfid=[],cgonelist=[],zoom=[],popup=[],iti=[],
	cgapiUrl="https://api-adresse.data.gouv.fr/search/?q=";

document.addEventListener('DOMContentLoaded', function() {

    mapix = 0;
    document.querySelectorAll('.cgaddress_field').forEach( function (afield) {
        afield.setAttribute('map_id',mapix);
        afield.setAttribute('id','cgmap_'+mapix);
        amap = afield.querySelector(".cg_map");
        // init fields
        cglong[mapix] = afield.querySelector(".cglong")
        cglat[mapix] = afield.querySelector(".cglat")
        isset[mapix] = true;
        if (cglong[mapix].innerHTML == "") { // default : tour eiffel
            cglong[mapix].innerHTML = 2.294844;
            cglat[mapix].innerHTML = 48.85773;
            zoom[mapix] = 13; // default zoom
            isset[mapix] = false;
        } 
      	cgaddress[mapix] =  afield.querySelector("input.cgaddress");
        cgaddressfid[mapix] = afield.querySelector('#cgaddressfid');
        zoom[mapix] = cgaddressfid[mapix].getAttribute('data-mapzoom');
        popup[mapix] = cgaddressfid[mapix].getAttribute('data-popup');
        iti[mapix] = cgaddressfid[mapix].getAttribute('data-iti');
        // create map
        cgmap[mapix] = (L.map(amap).setView([cglat[mapix].innerHTML, cglong[mapix].innerHTML], zoom[mapix]));
        mapix +=1;
    });

    if (document.body.classList.contains('admin')) { // conflict Leaflet and Bootstrap  
        first_map = document.querySelector('.cgaddress_field');
        mapparent = first_map.parentNode.parentNode.parentNode.parentNode.parentNode;
        observer = new MutationObserver(function(){
                        if(mapparent.style.display != 'none'){
                            document.querySelectorAll('.cgaddress_field').forEach( function (amap) {
                                mapid = amap.getAttribute('map_id');
                                cgmap[mapid].invalidateSize();
                            });
                        }
                   });
        observer.observe(mapparent, {attributes: true});
     };

    document.querySelectorAll('.cgaddress_field').forEach( function (amap) {
        mapid = amap.getAttribute('map_id');
        cgmap[mapid].scrollWheelZoom.disable();

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(cgmap[mapid]);    
        if (isset[mapid]) { // add marker
            cgmarker[mapid] = L.marker([cglat[mapid].innerHTML, cglong[mapid].innerHTML]).addTo(cgmap[mapid]);
            if (popup[mapid] == 'true') {
                createPopup(mapid,cglat[mapid].innerHTML, cglong[mapid].innerHTML);
            }
        }
    /* déclenchement recherche si mini atteint */

        ['click', 'touchstart', 'mouseup','keyup' ].forEach(type => {
            cgaddress[mapid].addEventListener(type,function(){ 
                id = this.parentNode.getAttribute('map_id');
                minlength = cgaddressfid[id].getAttribute('data-minlength')
                if (cgaddress[id].value.length <  minlength ) {
                    cleardisplay(id);
                    return
                }
            /*
                //recherche si sup à minlength
            var e=cgaddress.value ;
			url = apiUrl;
			getAddrFr(e,url);
            */
            })
        });
    /* déclenchement de la recherche sur action bouton */
        search[mapid] = amap.querySelector("#cgaddress_search");
        if (search[mapid]) {
            ['click', 'touchstart' ].forEach(type => {
                search[mapid].addEventListener(type,function(){ 
                    id = this.parentNode.getAttribute('map_id');
                    minlength = cgaddressfid[id].getAttribute('data-minlength')
                    cleardisplay(id);
                    if (cgaddress[id].value.length < minlength ) {
                        return
                    }
                    var e=cgaddress[id].value ;
                    url = cgapiUrl;
                    getAddrFr(this.parentNode,e,url)
                })
            })
        }
      	cglibs[mapid] = amap.querySelector(".cglibs")

       	cgonelist[mapid] = amap.querySelector("#cgaddress_select");
        if (cgonelist[mapid]) {
    /* sélection dans liste de résultats */
            cgonelist[mapid].addEventListener('change',function() {
                id = this.parentNode.getAttribute('map_id');
                sel = this.selectedOptions[0].value;
                addr = this.selectedOptions[0].text;
                if (sel) {
                    lonlat = sel.split(',');
                    cgmap[id].setView([lonlat[1],lonlat[0]], 15);
                    if (cgmarker[id]) cgmarker[id].remove();
                    cgmarker[id] = L.marker([lonlat[1], lonlat[0]]).addTo(cgmap[id]);
                    cgmarker[id].bindPopup(cgaddress[id].value);
                    cgonelist[id].style.display = "none";
                    $val = cgaddressfid[id].value;
                    $res = this.parentNode.querySelector("#"+$val);
                    $res.value = addr + '|' + lonlat[0] + '|' + lonlat[1];
                    cgaddress[id].value = addr;
                    cglong[id].innerHTML = lonlat[0];
                    cglat[id].innerHTML = lonlat[1];
                    if (popup == 'true') {
                        createPopup(id,lonlat[1], lonlat[0]);
                    }
                    cglibs[id].style.display = 'inline-flex';
                }
            })
        }
    });

     
	
});
/* recheche de l'adresse */
function getAddrFr(amap,e,url) {
    mapid = amap.getAttribute('map_id');
	result = amap.querySelector("#cg_result");
	cleardisplay(mapid);
	result.style.display = 'inline-flex';
	result.innerHTML = "Chargement...";

	clearTimeout(cgtimeout),
	cgtimeout=setTimeout(function(){
		const xhr = new XMLHttpRequest();
		xhr.open('GET', url+e, true); // Set the headers
		xhr.onreadystatechange = () => {
        // Request not finished
			if (xhr.readyState !== 4) {
				return;
			} // Request finished and response is ready
			if (xhr.status === 200) {
				var r = JSON.parse(xhr.responseText);
				$val = cgaddressfid[mapid].value;
				$res = amap.querySelector("#"+$val);
				if (r.features.length === 0) { // no result
					result.innerHTML = "Aucune réponse.&nbsp;";
					cgonelist[mapid].style.display = "none";
				}
				if (r.features.length ===1) { // one result
					result.innerHTML = ""
					cglong[mapid].value = r.features[0].geometry.coordinates[0].toString().substring(0,8);
                    cglong[mapid].innerHTML = cglong[mapid].value;
					cglat[mapid].value = r.features[0].geometry.coordinates[1].toString().substring(0,8);
                    cglat[mapid].innerHTML = cglat[mapid].value;
					cgaddress[mapid].value = r.features[0].properties.label;
					cglibs[mapid].style.display = 'inline-flex';
					$res.value = r.features[0].properties.label + '|' + cglong[mapid].value + '|' + cglat[mapid].value;
                    cgmap[mapid].setView([cglat[mapid].value,cglong[mapid].value], 15);
                    if (cgmarker[mapid]) cgmarker[mapid].remove();
                    cgmarker[mapid] = L.marker([cglat[mapid].value, cglong[mapid].value]).addTo(cgmap[mapid]);
                    cgmarker[mapid].bindPopup(cgaddress[mapid].value);
                    if (popup[mapid] == 'true') {
                        createPopup(mapid,cglat[mapid].value, cglong[mapid].value);
                    }
					cgonelist[mapid].style.display = "none"
				}
				if (r.features.length > 1) { // multiple results : display listbox
					result.innerHTML = "";
					amap.querySelectorAll('#cgaddress_select option').forEach(option => option.remove()); // cleanup
					empty = document.createElement("option");
					empty.text = " "+r.features.length+" proposition(s)---";
					empty.value = "";
					arr = new Array();
					arr.push(empty);
					r.features.forEach(onezip => { 
						opt = document.createElement("option");
						opt.text = onezip.properties.label;
						opt.value = onezip.geometry.coordinates[0] + ',' + onezip.geometry.coordinates[1];
						arr.push(opt);
					})	
					for(i = 0; i < arr.length; i++) { 
						cgonelist[mapid].add(arr[i]);
					}
					cgonelist[mapid].options[0].selected = true;	// set 1st option selected (empty)
					cgonelist[mapid].style.display = "inline-flex";
				}
			} else {
					console.log("Erreur xhr");
			}
		}
		xhr.send(null);
	},callDelay);
}
function createPopup(mapid,alat,along) {
    max = cgmap[mapid].getSize().x - 20;
    popuptext = cgaddress[mapid].value;
    if (iti[mapid] == 'true') { // affiche un lien Venir ici
        popuptext += '<br><a href="https://www.openstreetmap.org/directions?route=%3B'+alat+'%2C'+along+'#map=14/'+alat+'/'+along+'" target="_blank" rel="noopener">Venir ici</a>'; 
    }
    cgmarker[mapid].bindPopup(popuptext,{maxWidth: max,keepInView:true});
}
function cleardisplay(mapid) {
	cglong[mapid].value = ""; cglong[mapid].innerHTML = "";
	cglat[mapid].value = "";cglat[mapid].innerHTML ="";
	cglibs[mapid].style.display = 'none';
	$val = cgaddressfid[mapid].value;
	$res = document.querySelector("#"+$val);
    $res.value = "";
}