<?php

/***************************************************************************
 *
 *    OUGC Display Message In Forum Display plugin (/inc/plugins/ougc/DisplayMessageInForumDisplay/Hooks/Forums.php)
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

namespace ougc\DisplayMessageInForumDisplay\Hooks\Forum;

function forumdisplay_get_threads(): bool
{
    global $fid;

    $forumID = (int)$fid;

    $allowedForums = \ougc\DisplayMessageInForumDisplay\Core\getSetting('forums');

    if (!is_member($allowedForums, ['usergroup' => $forumID, 'additionalgroups' => ''])) {
        return false;
    }

    global $db;
    global $ougcDisplayMessageInForumDisplay;

    if ($ougcDisplayMessageInForumDisplay === null) {
        control_object(
            $db,
            'function query($string, $hide_errors = 0, $write_query = 0)
{
    static $done = false;

    if (!$done && !$write_query && strpos($string, "t.username AS threadusername, u.username") !== false) {
        $done = true;

        $replaces = [];

        if (my_strpos($string, "p.message, p.smilieoff") === false) {
            $replaces["SELECT t.*,"] = "SELECT t.*, p.message, p.smilieoff,";
        }

        if (my_strpos($string, "posts p ON") === false) {
            $replaces["WHERE t.fid="] = "LEFT JOIN ' . $db->table_prefix . 'posts p ON (p.pid = t.firstpost) WHERE t.fid=";
        }

        if ($replaces) {
            $string = strtr($string, $replaces);
        }
    }

    return parent::query($string, $hide_errors, $write_query);
}'
        );
    }

    $ougcDisplayMessageInForumDisplay = $forumID;

    return true;
}

function forumdisplay_thread(): bool
{
    global $ougcDisplayMessageInForumDisplay;

    if ($ougcDisplayMessageInForumDisplay === null) {
        return false;
    }

    global $thread, $foruminfo, $parser, $mybb, $threadcache, $db, $attachcache;

    if ($attachcache === null && $mybb->settings['enableattachments']) {
        $attachcache = [];

        $postIDs = [];

        foreach ($threadcache as $threadData) {
            if ($threadData['attachmentcount'] > 0 || is_moderator($ougcDisplayMessageInForumDisplay, 'caneditposts')) {
                $postIDs[] = (int)$threadData['firstpost'];
            }
        }

        if ($postIDs) {
            $postIDs = implode("','", $postIDs);

            $dbQuery = $db->simple_select('attachments', '*', "pid IN ('{$postIDs}')");

            while ($attachmentData = $db->fetch_array($dbQuery)) {
                $attachcache[$attachmentData['pid']][$attachmentData['aid']] = $attachmentData;
            }
        }
    }

    $parserOptions = [
        'allow_html' => $foruminfo['allowhtml'],
        'allow_mycode' => $foruminfo['allowmycode'],
        'allow_smilies' => $foruminfo['allowsmilies'],
        'allow_imgcode' => $foruminfo['allowimgcode'],
        'allow_videocode' => $foruminfo['allowvideocode'],
        'filter_badwords' => 1
    ];

    if ($thread['threadusername']) {
        $parserOptions['me_username'] = $thread['threadusername'];
    } else {
        $parserOptions['me_username'] = $thread['username'];
    }

    if (isset($thread['smilieoff']) && $thread['smilieoff'] == 1) {
        $parserOptions['allow_smilies'] = 0;
    }

    if (!$mybb->user['showimages'] && $mybb->user['uid'] || !$mybb->settings['guestimages'] && !$mybb->user['uid']) {
        $parserOptions['allow_imgcode'] = 0;
    }

    if (!$mybb->user['showvideos'] && $mybb->user['uid'] || !$mybb->settings['guestvideos'] && !$mybb->user['uid']) {
        $parserOptions['allow_videocode'] = 0;
    }

    $thread['message'] = $parser->parse_message($thread['message'], $parserOptions);

    if ($mybb->settings['enableattachments'] && isset($attachcache[$thread['firstpost']])) {
        require_once \MYBB_ROOT . 'inc/functions_post.php';

        get_post_attachments($thread['firstpost'], $thread);
    }

    return true;
}