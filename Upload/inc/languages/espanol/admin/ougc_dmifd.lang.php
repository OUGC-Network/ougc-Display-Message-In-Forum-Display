<?php

/***************************************************************************
 *
 *	OUGC Display Message In Forum Display plugin (/inc/languages/espanol/admin/ougc_dmifd.lang.php)
 *	Author: Omar Gonzalez
 *	Copyright: © 2019 2020 Omar Gonzalez
 *
 *	Website: https://ougc.network
 *
 *	Display the thread message inside the forum display thread listing.
 *
 ***************************************************************************
 
****************************************************************************
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
****************************************************************************/

// Plugin API
$l['ougc_dmifd'] = 'OUGC Display Message In Forum Display';
$l['ougc_dmifd_desc'] = 'Muestra el mensaje del tema dentro del listado de temas en los foros.';

// Settings
$l['setting_group_ougc_dmifd'] = $l['ougc_dmifd'];
$l['setting_group_ougc_dmifd_desc'] = $l['ougc_dmifd_desc'];
$l['setting_ougc_dmifd_forums'] = 'Foros Permitidos';
$l['setting_ougc_dmifd_forums_desc'] = 'Selecciona los foros donde se mostrara el mensaje de los temas.<br />
Necesitas colocar manualmente <i>{$thread[\'message\']}</i> dentro del template <i>forumdisplay_thread</i>.';

// PluginLibrary
$l['ougc_dmifd_pluginlibrary_required'] = 'Este plugin requiere <a href="{1}">PluginLibrary</a> version {2} para funcionar.';
$l['ougc_dmifd_pluginlibrary_outdated'] = $l['ougc_dmifd_pluginlibrary_required'];