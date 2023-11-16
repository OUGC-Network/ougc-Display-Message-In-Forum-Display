<?php

/***************************************************************************
 *
 *    OUGC Display Message In Forum Display plugin (/inc/plugins/ougc/DisplayMessageInForumDisplay/Admin.php)
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

namespace ougc\DisplayMessageInForumDisplay\Admin;

function pluginInfo(): array
{
    global $lang;

    \ougc\DisplayMessageInForumDisplay\Core\loadLanguage();

    return [
        'name' => 'OUGC Display Message In Forum Display',
        'description' => $lang->ougcDisplayMessageInForumDisplay_desc,
        'website' => 'https://ougc.network',
        'author' => 'Omar G.',
        'authorsite' => 'https://ougc.network',
        'version' => '1.8.36',
        'versioncode' => 1836,
        'compatibility' => '18*',
        'codename' => 'ougc_dmifd',
        'pl' => [
            'version' => 13,
            'url' => 'https://community.mybb.com/mods.php?action=view&pid=573'
        ],
    ];
}

function pluginActivate(): bool
{
    global $PL, $cache, $lang;

    \ougc\DisplayMessageInForumDisplay\Core\loadLanguage();

    $pluginInfo = \ougc_dmifd_info();

    \ougc\DisplayMessageInForumDisplay\Core\loadPluginLibrary();

    // Add settings group
    $settingsContents = \file_get_contents(OUGC_DMIFD_ROOT . '/settings.json');

    $settingsData = \json_decode($settingsContents, true);

    foreach ($settingsData as $settingKey => &$settingData) {
        if (empty($lang->{"setting_DisplayMessageInForumDisplay_{$settingKey}"})) {
            continue;
        }

        if ($settingData['optionscode'] == 'select') {
            foreach ($settingData['options'] as $optionKey) {
                $settingData['optionscode'] .= "\n{$optionKey}={$lang->{"setting_DisplayMessageInForumDisplay_{$settingKey}_{$optionKey}"}}";
            }
        }

        $settingData['title'] = $lang->{"setting_DisplayMessageInForumDisplay_{$settingKey}"};
        $settingData['description'] = $lang->{"setting_DisplayMessageInForumDisplay_{$settingKey}_desc"};
    }

    $PL->settings(
        'ougc_dmifd',
        $lang->setting_group_DisplayMessageInForumDisplay_,
        $lang->setting_group_DisplayMessageInForumDisplay_desc,
        $settingsData
    );

    // Insert/update version into cache
    $plugins = (array)$cache->read('ougc_plugins');

    if (!isset($plugins['ougc_dmifd'])) {
        $plugins['ougc_dmifd'] = $pluginInfo['versioncode'];
    }

    /*~*~* RUN UPDATES START *~*~*/

    /*~*~* RUN UPDATES END *~*~*/

    $plugins['ougc_dmifd'] = $pluginInfo['versioncode'];

    $cache->update('ougc_plugins', $plugins);

    return true;
}

function pluginIsInstalled(): bool
{
    global $cache;

    $plugins = (array)$cache->read('ougc_plugins');

    return isset($plugins['ougc_dmifd']);
}

function pluginUninstall(): bool
{
    global $PL, $cache;

    \ougc\DisplayMessageInForumDisplay\Core\loadPluginLibrary();

    $PL->settings_delete('ougc_dmifd');

    // Delete version from cache
    $plugins = (array)$cache->read('ougc_plugins');

    if (isset($plugins['ougc_dmifd'])) {
        unset($plugins['ougc_dmifd']);
    }

    if (!empty($plugins)) {
        $cache->update('ougc_plugins', $plugins);
    } else {
        $PL->cache_delete('ougc_plugins');
    }

    return true;
}