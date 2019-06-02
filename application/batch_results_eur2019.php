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
require_once("engine/bo/ResultBo.php");
require_once("engine/bo/CandidateBo.php");

$connection = openConnection();

$resultBo    = ResultBo::newInstance($connection, $config);
$candidateBo = CandidateBo::newInstance($connection, $config);

$csv = "resultats-eur_2019-definitifs-par-bureau-de-vote.txt";
$row = 0;
$election = "eur_2019";

if (($handle = fopen($csv, "r")) !== FALSE) {
    $header = fgetcsv($handle, 0, ";");

    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
/*        
        $num = count($data);
        echo "<p> $num champs à la ligne $row: <br /></p>\n";
        $row++;

        for ($c = 0; $c < $num; $c++) {
            echo $data[$c] . "<br />\n";
        }
*/        
        $result = array();
        $result["res_city_insee"] = $data[0] . $data[2];
        $result["res_election"] = $election;
        $result["res_vote_place"] = $data[4];
        $result["res_registered"] = $data[5];
        $result["res_abstention"] = $data[6];
        $result["res_voters"] = $data[8];
        $result["res_white"] = $data[10];
        $result["res_null"] = $data[13];
        $result["res_cast"] = $data[16];
        $result["res_id"] = 0;

        $resultBo->save($result);
/*
        if ($result["res_city_insee"] == "82073") {
            print_r($data);
            echo "\n";

            print_r($result);
            echo "\n";
*/
            for($index = 19; $index < count($data); $index += 7) {
                $candidate = array();
                $candidate["can_result_id"] = $result["res_id"];
                $candidate["can_order"] = $data[$index + 0];
                $candidate["can_label"] = $data[$index + 2];
                $candidate["can_head"] = $data[$index + 3];
                $candidate["can_votes"] = $data[$index + 4];
/*                
                $candidate["can_percent_registered"] = str_replace(",", '.', $data[$index + 5]);
                $candidate["can_precent_cast"] = str_replace(",", '.', $data[$index + 6]);
*/

/*
                print_r($candidate);
                echo "\n";
*/                
                $candidateBo->save($candidate);
            }
//        }

//        exit();
    }
    
    fclose($handle);
}