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

class CityBo {
	var $pdo = null;
	var $config = null;

	var $TABLE = "cities";
	var $ID_FIELD = "cit_insee";

	function __construct($pdo, $config) {
		$this->config = $config;
		$this->pdo = $pdo;
	}

	static function newInstance($pdo, $config = null) {
		return new CityBo($pdo, $config);
	}

	function create(&$motion) {
		return BoHelper::create($motion, $this->TABLE, $this->ID_FIELD, $this->config, $this->pdo);
	}

	function update($motion) {
		return BoHelper::update($motion, $this->TABLE, $this->ID_FIELD, $this->config, $this->pdo);
	}

	function save(&$motion) {
 		if (!isset($motion[$this->ID_FIELD]) || !$motion[$this->ID_FIELD]) {
			$this->create($motion);
		}

		$this->update($motion);
	}

	function getById($id) {
		$filters = array($this->ID_FIELD => intval($id));

		$results = $this->getByFilters($filters);

		if (count($results)) {
			return $results[0];
		}

		return null;
	}

	function getByFilters($filters = null) {
		if (!$filters) $filters = array();
		$args = array();

		$queryBuilder = QueryFactory::getInstance($this->config["database"]["dialect"]);

		$queryBuilder->select($this->TABLE);
		$queryBuilder->addSelect($this->TABLE . ".*");

		if (isset($filters[$this->ID_FIELD])) {
			$args[$this->ID_FIELD] = $filters[$this->ID_FIELD];
			$queryBuilder->where("$this->ID_FIELD = :$this->ID_FIELD");
		}

		if (isset($filters["cit_zip_code"])) {
			$args["cit_zip_code"] = $filters["cit_zip_code"];
			$queryBuilder->where("cit_zip_code = :cit_zip_code");
		}

		if (isset($filters["cit_region"])) {
			$args["cit_region"] = $filters["cit_region"];
			$queryBuilder->where("cit_region = :cit_region");
		}

		if (isset($filters["cit_department"])) {
			$args["cit_department"] = $filters["cit_department"];
			$queryBuilder->where("cit_department = :cit_department");
		}

		if (isset($filters["cit_like_name"])) {
			$args["cit_like_name"] = $filters["cit_like_name"] . "%";
			$queryBuilder->where("cit_name LIKE :cit_like_name");
		}

		if (isset($filters["cit_like_zip_code"])) {
			$args["cit_like_zip_code"] = $filters["cit_like_zip_code"] . "%";
			$queryBuilder->where("cit_zip_code LIKE :cit_like_zip_code");
		}

		$query = $queryBuilder->constructRequest();
		$statement = $this->pdo->prepare($query);

/*
		if (isset($filters["with_total_votes"])) {
			echo showQuery($query, $args);	
		}
*/

//		exit();
//		error_log(showQuery($query, $args));
//		echo showQuery($queryBuilder->constructRequest(), $args);

		$results = array();

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			foreach($results as $index => $line) {
				foreach($line as $field => $value) {
					if (is_numeric($field)) {
						unset($results[$index][$field]);
					}
				}
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return $results;
	}

}