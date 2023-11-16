<?php

/***************************************************************************
 *
 *    OUGC Display Message In Forum Display plugin (/inc/plugins/ougc/DisplayMessageInForumDisplay/Core.php)
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

namespace ougc\DisplayMessageInForumDisplay\Core;

function loadLanguage(): bool
{
    global $lang;

    if (!isset($lang->ougc_dmifd)) {
        $lang->load('ougc_dmifd');
    }

    return true;
}

function pluginLibraryRequirements(): object
{
    return (object)\ougc\DisplayMessageInForumDisplay\Admin\pluginInfo()['pl'];
}

function loadPluginLibrary(): bool
{
    global $PL, $lang;

    loadLanguage();

    $fileExists = file_exists(\PLUGINLIBRARY);

    if ($fileExists && !($PL instanceof \PluginLibrary)) {
        require_once \PLUGINLIBRARY;
    }

    if (!$fileExists || $PL->version < pluginLibraryRequirements()->version) {
        \flash_message(
            $lang->sprintf(
                $lang->ougcDisplayMessageInForumDisplay_pluginLibrary,
                pluginLibraryRequirements()->url,
                pluginLibraryRequirements()->version
            ),
            'error'
        );

        \admin_redirect('index.php?module=config-plugins');
    }

    return true;
}

function addHooks(string $namespace)
{
    global $plugins;

    $namespaceLowercase = strtolower($namespace);
    $definedUserFunctions = get_defined_functions()['user'];

    foreach ($definedUserFunctions as $callable) {
        $namespaceWithPrefixLength = strlen($namespaceLowercase) + 1;

        if (substr($callable, 0, $namespaceWithPrefixLength) == $namespaceLowercase . '\\') {
            $hookName = substr_replace($callable, '', 0, $namespaceWithPrefixLength);

            $priority = substr($callable, -2);

            if (is_numeric(substr($hookName, -2))) {
                $hookName = substr($hookName, 0, -2);
            } else {
                $priority = 10;
            }

            $plugins->add_hook($hookName, $callable, $priority);
        }
    }
}

function getSetting(string $settingKey = '')
{
    global $mybb;

    return isset(SETTINGS[$settingKey]) ? SETTINGS[$settingKey] : (string)$mybb->settings['ougc_dmifd_' . $settingKey];
}