<?php

/**
 * RecyclingLogManager
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @version 09.02.15
 **/
namespace Asylamba\Modules\Athena\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;

use Asylamba\Modules\Athena\Model\RecyclingLog;

class RecyclingLogManager extends Manager {
	protected $managerType = '_RecyclingLog';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'rl.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = Database::getInstance();
		$qr = $db->prepare('SELECT rl.*
			FROM recyclingLog AS rl
			' . $formatWhere . '
			' . $formatOrder . '
			' . $formatLimit
		);

		foreach($where AS $v) {
			if (is_array($v)) {
				foreach ($v as $p) {
					$valuesArray[] = $p;
				}
			} else {
				$valuesArray[] = $v;
			}
		}

		if(empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}

		$this->fill($qr);
	}

	protected function fill($qr) {
		while ($aw = $qr->fetch()) {
			$rl = new RecyclingLog();

			$rl->id = $aw['id'];
			$rl->rRecycling = $aw['rRecycling'];
			$rl->resources = $aw['resources'];
			$rl->credits = $aw['credits'];
			$rl->ship0 = $aw['ship0'];
			$rl->ship1 = $aw['ship1'];
			$rl->ship2 = $aw['ship2'];
			$rl->ship3 = $aw['ship3'];
			$rl->ship4 = $aw['ship4'];
			$rl->ship5 = $aw['ship5'];
			$rl->ship6 = $aw['ship6'];
			$rl->ship7 = $aw['ship7'];
			$rl->ship8 = $aw['ship8'];
			$rl->ship9 = $aw['ship9'];
			$rl->ship10 = $aw['ship10'];
			$rl->ship11 = $aw['ship11'];
			$rl->dLog = $aw['dLog'];

			$this->_Add($rl);
		}
	}

	public function add(RecyclingLog $rl) {
		$db = Database::getInstance();
		$qr = $db->prepare('INSERT INTO
			recyclingLog(rRecycling, resources, credits, ship0, ship1, ship2, ship3, ship4, ship5, ship6, ship7,
				ship8, ship9, ship10, ship11, dLog)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$rl->rRecycling,
			$rl->resources,
			$rl->credits,
			$rl->ship0,
			$rl->ship1,
			$rl->ship2,
			$rl->ship3,
			$rl->ship4,
			$rl->ship5,
			$rl->ship6,
			$rl->ship7,
			$rl->ship8,
			$rl->ship9,
			$rl->ship10,
			$rl->ship11,
			$rl->dLog
		));

		$rl->id = $db->lastInsertId();

		$this->_Add($rl);
	}

	public function save() {
		$recyclingLogs = $this->_Save();

		foreach ($recyclingLogs AS $rl) {
			$db = Database::getInstance();
			$qr = $db->prepare('UPDATE recyclingLog
				SET	id = ?,
					rRecycling = ?,
					resources = ?,
					credits = ?,
					ship0 = ?,
					ship1 = ?,
					ship2 = ?,
					ship3 = ?,
					ship4 = ?,
					ship5 = ?,
					ship6 = ?,
					ship7 = ?,
					ship8 = ?,
					ship9 = ?,
					ship10 = ?,
					ship11 = ?,
					dLog = ?
				WHERE id = ?');
			$qr->execute(array(
				$rl->id,
				$rl->rRecycling,
				$rl->resources,
				$rl->credits,
				$rl->ship0,
				$rl->ship1,
				$rl->ship2,
				$rl->ship3,
				$rl->ship4,
				$rl->ship5,
				$rl->ship6,
				$rl->ship7,
				$rl->ship8,
				$rl->ship9,
				$rl->ship10,
				$rl->ship11,
				$rl->dLog,
				$rl->id
			));
		}
	}

	public function deleteById($id) {
		$db = Database::getInstance();
		$qr = $db->prepare('DELETE FROM recyclingLog WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		
		return TRUE;
	}

	public function deleteAllFromMission($missionId) {
		$db = Database::getInstance();
		$qr = $db->prepare('SELECT id FROM recyclingLog WHERE rRecycling = ?');
		$qr->execute(array($missionId));

		while ($aw = $qr->fetch()) {
			$this->deleteById($aw['id']);
		}

		return TRUE;
	}
}