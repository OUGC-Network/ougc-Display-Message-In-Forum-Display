<?php

/***************************************************************************
 *
 *	OUGC Display Message In Forum Display plugin (/inc/plugins/ougc_dmifd.php)
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

// Die if IN_MYBB is not defined, for security reasons.
defined('IN_MYBB') or die('Direct initialization of this file is not allowed.');

// Run/Add Hooks
if(defined('IN_ADMINCP'))
{
	$plugins->add_hook('admin_config_settings_start', 'ougc_dmifd_load_language');
	$plugins->add_hook('admin_style_templates_set', 'ougc_dmifd_load_language');
}
else
{
	$plugins->add_hook('forumdisplay_get_threads', 'ougc_dmifd_forumdisplay_get_threads');
}

// PLUGINLIBRARY
defined('PLUGINLIBRARY') or define('PLUGINLIBRARY', MYBB_ROOT.'inc/plugins/pluginlibrary.php');

// Plugin API
function ougc_dmifd_info()
{
	global $lang;

	ougc_dmifd_load_language();

	return array(
		'name'			=> 'OUGC Display Message In Forum Display',
		'description'	=> $lang->ougc_dmifd_desc,
		'website'		=> 'https://ougc.network',
		'author'		=> 'Omar G.',
		'authorsite'	=> 'https://ougc.network',
		'version'		=> '1.8.20',
		'versioncode'	=> 1820,
		'compatibility'	=> '18*',
		'codename'		=> 'ougc_dmifd',
		'pl'			=> array(
			'version'	=> 13,
			'url'		=> 'https://community.mybb.com/mods.php?action=view&pid=573'
		)
	);
}

// Plugin API:_activate() routine
function ougc_dmifd_activate()
{
	global $cache, $lang, $PL;

	ougc_dmifd_load_pluginlibrary();

	$PL->settings('ougc_dmifd', $lang->setting_group_ougc_dmifd, $lang->setting_group_ougc_dmifd_desc, array(
		'forums'	=> array(
		   'title'			=> $lang->setting_ougc_dmifd_forums,
		   'description'	=> $lang->setting_ougc_dmifd_forums_desc,
		   'optionscode'	=> 'forumselect',
		   'value'			=> -1
		)
	));

	// Insert/update version into cache
	$plugins = $cache->read('ougc_plugins');
	if(!$plugins)
	{
		$plugins = array();
	}

	$info = ougc_dmifd_info();

	if(!isset($plugins['ougc_dmifd']))
	{
		$plugins['ougc_dmifd'] = $info['versioncode'];
	}

	/*~*~* RUN UPDATES START *~*~*/

	/*~*~* RUN UPDATES END *~*~*/

	$plugins['ougc_dmifd'] = $info['versioncode'];
	$cache->update('ougc_plugins', $plugins);
}

// Plugin API:_is_installed() routine
function ougc_dmifd_is_installed()
{
	global $cache;

	$plugins = $cache->read('ougc_plugins');
	if(!$plugins)
	{
		$plugins = array();
	}

	return isset($plugins['ougc_dmifd']);
}

// Plugin API:_uninstall() routine
function ougc_dmifd_uninstall()
{
	global $PL, $cache;

	ougc_dmifd_load_pluginlibrary();

	// Delete settings
	$PL->settings_delete('ougc_feedback');

	// Delete version from cache
	$plugins = (array)$cache->read('ougc_plugins');

	if(isset($plugins['ougc_dmifd']))
	{
		unset($plugins['ougc_dmifd']);
	}

	if(!empty($plugins))
	{
		$cache->update('ougc_plugins', $plugins);
	}
	else
	{
		$PL->cache_delete('ougc_plugins');
	}

	$cache->update_forums();
	$cache->update_usergroups();
}

// PluginLibrary requirement check
function ougc_dmifd_load_pluginlibrary()
{
	global $lang;

	$info = ougc_dmifd_info();

	ougc_dmifd_load_language();

	if(!file_exists(PLUGINLIBRARY))
	{
		flash_message($lang->sprintf($lang->ougc_dmifd_pluginlibrary_required, $info['pl']['ulr'], $info['pl']['version']), 'error');
		admin_redirect('index.php?module=config-plugins');
	}

	global $PL;

	$PL or require_once PLUGINLIBRARY;

	if($PL->version < $info['pl']['version'])
	{
		global $lang;

		flash_message($lang->sprintf($lang->ougc_dmifd_pluginlibrary_outdated, $PL->version, $info['pl']['version'], $info['pl']['ulr']), 'error');
		admin_redirect('index.php?module=config-plugins');
	}
}

