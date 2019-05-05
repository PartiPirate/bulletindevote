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
require_once("engine/bo/BallotBo.php");

$connection = openConnection();

$ballotBo = BallotBo::newInstance($connection, $config);
$ballots = $ballotBo->getByFilters(array("bal_confirmed" => 1));

$data = array();
$data["type"] = "FeatureCollection";
$data["features"] = array();

foreach($ballots as $ballot) {
	$ballotInformation = array("type" => "Feature", "id" => $ballot["cit_insee"], "properties" => array("name" => $ballot["cit_name"], "pourcent" => $ballot["bal_percent"] . "%"));

//	echo $ballot["cit_geo_shape"] . "<br>";

	$shape = $ballot["cit_geo_shape"];
	$shape = substr($shape, 1);
	$shape = substr($shape, 0, mb_strlen($shape) - 1);
	$shape = str_replace("\"\"", "\"", $shape);

//	echo $shape . "<br>";


	$defaultCoordinates = explode(",", $ballot["cit_geo_point_2d"]);
	$ballotInformation["properties"]["defaultCoordinates"] = array(trim($defaultCoordinates[0]) * 1., trim($defaultCoordinates[1]) * 1.);
	$ballotInformation["geometry"] = json_decode($shape);

	$data["features"][] = $ballotInformation;
}

// echo json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
?>

var citiesData = <?=json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRETTY_PRINT)?>;
