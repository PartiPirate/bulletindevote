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

$connection = openConnection();

$cityBo = CityBo::newInstance($connection, $config);
$cities = $cityBo->getByFilters(array("with_votes" => 1, "res_election" => "eur_2019", "can_order" => "4"/*, "with_ballots" => 1*/));

$data = array();
$data["type"] = "FeatureCollection";
$data["features"] = array();

foreach($cities as $city) {
	$cityInformation = array("type" => "Feature", 
	                            "id" => $city["cit_insee"], 
	                            "properties" => array("name" => $city["cit_name"], 
	                                                    "pourcent" => $city["cit_percent_votes"] . "%", 
	                                                    "insee" => $city["cit_insee"], 
	                                                    "cit_votes" => $city["cit_votes"], 
	                                                    "cit_cast" => $city["cit_cast"], 
	                                                    "cit_percent_votes" => $city["cit_percent_votes"] / 100.,
	                                                    "cit_ballot_percent" => $city["cit_ballot_percent"]
	                                                   )
	                           );

//	echo $city["cit_geo_shape"] . "<br>";

	$shape = $city["cit_geo_shape"];
	$shape = substr($shape, 1);
	$shape = substr($shape, 0, mb_strlen($shape) - 1);
	$shape = str_replace("\"\"", "\"", $shape);

//	echo $shape . "<br>";

	$defaultCoordinates = explode(",", $city["cit_geo_point_2d"]);
	$cityInformation["properties"]["defaultCoordinates"] = array(trim($defaultCoordinates[0]) * 1., trim($defaultCoordinates[1]) * 1.);
	$cityInformation["geometry"] = json_decode($shape);

	$data["features"][] = $cityInformation;
}

// echo json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
?>

<?php   if (!isset($_REQUEST["json"])) { ?>
var citiesData = <?=json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRETTY_PRINT)?>;
<?php   } else {
    
            echo json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRETTY_PRINT);
    
        }
?>