// Load language strings
function ougc_dmifd_load_language()
{
	global $lang;

	isset($lang->ougc_dmifd) or $lang->load('ougc_dmifd', false, true);

	if(!isset($lang->ougc_dmifd))
	{
		// Plugin API
		$lang->ougc_dmifd = 'OUGC Display Message In Forum Display';
		$lang->ougc_dmifd_desc = 'Display the thread message inside the forum display thread listing.';

		// Settings
		$lang->setting_group_ougc_dmifd = 'OUGC Display Message In Forum Display';
		$lang->setting_group_ougc_dmifd_desc = 'Display the thread message inside the forum display thread listing.';
		$lang->setting_ougc_dmifd_forums = 'Allowed Forums';
		$lang->setting_ougc_dmifd_forums_desc = 'Select the forums where this feature should be run in.<br />You need to manually place <i>{$thread[\'message\']}</i> inside your <i>forumdisplay_thread</i> template.';

		// PluginLibrary
		$lang->ougc_dmifd_pluginlibrary_required = 'This plugin requires <a href="{1}">PluginLibrary</a> version {2} or later to be uploaded to your forum.';
		$lang->ougc_dmifd_pluginlibrary_outdated = 'This plugin requires PluginLibrary version {2} or later, whereas your current version is {1}. Please do update <a href="{3}">PluginLibrary</a>.';
	}
}

function ougc_dmifd_forumdisplay_get_threads()
{
	global $foruminfo, $db, $plugins, $settings;

	$foruminfo['fid'] = (int)$foruminfo['fid'];

	if(!$settings['ougc_dmifd_forums'] || ((int)$settings['ougc_dmifd_forums'] !== -1 && !in_array($foruminfo['fid'], explode(',', $settings['ougc_dmifd_forums']))))
	{
		return;
	}

	control_object($db, '
		function query($string, $hide_errors=0, $write_query=0)
		{
			static $done = false;
			if(!$done && strpos($string, \'t.username AS threadusername, u.username\') !== false)
			{
				$done = true;
				$string = strtr($string, array(
					\', u.username\' => \', u.username, p.message, p.smilieoff\',
					\'users u ON (u.uid = t.uid)\' => \'users u ON (u.uid = t.uid) LEFT JOIN '.TABLE_PREFIX.'posts p ON (p.pid = t.firstpost)\'
				));
			}
			return parent::query($string, $hide_errors, $write_query);
		}
	');

	$plugins->add_hook('forumdisplay_thread', 'ougc_dmifd_forumdisplay_thread');
}

function ougc_dmifd_forumdisplay_thread()
{
	global $thread, $foruminfo, $parser, $mybb;

	$parser_options = array(
		'allow_html'	=> $foruminfo['allowhtml'],
		'allow_mycode'	=> $foruminfo['allowmycode'],
		'allow_smilies'	=> $foruminfo['allowsmilies'],
		'allow_imgcode'	=> $foruminfo['allowimgcode'],
		'allow_videocode'	=> $foruminfo['allowvideocode'],
		'filter_badwords'	=> 1
	 );

	if($thread['threadusername'])
	{
		$parser_options['me_username'] = $thread['threadusername'];
	}
	else
	{
		$parser_options['me_username'] = $thread['username'];
	}

	if(isset($thread['smilieoff']) && $thread['smilieoff'] == 1)
	{
		$parser_options['allow_smilies'] = 0;
	}

	if($mybb->user['showimages'] != 1 && $mybb->user['uid'] != 0 || $mybb->settings['guestimages'] != 1 && $mybb->user['uid'] == 0)
	{
		$parser_options['allow_imgcode'] = 0;
	}

	if($mybb->user['showvideos'] != 1 && $mybb->user['uid'] != 0 || $mybb->settings['guestvideos'] != 1 && $mybb->user['uid'] == 0)
	{
		$parser_options['allow_videocode'] = 0;
	}

	$thread['message'] = $parser->parse_message($thread['message'], $parser_options);
}

// control_object by Zinga Burga from MyBBHacks ( mybbhacks.zingaburga.com ), 1.62
if(!function_exists('control_object'))
{
	function control_object(&$obj, $code)
	{
		static $cnt = 0;
		$newname = '_objcont_'.(++$cnt);
		$objserial = serialize($obj);
		$classname = get_class($obj);
		$checkstr = 'O:'.strlen($classname).':"'.$classname.'":';
		$checkstr_len = strlen($checkstr);
		if(substr($objserial, 0, $checkstr_len) == $checkstr)
		{
			$vars = array();
			// grab resources/object etc, stripping scope info from keys
			foreach((array)$obj as $k => $v)
			{
				if($p = strrpos($k, "\0"))
				{
					$k = substr($k, $p+1);
				}
				$vars[$k] = $v;
			}
			if(!empty($vars))
			{
				$code .= '
					function ___setvars(&$a) {
						foreach($a as $k => &$v)
							$this->$k = $v;
					}
				';
			}
			eval('class '.$newname.' extends '.$classname.' {'.$code.'}');
			$obj = unserialize('O:'.strlen($newname).':"'.$newname.'":'.substr($objserial, $checkstr_len));
			if(!empty($vars))
			{
				$obj->___setvars($vars);
			}
		}
		// else not a valid object or PHP serialize has changed
	}
}