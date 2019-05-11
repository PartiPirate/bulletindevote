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
require_once("engine/bo/CityBo.php");

$connection = openConnection();

$ballotBo = BallotBo::newInstance($connection, $config);
$cityBo = CityBo::newInstance($connection, $config);

$ballots = $ballotBo->getByFilters(array("bal_confirmed" => 1));

$mail = $_REQUEST["xxx"];
$confirmation = $_REQUEST["confirmationMail"];

if ($mail != $confirmation) {
    echo json_encode(array("ko" => "ko"));
    exit();
}

$destinations = json_decode($_REQUEST["destinations-input"], true);

$acceptNotification = isset($_REQUEST["accept_notification"]);

foreach($destinations as $destination) {
    if (isset($destination["insee"])) {
        $ballot = array("bal_city_insee" => $destination["insee"], "bal_email" => $mail, "bal_percent" => $destination["proportion"]);
        $ballot["bal_accept_notification"] = $acceptNotification;
        $ballotBo->save($ballot);
    }
    else {
        $filters = array();

        if ($destination["type"] == "region") {
        	$filters["cit_region"] = $destination["zone"];
        }
        else if ($destination["type"] == "departement") {
        	$filters["cit_department"] = $destination["zone"];
        }
        else {
        	if (intval($destination["zone"])) {
        		$filters["cit_like_zip_code"] = $destination["zone"];
        	}
        	else {
        		$filters["cit_like_name"] = $destination["zone"];
        	}
        }

        $cities = $cityBo->getByFilters($filters);

        foreach($cities as $city) {
            $ballot = array("bal_city_insee" => $city["cit_insee"], "bal_email" => $mail, "bal_percent" => $destination["proportion"]);
            $ballot["bal_accept_notification"] = $acceptNotification;
            $ballotBo->save($ballot);
        }
    }
}

echo json_encode(array("ok" => "ok"));