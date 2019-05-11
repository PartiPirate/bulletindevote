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
require_once("engine/bo/CityBo.php");

$data = array();
$data["cities"] = array();

$connection = openConnection();

$ratio = intval($_REQUEST["proportion"]);

$cityBo = CityBo::newInstance($connection, $config);

$filters = array();

if (isset($_REQUEST["region"])) {
	$filters["cit_region"] = $_REQUEST["region"];
}

if (isset($_REQUEST["department"])) {
	$filters["cit_department"] = $_REQUEST["department"];
}

if (isset($_REQUEST["city"])) {
	if (intval($_REQUEST["city"])) {
		$filters["cit_like_zip_code"] = $_REQUEST["city"];
	}
	else {
		$filters["cit_like_name"] = $_REQUEST["city"];
	}
}

$cities = $cityBo->getByFilters($filters);

$numberOfBallots = 0;
$electors = 0;
$population = 0;

foreach($cities as &$city) {
	$numberOfElectors = intval($city["cit_population"] * 0.75 * 1000);

	$localBallots = ($numberOfElectors * $ratio / 100);
	$localBallots = ceil($localBallots / 100) * 100;

	$city["cit_numberOfBallots"] = $localBallots;
	$city["cit_numberOfElectors"] = $numberOfElectors;

	$numberOfBallots += $localBallots;
	$electors += $numberOfElectors;
	$population += $city["cit_population"];
	$data["cities"][] = $city;
}

if (isset($_REQUEST["requestId"])) {
	$data["requestId"] = $_REQUEST["requestId"];
}

$data["numberOfBallots"] = $numberOfBallots;
$data["numberOfCities"] = count($data["cities"]);
$data["population"] = $population;
$data["electors"] = $electors;
$data["proportion"] = $ratio;

echo json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);