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

namespace tool_imageoptimize\local\hooks;

/**
 * Hook callbacks for tool_imageoptimize
 *
 * @package    tool_imageoptimize
 * @copyright  2025 ISB Bayern
 * @author     Stefan Hanauska <stefan.hanauska@csg-in.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class after_file_created {
    /**
     * Handle 'after_file_created' hook
     *
     * @param \core_files\hook\after_file_created $hook
     */
    public static function callback(\core_files\hook\after_file_created $hook): void {
        global $CFG;
        if (during_initial_install() || isset($CFG->upgraderunning) || !get_config('tool_imageoptimize', 'version')) {
            // Do nothing during installation or upgrade.
            return;
        }

        $filerecord = $hook->filerecord;

        $imageoptimizehelper = \tool_imageoptimize\tool_image_optimize_helper::get_instance();
        $imageoptimizehelper->get_enabled_mimetypes();

        if (!in_array($filerecord->mimetype, $imageoptimizehelper->enabledmimetypes)) {
            return;
        }

        if (empty(get_config('tool_imageoptimize', 'enablebackgroundoptimizing'))) {
            $obj = new \tool_image_optimize($filerecord);
            $obj->handle('create');
        }

        $imageoptimizehelper->insert_fileinfo_depending_on_contenthash($filerecord);
    }
}
