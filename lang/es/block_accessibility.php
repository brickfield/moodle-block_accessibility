<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Spanish translation                                                 (1)
 *
 * @author  Enrique Robredo                                            (2)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (3)
 */
defined('MOODLE_INTERNAL') || die();

$string['autolaunch'] = '(¿siempre?)';
$string['blockname'] = 'Accesibilidad';
$string['char'] = 'A';
$string['clearedoldcache'] = 'Eliminados de la caché {$a} archivos antiguos';
$string['dectext'] = 'Disminuir Texto';
$string['inctext'] = 'Aumentar Texto';
$string['invalidop'] = 'Esta operación será invalidada';
$string['jsnocolour'] = 'Se ha producido un error cambiando el esquema de colores';
$string['jsnocolourreset'] = 'Se ha producido un error reiniciano el esquema de colores';
$string['jsnosave'] = 'Se ha producido un error cambiando la configuración';
$string['jsnosize'] = 'Se ha producido un error cambiando el tamaño';
$string['jsnosizereset'] = 'Se ha producido un error reiniciando el tamaño del texto';
$string['launchtoolbar'] = 'Lanzar ATbar';
$string['pluginname'] = 'Accesibilidad';
$string['pluginnameplural'] = 'Accesibilidad';
$string['resettext'] = 'Recuperar tamaño de la fuente (Se pierde la configuración guardada)';
$string['reset'] = 'Configuración Reiniciada';
$string['save'] = 'Guardar configuración';
$string['saved'] = 'Configuración guardada';
$string['col1text'] = 'Volver al esquema de colores original (Se pierde la configuración guardada)';
$string['col2text'] = 'Bajo Contraste 1';
$string['col3text'] = 'Bajo Contraste 2';
$string['col4text'] = 'Alto Contraste';

/*	Configuration form - please help us translate it on GitHub 
	------------------------------------------------------------
*/
$string['config_autosave'] = 'Auto save'; // label
$string['config_autosave_checkbox'] = 'Save user settings automatically (button "save" will disappear)'; // checkbox label
$string['config_autosave_help'] = 'Font size and colour schemes settings are automatically saved to the session as long as user is logged in to the system. However, session settings will be cleared once the user log off. The user can keep chosen settings throughout the sessions using the "save" button in the block. Enabling Auto-save option will remove the "save" button and automatically save settings for the user. This might lead to slightly higher impact to the system performance, which is why this option is disabled by default.'; // help block
$string['config_showATbar'] = 'ATbar';
$string['config_showATbar_checkbox'] = 'Allow ATbar appearance within Accessibility block';
$string['config_showATbar_help'] = 'Accessibility block also integrates ATbar from Southampton University ECS <a href="http://www.atbar.org">http://www.atbar.org</a>.';
$string['config_fg'] = 'Text colour (not required)';
$string['config_fg_help'] = 'Define colour scheme foreground colour here. Keep in mind that the colour will be applied uniformly to all user interface elements. It is not always desirable to have the same colour in each user interface element. <strong>It is recommended to leave foreground colour field empty</strong> so that elements can keep its default colours. Try to change background colour only.';
$string['config_bg'] = 'Background colour';
$string['config_bg_help'] = 'Define colour scheme background colour here. Keep in mind that the background colour will be applied uniformly to all user interface elements.';
$string['color_input_error'] = 'Please enter a color in a format as such: #FF0050';

$string['accessibility:addinstance'] = 'Add a new Accessibility block';
$string['accessibility:myaddinstance'] = 'Add a new Accessibility block to My home';
$string['jsnotloggedin'] = 'Error! Please check if you are logged-in to the system or contact your administrator';