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

class DepartmentBo {
	var $pdo = null;
	var $config = null;

	var $TABLE = "cities";
	var $ID_FIELD = "cit_insee";

	function __construct($pdo, $config) {
		$this->config = $config;
		$this->pdo = $pdo;
	}

	static function newInstance($pdo, $config = null) {
		return new DepartmentBo($pdo, $config);
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
		$queryBuilder->addSelect($this->TABLE . ".cit_department as dep_name");
		$queryBuilder->addSelect($this->TABLE . ".cit_region as dep_region");
		$queryBuilder->addSelect("SUM(" . $this->TABLE . ".cit_population) as dep_population");
		$queryBuilder->addSelect("COUNT(" . $this->TABLE . ".cit_insee) as dep_number_of_cities");
//		$queryBuilder->addSelect("SUBSTRING(" . $this->TABLE . ".cit_zip_code, 1, 2) as dep_num2");
		$queryBuilder->addSelect($this->TABLE . ".cit_code_department as dep_num2");
		$queryBuilder->addSelect("SUBSTRING(" . $this->TABLE . ".cit_zip_code, 1, 3) as dep_num3");

		if (isset($filters[$this->ID_FIELD])) {
			$args[$this->ID_FIELD] = $filters[$this->ID_FIELD];
			$queryBuilder->where("$this->ID_FIELD = :$this->ID_FIELD");
		}
/*
		if (isset($filters["cit_zip_code"])) {
			$args["cit_zip_code"] = $filters["cit_zip_code"];
			$queryBuilder->where("cit_zip_code = :cit_zip_code");
		}
*/
		if (isset($filters["dep_like_name"])) {
			$args["dep_like_name"] = $filters["dep_like_name"] . "%";
			$queryBuilder->where("cit_department LIKE :dep_like_name");
		}

		$queryBuilder->groupBy("dep_name");

		if (isset($filters["order_by_num2"])) {
			$queryBuilder->orderBy("dep_num2");
		}

		if (isset($filters["order_by_region"])) {
			$queryBuilder->orderBy("dep_region");
		}

		if (isset($filters["order_by_name"])) {
			$queryBuilder->orderBy("dep_name");
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