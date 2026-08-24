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
 * Hook callbacks for tool_imageoptimize.
 *
 * @package   tool_imageoptimize
 * @copyright 2026 eduvidual
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_imageoptimize;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/admin/tool/imageoptimize/tool_imageoptimize.php');

class hook_callbacks {

    public static function after_file_created(\core_files\hook\after_file_created $hook): void {
        $filerecord = $hook->filerecord;

        $imageoptimizehelper = tool_image_optimize_helper::get_instance();
        $imageoptimizehelper->get_enabled_mimetypes();

        if (!in_array($filerecord->mimetype, $imageoptimizehelper->enabledmimetypes)) {
            return;
        }

        if (!get_config('tool_imageoptimize', 'enablebackgroundoptimizing')) {
            $obj = new \tool_image_optimize($filerecord);
            $obj->handle('create');
            return;
        }

        $imageoptimizehelper->insert_fileinfo_depending_on_contenthash($filerecord);
    }
}
