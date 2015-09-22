<?php

/*  Poweradmin, a friendly web-based admin tool for PowerDNS.
 *  See <http://www.poweradmin.org> for more details.
 *
 *  Copyright 2007-2010  Rejo Zenger <rejo@zenger.nl>
 *  Copyright 2010-2014  Poweradmin Development Team
 *      <http://www.poweradmin.org/credits.html>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Script that handles user password changes
 *
 * @package     Poweradmin
 * @copyright   2007-2010 Rejo Zenger <rejo@zenger.nl>
 * @copyright   2010-2014 Poweradmin Development Team
 * @license     http://opensource.org/licenses/GPL-3.0 GPL
 */
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

if (isset($_POST['submit']) && $_POST['submit']) {
    do_hook('change_user_pass' , $_POST );
}

echo "    <h1 class=\"page-header\">" . _('Change password') . "</h1>\n";
echo "     <div class=\"panel-body\">\n";
echo "    <form class=\"form-horizontal\" method=\"post\" action=\"change_password.php\">\n";
echo "       <div class=\"form-group\">\n";
echo "        <label for=\"currentpass\" class=\"col-sm-2 control-label\">" . _('Current password') . "</label>\n";
echo "        <div class=\"col-sm-10\">\n";
echo "         <input type=\"password\" class=\"form-control\" name=\"currentpass\" value=\"\">\n";
echo "        </div>\n";
echo "       </div>\n";
echo "       <div class=\"form-group\">\n";
echo "        <label for=\"newpass\" class=\"col-sm-2 control-label\">" . _('New password') . "</label>\n";
echo "        <div class=\"col-sm-10\">\n";
echo "         <input type=\"password\" class=\"form-control\" name=\"newpass\" value=\"\">\n";
echo "        </div>\n";
echo "       </div>\n";
echo "       <div class=\"form-group\">\n";
echo "        <label for=\"newpass2\" class=\"col-sm-2 control-label\">" . _('New password') . "</label>\n";
echo "        <div class=\"col-sm-10\">\n";
echo "         <input type=\"password\" class=\"form-control\" name=\"newpass2\" value=\"\">\n";
echo "        </div>\n";
echo "       </div>\n";
echo "       <div class=\"form-group\">\n";
echo "        <div class=\"col-sm-offset-2 col-sm-10\">\n";
echo "         <input type=\"submit\" class=\"btn btn-default\" name=\"submit\" value=\"" . _('Change password') . "\">\n";
echo "        </div>\n";
echo "       </div>\n";
echo "    </form>\n";

include_once("inc/footer.inc.php");
