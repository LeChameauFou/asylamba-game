<?php

/**
 * Color Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 26.11.13
*/

namespace Asylamba\Modules\Demeter\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Classes\Worker\ASM;

class ColorManager extends Manager {
	protected $managerType ='_Color';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'c.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = Database::getInstance();
		$qr = $db->prepare('SELECT c.*
			FROM color AS c
			' . $formatWhere .'
			' . $formatOrder .'
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

		if (empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}

		$awColor = $qr->fetchAll();

		$qr->closeCursor();

		$qr = $db->prepare('SELECT c.*
			FROM colorLink AS c ORDER BY rColorLinked'
		);

		$qr->execute();

		$awColorLink = $qr->fetchAll();
		$qr->closeCursor();

		$colorsArray = array();

		for ($i = 0; $i < count($awColor); $i++) {
			$color = new Color();
			$color->id = $awColor[$i]['id'];
			$color->alive = $awColor[$i]['alive'];
			$color->isWinner = $awColor[$i]['isWinner'];
			$color->credits = $awColor[$i]['credits'];
			$color->players = $awColor[$i]['players'];
			$color->activePlayers = $awColor[$i]['activePlayers'];
			$color->rankingPoints = $awColor[$i]['rankingPoints'];
			$color->points = $awColor[$i]['points'];
			$color->sectors = $awColor[$i]['sectors'];
			$color->electionStatement = $awColor[$i]['electionStatement'];
			$color->isClosed = $awColor[$i]['isClosed'];
			$color->description = $awColor[$i]['description'];
			$color->dClaimVictory = $awColor[$i]['dClaimVictory'];
			$color->dLastElection = $awColor[$i]['dLastElection'];
			$color->isInGame = $awColor[$i]['isInGame'];
			$color->colorLink[0] = Color::NEUTRAL;

			$color->officialName = ColorResource::getInfo($color->id, 'officialName');
			$color->popularName = ColorResource::getInfo($color->id, 'popularName');
			$color->government = ColorResource::getInfo($color->id, 'government');
			$color->demonym = ColorResource::getInfo($color->id, 'demonym');
			$color->factionPoint = ColorResource::getInfo($color->id, 'factionPoint');
			$color->status = ColorResource::getInfo($color->id, 'status');
			$color->regime = ColorResource::getInfo($color->id, 'regime');
			$color->devise = ColorResource::getInfo($color->id, 'devise');
			$color->desc1 = ColorResource::getInfo($color->id, 'desc1');
			$color->desc2 = ColorResource::getInfo($color->id, 'desc2');
			$color->desc3 = ColorResource::getInfo($color->id, 'desc3');
			$color->desc4 = ColorResource::getInfo($color->id, 'desc4');
			$color->bonus = ColorResource::getInfo($color->id, 'bonus');
			$color->mandateDuration = ColorResource::getInfo($color->id, 'mandateDuration');
			$color->senateDesc = ColorResource::getInfo($color->id, 'senateDesc');
			$color->campaignDesc = ColorResource::getInfo($color->id, 'campaignDesc');
	
			$color->bonusText = [];
			foreach (ColorResource::getInfo($color->id, 'bonus') AS $k) {
				$color->bonusText[] = ColorResource::getBonus($k);
			}

			if ($color->id != 0) {
				foreach ($awColorLink AS $colorLink) {
					if ($colorLink['rColor'] == $color->id) {
						$color->colorLink[$colorLink['rColorLinked']] = $colorLink['statement'];
					}
				}
			} else {
				$color->colorLink = array(Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL, Color::NEUTRAL);
			}

			$this->_Add($color);
			if ($this->currentSession->getUMode()) {
				$color->uMethod();
			}
		}
	}

	public function save() {
		$colors = $this->_Save();

		foreach ($colors AS $color) {
			$db = Database::getInstance();
			$qr = $db->prepare('UPDATE color
				SET
					alive = ?,
					isWinner = ?,
					credits = ?,
					players = ?,	
					activePlayers = ?,
					rankingPoints = ?,
					points = ?,
					sectors = ?,
					electionStatement = ?,
					isClosed = ?,
					isInGame = ?,
					description = ?,
					dClaimVictory = ?,
					dLastElection = ?
				WHERE id = ?');
			$aw = $qr->execute(array(
					$color->alive,
					$color->isWinner,
					$color->credits,
					$color->players,
					$color->activePlayers,
					$color->rankingPoints,
					$color->points,
					$color->sectors,
					$color->electionStatement,
					$color->isClosed,
					$color->isInGame,
					$color->description,
					$color->dClaimVictory,
					$color->dLastElection,
					$color->id
				));

			$qr2 = $db->prepare('UPDATE colorLink SET
					statement = ? WHERE rColor = ? AND rColorLinked = ?
				');

			foreach ($color->colorLink as $key => $value) {
				$qr2->execute(array($value, $color->id, $key));
			}
		}
	}

	public function add($newColor) {
		$db = Database::getInstance();

		$qr = $db->prepare('INSERT INTO color
		SET
			id = ?,
			alive = ?,
			isWinner = ?,
			credits = ?,
			players = ?,		
			activePlayers = ?,
			rankingPoints = ?,
			points = ?,
			sectors = ?,
			electionStatement = ?,
			isClosed = ?,
			isInGame = ?,
			description = ?,
			dClaimVictory = ?,
			dLastElection = ?');
		$aw = $qr->execute(array(
				$color->id,
				$color->alive,
				$color->isWinner,
				$color->credits,
				$color->players,
				$color->activePlayers,
				$color->rankingPoints,
				$color->points,
				$color->sectors,
				$color->electionStatement,
				$color->isClosed,
				$color->isInGame,
				$color->description,
				$color->dClaimVictory,
				$color->dLastElection
			));

		$newColor->id = $db->lastInsertId();

		$this->_Add($newColor);

		return $newColor->id;
	}

	public function deleteById($id) {
		$db = Database::getInstance();
		$qr = $db->prepare('DELETE FROM color WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}

	// FONCTIONS STATICS
	public static function updateInfos($id) {
		self::updatePlayers($id);
		self::updateActivePlayers($id);
	}

	public static function updatePlayers($id) {
		$_CLM1 = ASM::$clm->getCurrentSession();
		ASM::$clm->newSession();
		ASM::$clm->load(array('id' => $id));

		$_PAM = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(FALSE);
		ASM::$pam->load(array('statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY), 'rColor' => $id));

		ASM::$clm->getById($id)->players = ASM::$pam->size();	

		ASM::$pam->changeSession($_PAM);
		ASM::$clm->changeSession($_CLM1);
	}

	public static function updateActivePlayers($id) {
		$_CLM1 = ASM::$clm->getCurrentSession();
		ASM::$clm->newSession();
		ASM::$clm->load(array('id' => $id));

		$_PAM = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(FALSE);
		ASM::$pam->load(array('statement' => PAM_ACTIVE, 'rColor' => $id));
		
		ASM::$clm->getById($id)->activePlayers = ASM::$pam->size();

		ASM::$pam->changeSession($_PAM);
		ASM::$clm->changeSession($_CLM1);
	}
}
