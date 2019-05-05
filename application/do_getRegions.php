<?php /*
	Copyright 2019 Cédric Levieux, Parti Pirate

	This file is part of BulletinDeVote.

    BulletinDeVote is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    BulletinDeVote is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with BulletinDeVote.  If age, see <http://www.gnu.org/licenses/>.
*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("config/database.php");
require_once("engine/bo/RegionBo.php");

$connection = openConnection();

$regionBo = RegionBo::newInstance($connection, $config);
$allRegions = $regionBo->getByFilters(array());

?>

var regions = <?=json_encode($allRegions)?>;

$(function() {
    for(var index = 0; index < regions.length; ++index) {
        $("#region-select").append($("<option value=\""+regions[index].reg_name+"\">"+regions[index].reg_name+"</option>"));
    }
});