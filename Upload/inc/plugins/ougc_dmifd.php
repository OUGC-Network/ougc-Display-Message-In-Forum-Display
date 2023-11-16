<?php

/***************************************************************************
 *
 *    OUGC Display Message In Forum Display plugin (/inc/plugins/ougc_dmifd.php)
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

// Die if IN_MYBB is not defined, for security reasons.
if (!defined('IN_MYBB')) {
    die('This file cannot be accessed directly.');
}

const OUGC_DMIFD_ROOT = \MYBB_ROOT . 'inc/plugins/ougc/DisplayMessageInForumDisplay';

// Plugin Settings
define('ougc\DisplayMessageInForumDisplay\Core\SETTINGS', [
    //'forums' => '-1'
]);

// PLUGINLIBRARY
if (!defined('PLUGINLIBRARY')) {
    define('PLUGINLIBRARY', \MYBB_ROOT . 'inc/plugins/pluginlibrary.php');
}

require_once OUGC_DMIFD_ROOT . '/Core.php';

if (defined('IN_ADMINCP')) {
    require_once OUGC_DMIFD_ROOT . '/Admin.php';
    require_once OUGC_DMIFD_ROOT . '/Hooks/Admin.php';

    \ougc\DisplayMessageInForumDisplay\Core\addHooks('ougc\DisplayMessageInForumDisplay\Hooks\Admin');
}

require_once OUGC_DMIFD_ROOT . '/Hooks/Forum.php';

\ougc\DisplayMessageInForumDisplay\Core\addHooks('ougc\DisplayMessageInForumDisplay\Hooks\Forum');

require_once OUGC_DMIFD_ROOT . '/Core.php';

// Plugin API
function ougc_dmifd_info(): array
{
    return \ougc\DisplayMessageInForumDisplay\Admin\pluginInfo();
}

function ougc_dmifd_activate(): bool
{
    return \ougc\DisplayMessageInForumDisplay\Admin\pluginActivate();
}

// _is_installed() routine
function ougc_dmifd_is_installed(): bool
{
    return \ougc\DisplayMessageInForumDisplay\Admin\pluginIsInstalled();
}

// _uninstall() routine
function ougc_dmifd_uninstall(): bool
{
    return \ougc\DisplayMessageInForumDisplay\Admin\pluginUninstall();
}

// control_object by Zinga Burga from MyBBHacks ( mybbhacks.zingaburga.com )
if (!function_exists('control_object')) {
    function control_object(&$obj, $code)
    {
        static $cnt = 0;
        $newname = '_objcont_' . (++$cnt);
        $objserial = serialize($obj);
        $classname = get_class($obj);
        $checkstr = 'O:' . strlen($classname) . ':"' . $classname . '":';
        $checkstr_len = strlen($checkstr);
        if (substr($objserial, 0, $checkstr_len) == $checkstr) {
            $vars = array();
            // grab resources/object etc, stripping scope info from keys
            foreach ((array)$obj as $k => $v) {
                if ($p = strrpos($k, "\0")) {
                    $k = substr($k, $p + 1);
                }
                $vars[$k] = $v;
            }
            if (!empty($vars)) {
                $code .= '
					function ___setvars(&$a) {
						foreach($a as $k => &$v)
							$this->$k = $v;
					}
				';
            }
            eval('class ' . $newname . ' extends ' . $classname . ' {' . $code . '}');
            $obj = unserialize('O:' . strlen($newname) . ':"' . $newname . '":' . substr($objserial, $checkstr_len));
            if (!empty($vars)) {
                $obj->___setvars($vars);
            }
        }
        // else not a valid object or PHP serialize has changed
    }
}