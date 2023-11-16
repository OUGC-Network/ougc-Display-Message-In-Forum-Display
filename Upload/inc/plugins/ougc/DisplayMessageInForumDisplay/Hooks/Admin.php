<?php

/***************************************************************************
 *
 *    OUGC Display Message In Forum Display plugin (/inc/plugins/ougc/DisplayMessageInForumDisplay/Hooks/Admin.php)
 *    Author: Omar Gonzalez
 *    Copyright: © 2019 Omar Gonzalez
 *
 *    Website: https://ougc.network
 *
 *    Display the thread message inside the forum display thread listing.
 *
 ***************************************************************************
 ****************************************************************************
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 ****************************************************************************/

declare(strict_types=1);

namespace ougc\DisplayMessageInForumDisplay\Hooks\Admin;

use function ougc\DisplayMessageInForumDisplay\Core\loadLanguage;

function admin_config_plugins_deactivate(): bool
{
    global $mybb, $page;

    if (
        $mybb->get_input('action') != 'deactivate' ||
        $mybb->get_input('plugin') != 'ougc_dmifd' ||
        !$mybb->get_input('uninstall', \MyBB::INPUT_INT)
    ) {
        return false;
    }

    if ($mybb->request_method != 'post') {
        $page->output_confirm_action(
            'index.php?module=config-plugins&amp;action=deactivate&amp;uninstall=1&amp;plugin=ougc_dmifd'
        );
    }

    if ($mybb->get_input('no')) {
        \admin_redirect('index.php?module=config-plugins');
    }

    return true;
}

function admin_config_settings_start()
{
    loadLanguage();
}


function admin_style_templates_set()
{
    loadLanguage();
}