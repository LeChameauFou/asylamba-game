<?php
/**
 * Place
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Gaia
 * @update 21.04.13
*/
namespace Asylamba\Modules\Gaia\Model;

use Asylamba\Classes\Worker\CTC;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Format;

use Asylamba\Modules\Ares\FightController;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Ares\Model\LiveReport;
use Asylamba\Modules\Ares\Model\Report;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Demeter\Model\Color;

use Asylamba\Modules\Gaia\Resource\SquadronResource;

class Place { 
	# CONSTANTS
	const TYP_EMPTY = 0;
	const TYP_MS1 = 1;
	const TYP_MS2 = 2;
	const TYP_MS3 = 3;
	const TYP_ORBITALBASE = 4;

	const COEFFMAXRESOURCE = 600;
	const COEFFRESOURCE = 2;
	const REPOPDANGER = 2;
	const COEFFPOPRESOURCE = 50;
	const COEFFDANGER = 5;

	# typeOfPlace
	const TERRESTRIAL = 1;
	const EMPTYZONE = 6; # zone vide

	# CONST PNJ COMMANDER
	const LEVELMAXVCOMMANDER = 20;
	const POPMAX 			 = 250;
	const DANGERMAX 		 = 100;

	# CONST RESULT BATTLE
	const CHANGESUCCESS 						= 10;
	const CHANGEFAIL							= 11;
	const CHANGELOST							= 12;

	const LOOTEMPTYSSUCCESS 					= 20;
	const LOOTEMPTYFAIL							= 21;
	const LOOTPLAYERWHITBATTLESUCCESS			= 22;
	const LOOTPLAYERWHITBATTLEFAIL				= 23;
	const LOOTPLAYERWHITOUTBATTLESUCCESS		= 24;
	const LOOTLOST								= 27;

	const CONQUEREMPTYSSUCCESS 					= 30;
	const CONQUEREMPTYFAIL						= 31;
	const CONQUERPLAYERWHITBATTLESUCCESS		= 32;
	const CONQUERPLAYERWHITBATTLEFAIL			= 33;
	const CONQUERPLAYERWHITOUTBATTLESUCCESS		= 34;
	const CONQUERLOST							= 37;

	const COMEBACK 								= 40;

	# constante de danger
	const DNG_CASUAL 							= 10;
	const DNG_EASY 								= 20;
	const DNG_MEDIUM 							= 50;
	const DNG_HARD 								= 75;
	const DNG_VERY_HARD 						= 100;

	// PLACE
	public $id = 0;
	public $rPlayer = NULL;
	public $rSystem = 0;
	public $typeOfPlace = 0;
	public $position = 0;
	public $population = 0;
	public $coefResources = 0;
	public $coefHistory = 0;
	public $resources = 0; 						# de la place si $typeOfBase = 0, sinon de la base
	public $danger = 0;							# danger actuel de la place (force des flottes rebelles)
	public $maxDanger = 0;						# danger max de la place (force des flottes rebelles)
	public $uPlace = '';

	// SYSTEM
	public $rSector = 0;
	public $xSystem = 0;
	public $ySystem = 0;
	public $typeOfSystem = 0;

	// SECTOR
	public $tax = 0;
	public $sectorColor = 0;

	// PLAYER
	public $playerColor = 0;
	public $playerName = '';
	public $playerAvatar = '';
	public $playerStatus = 0;
	public $playerLevel = 0;

	// BASE
	public $typeOfBase = 0;
	public $typeOfOrbitalBase;
	public $baseName = '';
	public $points = '';

	// OB
	public $levelCommercialPlateforme = 0;
	public $levelSpatioport = 0;
	public $antiSpyInvest = 0;

	// COMMANDER 
	public  $commanders = array();

	//uMode
	public $uMode = TRUE;

	public function getId() 							{ return $this->id; }
	public function getRPlayer() 						{ return $this->rPlayer; }
	public function getRSystem() 						{ return $this->rSystem; }
	public function getTypeOfPlace() 					{ return $this->typeOfPlace; }
	public function getPosition() 						{ return $this->position; }
	public function getPopulation() 					{ return $this->population; }
	public function getCoefResources() 					{ return $this->coefResources; }
	public function getCoefHistory() 					{ return $this->coefHistory; }
	public function getResources() 						{ return $this->resources; }
	public function getRSector() 						{ return $this->rSector; }
	public function getXSystem() 						{ return $this->xSystem; }
	public function getYSystem() 						{ return $this->ySystem; }
	public function getTypeOfSystem() 					{ return $this->typeOfSystem; }
	public function getTax() 							{ return $this->tax; }
	public function getSectorColor() 					{ return $this->sectorColor; }
	public function getPlayerColor() 					{ return $this->playerColor; }
	public function getPlayerName() 					{ return $this->playerName; }
	public function getPlayerAvatar() 					{ return $this->playerAvatar; }
	public function getPlayerStatus() 					{ return $this->playerStatus; }
	public function getTypeOfBase() 					{ return $this->typeOfBase; }
	public function getBaseName() 						{ return $this->baseName; }
	public function getPoints() 						{ return $this->points; }
	public function getLevelCommercialPlateforme() 		{ return $this->levelCommercialPlateforme; }
	public function getLevelSpatioport() 				{ return $this->levelSpatioport; }
	public function getAntiSpyInvest()					{ return $this->antiSpyInvest; }

	public function setId($v) 							{ $this->id = $v; }
	public function setRPlayer($v) 						{ $this->rPlayer = $v; }
	public function setRSystem($v) 						{ $this->rSystem = $v; }
	public function setTypeOfPlace($v) 					{ $this->typeOfPlace = $v; }
	public function setPosition($v) 					{ $this->position = $v; }
	public function setPopulation($v) 					{ $this->population = $v; }
	public function setCoefResources($v) 				{ $this->coefResources = $v; }
	public function setCoefHistory($v) 					{ $this->coefHistory = $v; }
	public function setResources($v) 					{ $this->resources = $v; }
	public function setRSector($v) 						{ $this->rSector = $v; }
	public function setXSystem($v) 						{ $this->xSystem = $v; }
	public function setYSystem($v) 						{ $this->ySystem = $v; }
	public function setTypeOfSystem($v) 				{ $this->typeOfSystem = $v; }
	public function setTax($v) 							{ $this->tax = $v; }
	public function setSectorColor($v) 					{ $this->sectorColor = $v; }
	public function setPlayerColor($v) 					{ $this->playerColor = $v; }
	public function setPlayerName($v) 					{ $this->playerName = $v; }
	public function setPlayerAvatar($v) 				{ $this->playerAvatar = $v; }
	public function setPlayerStatus($v) 				{ $this->playerStatus = $v; }
	public function setTypeOfBase($v) 					{ $this->typeOfBase = $v; }
	public function setBaseName($v) 					{ $this->baseName = $v; }
	public function setPoints($v) 						{ $this->points = $v; }
	public function setLevelCommercialPlateforme($v) 	{ $this->levelCommercialPlateforme = $v; }
	public function setLevelSpatioport($v) 				{ $this->levelSpatioport = $v; }
	public function setAntiSpyInvest($v)				{ $this->antiSpyInvest = $v; }

	public function uMethod() {
		$token = CTC::createContext('place');
		$now   = Utils::now();

		if (Utils::interval($this->uPlace, $now, 's') > 0) {
			# update time
			$hours = Utils::intervalDates($now, $this->uPlace);
			$this->uPlace = $now;

			# RESOURCE
			if ($this->typeOfBase == self::TYP_EMPTY && $this->typeOfPlace == self::TERRESTRIAL) {
				foreach ($hours as $hour) {
					CTC::add($hour, $this, 'uResources', array());
				}
			}

			if ($this->rPlayer == NULL) {
				foreach ($hours as $hour) {
					CTC::add($hour, $this, 'uDanger', array());
				}
			}
			$S_COM_OUT = ASM::$com->getCurrentSession();
			ASM::$com->newSession();
			ASM::$com->load([
					'c.rDestinationPlace' => $this->id,
					'c.statement' => Commander::MOVING
				], ['c.dArrival', 'ASC']
			);

			if (ASM::$com->size() > 0) {
				$places = array();
				$playerBonuses = array();

				for ($i = 0; $i < ASM::$com->size(); $i++) { 
					$c = ASM::$com->get($i);
					# fill the places
					$places[] = $c->getRBase();

					# fill & load the bonuses if needed
					if (!array_key_exists($c->rPlayer, $playerBonuses)) {
						$bonus = new PlayerBonus($c->rPlayer);
						$bonus->load();
						$playerBonuses[$c->rPlayer] = $bonus;
					}
				}

				# load all the places at the same time
				$S_PLM_OUT = ASM::$plm->getCurrentSession();
				ASM::$plm->newSession();
				ASM::$plm->load(['id' => $places]);

				# load annexes components
				for ($i = 0; $i < ASM::$com->size(); $i++) { 
					$commander = ASM::$com->get($i);

					# only if the commander isn't in travel
					$hasntU = TRUE;
					if ($commander->uMethodCtced) {
						$hasntU = FALSE;

						if (Utils::interval($commander->lasrUMethod, Utils::now(), 's') > 10) {
							$hasntU = TRUE;
						}
					}
					if ($commander->dArrival <= $now and $hasntU) {
						switch ($commander->travelType) {
							case Commander::MOVE: 
								$place = ASM::$plm->getById($commander->rBase);
								$bonus = $playerBonuses[$commander->rPlayer];
								
								if (CTC::add($commander->dArrival, $this, 'uChangeBase', [$commander, $place, $bonus])) {
									$commander->uMethodCtced = TRUE;
									$commander->lastUMethod = Utils::now();
								}
							break;

							case Commander::LOOT: 
								$place = ASM::$plm->getById($commander->rBase);
								$bonus = $playerBonuses[$commander->rPlayer];

								$S_PAM_L1 = ASM::$pam->getCurrentSession();
								ASM::$pam->newSession();
								ASM::$pam->load(['id' => $commander->rPlayer]);
								$commanderPlayer = ASM::$pam->get();
								ASM::$pam->changeSession($S_PAM_L1);

								if ($this->rPlayer != NULL) {
									$S_PAM_L2 = ASM::$pam->getCurrentSession();
									ASM::$pam->newSession();
									ASM::$pam->load(['id' => $this->rPlayer]);
									$placePlayer = ASM::$pam->get();
									ASM::$pam->changeSession($S_PAM_L2);

									$S_OBM_L1 = ASM::$obm->getCurrentSession();
									ASM::$obm->newSession();
									ASM::$obm->load(['rPlace' => $this->id]);
									$placeBase = ASM::$obm->get();
									ASM::$obm->changeSession($S_OBM_L1);
								} else {
									$placePlayer = NULL;
									$placeBase = NULL;
								}

								$S_CLM_L1 = ASM::$clm->getCurrentSession();
								ASM::$clm->newSession();
								ASM::$clm->load(['id' => $commander->playerColor]);
								$commanderColor = ASM::$clm->get();
								ASM::$clm->changeSession($S_CLM_L1);

								if (CTC::add($commander->dArrival, $this, 'uLoot', array($commander, $place, $bonus, $commanderPlayer, $placePlayer, $placeBase, $commanderColor))) {
									$commander->uMethodCtced = TRUE;
									$commander->lastUMethod = Utils::now();
								}
							break;

							case Commander::COLO: 
								$place = ASM::$plm->getById($commander->rBase);
								$bonus = $playerBonuses[$commander->rPlayer];

								$S_PAM_C1 = ASM::$pam->getCurrentSession();
								ASM::$pam->newSession();
								ASM::$pam->load(array('id' => $commander->rPlayer));
								$commanderPlayer = ASM::$pam->get();
								ASM::$pam->changeSession($S_PAM_C1);

								if ($this->rPlayer != NULL) {
									$S_PAM_C2 = ASM::$pam->getCurrentSession();
									ASM::$pam->newSession();
									ASM::$pam->load(array('id' => $this->rPlayer));
									$placePlayer = ASM::$pam->get();
									ASM::$pam->changeSession($S_PAM_C2);

									$S_OBM_C1 = ASM::$obm->getCurrentSession();
									ASM::$obm->newSession();
									ASM::$obm->load(array('rPlace' => $this->id));
									$placeBase = ASM::$obm->get();
									ASM::$obm->changeSession($S_OBM_C1);

									$S_CRM_C1 = ASM::$crm->getCurrentSession();
									ASM::$crm->newSession();
									ASM::$crm->load(['rOrbitalBase' => $this->id]);
									ASM::$crm->load(['rOrbitalBaseLinked' => $this->id]);
									$S_CRM_C2 = ASM::$crm->getCurrentSession();
									ASM::$crm->changeSession($S_CRM_C1);

									$S_REM_C1 = ASM::$rem->getCurrentSession();
									ASM::$rem->newSession();
									ASM::$rem->load(array('rBase' => $this->id));
									$S_REM_C2 = ASM::$rem->getCurrentSession();
									ASM::$rem->changeSession($S_REM_C1);

									$S_COM_C1 = ASM::$com->getCurrentSession();
									ASM::$com->newSession(); # CRASH
									ASM::$com->load(array('c.rBase' => $this->id));
									$S_COM_C2 = ASM::$com->getCurrentSession();
									ASM::$com->changeSession($S_COM_C1);

								} else {
									$placePlayer = NULL;
									$placeBase = NULL;
									$S_CRM_C2 = NULL;
									$S_REM_C2 = NULL;
									$S_COM_C2 = NULL;
								}

								$S_CLM = ASM::$clm->getCurrentSession();
								ASM::$clm->newSession();
								ASM::$clm->load(array('id' => $commander->playerColor));
								$commanderColor = ASM::$clm->get();
								ASM::$clm->changeSession($S_CLM);
								
								if (CTC::add($commander->dArrival, $this, 'uConquer', array($commander, $place, $bonus, $commanderPlayer, $placePlayer, $placeBase, $commanderColor, $S_CRM_C2, $S_REM_C2, $S_COM_C2))) {
									$commander->uMethodCtced = TRUE;
									$commander->lastUMethod = Utils::now();
								}
							break;

							case Commander::BACK: 
								$S_OBM_B1 = ASM::$obm->getCurrentSession();

								ASM::$obm->newSession();
								ASM::$obm->load(array('rPlace' => $commander->getRBase()));
								$base = ASM::$obm->get();
								ASM::$obm->changeSession($S_OBM_B1);
								
								if (CTC::add($commander->dArrival, $this, 'uComeBackHome', array($commander, $base))) {
									$commander->uMethodCtced = TRUE;
									$commander->lastUMethod = Utils::now();
								}
							break;

							default: CTR::$alert->add('Cette action n\'existe pas.', ALT_BUG_INFO);
						}
					}
				}
				ASM::$plm->changeSession($S_PLM_OUT);
			}
			ASM::$com->changeSession($S_COM_OUT);
		}
		CTC::applyContext($token);
	}

	public function uDanger() {
		$this->danger += self::REPOPDANGER;

		if ($this->danger > $this->maxDanger) {
			$this->danger = $this->maxDanger;
		}
	}

	public function uResources() {
		$maxResources = ceil($this->population / self::COEFFPOPRESOURCE) * self::COEFFMAXRESOURCE * ($this->maxDanger + 1);
		$this->resources += floor(self::COEFFRESOURCE * $this->population);

		if ($this->resources > $maxResources) {
			$this->resources = $maxResources;
		}
	}

	# déplacement de flotte
	public function uChangeBase($commander, $commanderPlace, $playerBonus) {
		# si la place et la flotte ont la même couleur
		# on pose la flotte si il y a assez de place
		# sinon on met la flotte dans les hangars
		if ($this->playerColor == $commander->playerColor AND $this->typeOfBase == 4) {
			$maxCom = ($this->typeOfOrbitalBase == OrbitalBase::TYP_MILITARY || $this->typeOfOrbitalBase == OrbitalBase::TYP_CAPITAL)
				? OrbitalBase::MAXCOMMANDERMILITARY
				: OrbitalBase::MAXCOMMANDERSTANDARD;

			# si place a assez de case libre :
			if (count($this->commanders) < $maxCom) {
				$comLine2 = 0;

				foreach ($this->commanders as $com) {
					if ($com->line == 2) {
						$comLine2++;
					}
				}

				if ($maxCom == OrbitalBase::MAXCOMMANDERMILITARY) {
					if ($comLine2 < 2) {
						$commander->line = 2;
					} else {
						$commander->line = 1;
					}
				} else {
					if ($comLine2 < 1) {
						$commander->line = 2;
					} else {
						$commander->line = 1;
					}
				}

				# changer rBase commander
				$commander->rBase = $this->id;
				$commander->travelType = NULL;
				$commander->statement = Commander::AFFECTED;

				# ajouter à la place le commandant
				$this->commanders[] = $commander;

				# envoi de notif
				$this->sendNotif(self::CHANGESUCCESS, $commander);
			} else {
				# changer rBase commander
				$commander->rBase = $this->id;
				$commander->travelType = NULL;
				$commander->statement = Commander::RESERVE;

				$commander->emptySquadrons();

				# envoi de notif
				$this->sendNotif(self::CHANGEFAIL, $commander);
			}

			# modifier le rPlayer (ne se modifie pas si c'est le même)
			$commander->rPlayer = $this->rPlayer;

			# instance de la place d'envoie + suppr commandant de ses flottes
			# enlever à rBase le commandant
			for ($i = 0; $i < count($commanderPlace->commanders); $i++) {
				if ($commanderPlace->commanders[$i]->id == $commander->id) {
					unset($commanderPlace->commanders[$i]);
					$commanderPlace->commanders = array_merge($commanderPlace->commanders);
				}
			}
		} else {
			# retour forcé
			$this->comeBack($commander, $commanderPlace, $playerBonus);
			$this->sendNotif(self::CHANGELOST, $commander);
		}
	}

	# pillage
	public function uLoot($commander, $commanderPlace, $playerBonus, $commanderPlayer, $placePlayer, $placeBase, $commanderColor) {
		LiveReport::$type   = Commander::LOOT;
		LiveReport::$dFight = $commander->dArrival;

		# si la planète est vide
		if ($this->rPlayer == NULL) {
			LiveReport::$isLegal = Report::LEGAL;

			$commander->travelType = NULL;
			$commander->travelLength = NULL;

			# planète vide : faire un combat
			$this->startFight($commander, $commanderPlayer);

			# victoire
			if ($commander->getStatement() != Commander::DEAD) {
				# piller la planète
				$this->lootAnEmptyPlace($commander, $playerBonus);
				# création du rapport de combat
				$report = $this->createReport();

				# réduction de la force de la planète
				$percentage = (($report->pevAtEndD + 1) / ($report->pevInBeginD + 1)) * 100;
				$this->danger = round(($percentage * $this->danger) / 100);

				$this->comeBack($commander, $commanderPlace, $playerBonus);
				$this->sendNotif(self::LOOTEMPTYSSUCCESS, $commander, $report->id);
			} else {
				# si il est mort
				# enlever le commandant de la session
				for ($i = 0; $i < count($commanderPlace->commanders); $i++) {
					if ($commanderPlace->commanders[$i]->getId() == $commander->getId()) {
						unset($commanderPlace->commanders[$i]);
						$commanderPlace->commanders = array_merge($commanderPlace->commanders);
					}
				}

				# création du rapport de combat
				$report = $this->createReport();
				$this->sendNotif(self::LOOTEMPTYFAIL, $commander, $report->id);

				# réduction de la force de la planète
				$percentage = (($report->pevAtEndD + 1) / ($report->pevInBeginD + 1)) * 100;
				$this->danger = round(($percentage * $this->danger) / 100);
			}
		# si il y a une base d'un joueur
		} else {
			if ($commanderColor->colorLink[$this->playerColor] == Color::ALLY || $commanderColor->colorLink[$this->playerColor] == Color::PEACE) {
				LiveReport::$isLegal = Report::ILLEGAL;
			} else {
				LiveReport::$isLegal = Report::LEGAL;
			}

			# planète à joueur : si $this->rColor != commandant->rColor
			# si il peut l'attaquer
			if (($this->playerColor != $commander->getPlayerColor() && $this->playerLevel > 1 && $commanderColor->colorLink[$this->playerColor] != Color::ALLY) || ($this->playerColor == 0)) {
				$commander->travelType = NULL;
				$commander->travelLength = NULL;

				$dCommanders = array();
				foreach ($this->commanders AS $dCommander) {
					if ($dCommander->statement == Commander::AFFECTED && $dCommander->line == 1) {
						$dCommanders[] = $dCommander;
					}
				}

				# il y a des commandants en défense : faire un combat avec un des commandants
				if (count($dCommanders) != 0) {
					$aleaNbr = rand(0, count($dCommanders) - 1);
					$this->startFight($commander, $commanderPlayer, $dCommanders[$aleaNbr], $placePlayer, TRUE);

					# victoire
					if ($commander->getStatement() != COM_DEAD) {
						# piller la planète
						$this->lootAPlayerPlace($commander, $playerBonus, $placeBase);
						$this->comeBack($commander, $commanderPlace, $playerBonus);
	
						# suppression des commandants						
						unset($this->commanders[$aleaNbr]);
						$this->commanders = array_merge($this->commanders);

						# création du rapport
						$report = $this->createReport();

						$this->sendNotif(self::LOOTPLAYERWHITBATTLESUCCESS, $commander, $report->id);
				
					# défaite
					} else {
						# enlever le commandant de la session
						for ($i = 0; $i < count($commanderPlace->commanders); $i++) {
							if ($commanderPlace->commanders[$i]->getId() == $commander->getId()) {
								unset($commanderPlace->commanders[$i]);
								$commanderPlace->commanders = array_merge($commanderPlace->commanders);
							}
						}

						# création du rapport
						$report = $this->createReport();

						# mise à jour des flottes du commandant défenseur
						for ($j = 0; $j < count($dCommanders[$aleaNbr]->armyAtEnd); $j++) {
							for ($i = 0; $i < 12; $i++) { 
								$dCommanders[$aleaNbr]->armyInBegin[$j][$i] = $dCommanders[$aleaNbr]->armyAtEnd[$j][$i];
							}
						}

						$this->sendNotif(self::LOOTPLAYERWHITBATTLEFAIL, $commander, $report->id);
					}
				} else {
					$this->lootAPlayerPlace($commander, $playerBonus, $placeBase);
					$this->comeBack($commander, $commanderPlace, $playerBonus);
					$this->sendNotif(self::LOOTPLAYERWHITOUTBATTLESUCCESS, $commander);
				}

			} else {
				# si c'est la même couleur
				if ($this->rPlayer == $commander->rPlayer) {
					# si c'est une de nos planètes
					# on tente de se poser
					$this->uChangeBase($commander, $commanderPlace, $playerBonus);
				} else {
					# si c'est une base alliée
					# on repart
					$this->comeBack($commander, $commanderPlace, $playerBonus);
					$this->sendNotif(self::CHANGELOST, $commander);
				}
			}
		}
	}

	# conquest
	public function uConquer($commander, $commanderPlace, $playerBonus, $commanderPlayer, $placePlayer, $placeBase, $commanderColor, $routeSession, $recyclingSession, $commanderSession) {
		
		# conquete
		if ($this->rPlayer != NULL) {
			$commander->travelType = NULL;
			$commander->travelLength = NULL;

			if (($this->playerColor != $commander->getPlayerColor() && $this->playerLevel > 3 && $commanderColor->colorLink[$this->playerColor] != Color::ALLY) || ($this->playerColor == 0)) {
				$tempCom = array();

				for ($i = 0; $i < count($this->commanders); $i++) {
					if ($this->commanders[$i]->line <= 1) {
						$tempCom[] = $this->commanders[$i];
					}
				}
				for ($i = 0; $i < count($this->commanders); $i++) {
					if ($this->commanders[$i]->line >= 2) {
						$tempCom[] = $this->commanders[$i];
					}
				}

				$this->commanders = $tempCom;

				$nbrBattle = 0;
				$reportIds   = array();
				$reportArray = array();

				while ($nbrBattle < count($this->commanders)) {
					if ($this->commanders[$nbrBattle]->statement == Commander::AFFECTED) {
						LiveReport::$type = Commander::COLO;
						LiveReport::$dFight = $commander->dArrival;

						if ($commanderColor->colorLink[$this->playerColor] == Color::ALLY || $commanderColor->colorLink[$this->playerColor] == Color::PEACE) {
							LiveReport::$isLegal = Report::ILLEGAL;
						} else {
							LiveReport::$isLegal = Report::LEGAL;
						}

						$this->startFight($commander, $commanderPlayer, $this->commanders[$nbrBattle], $placePlayer, TRUE);

						$report = $this->createReport();
						$reportArray[] = $report;
						$reportIds[] = $report->id;
						
						# PATCH DEGUEU POUR LES MUTLIS-COMBATS
						$_RPM341 = ASM::$rpm->getCurrentSession();
						ASM::$rpm->newSession(TRUE);
						ASM::$rpm->load(['rPlayerAttacker' => $commander->rPlayer, 'rPlace' => $this->id, 'dFight' => $commander->dArrival]);
						if (ASM::$rpm->size() > 0) {
							for ($i = 0; $i < ASM::$rpm->size(); $i++) {
								if (ASM::$rpm->get($i)->id != $report->id) {
									ASM::$rpm->get($i)->statementAttacker = Report::DELETED;
									ASM::$rpm->get($i)->statementDefender = Report::DELETED;
								}
							}
						}
						ASM::$rpm->changeSession($_RPM341);
						########################################

						# mettre à jour armyInBegin si prochain combat pour prochain rapport
						for ($j = 0; $j < count($commander->armyAtEnd); $j++) {
							for ($i = 0; $i < 12; $i++) { 
								$commander->armyInBegin[$j][$i] = $commander->armyAtEnd[$j][$i];
							}
						}
						for ($j = 0; $j < count($this->commanders[$nbrBattle]->armyAtEnd); $j++) {
							for ($i = 0; $i < 12; $i++) {
								$this->commanders[$nbrBattle]->armyInBegin[$j][$i] = $this->commanders[$nbrBattle]->armyAtEnd[$j][$i];
							}
						}
						
						$nbrBattle++;
						# mort du commandant
						# arrêt des combats
						if ($commander->getStatement() == COM_DEAD) {
							break;
						}
					} else {
						$nbrBattle++;
					}
				}

				# victoire
				if ($commander->getStatement() != COM_DEAD) {
					if ($nbrBattle == 0) {
						$this->sendNotif(self::CONQUERPLAYERWHITOUTBATTLESUCCESS, $commander, NULL);
					} else {
						$this->sendNotifForConquest(self::CONQUERPLAYERWHITBATTLESUCCESS, $commander, $reportIds);
					}


					#attribuer le joueur à la place
					$this->commanders = array();
					$this->playerColor = $commander->playerColor;
					$this->rPlayer = $commander->rPlayer;

					# changer l'appartenance de la base (et de la place)
					ASM::$obm->changeOwnerById($this->id, $placeBase, $commander->getRPlayer(), $routeSession, $recyclingSession, $commanderSession);
					$this->commanders[] = $commander;

					$commander->rBase = $this->id;
					$commander->statement = Commander::AFFECTED;
					$commander->line = 2;

					# PATCH DEGUEU POUR LES MUTLIS-COMBATS
					$_NTM465 = ASM::$ntm->getCurrentSession();
					ASM::$ntm->newSession(TRUE);
					ASM::$ntm->load(['rPlayer' => $commander->rPlayer, 'dSending' => $commander->dArrival]);
					ASM::$ntm->load(['rPlayer' => $this->rPlayer, 'dSending' => $commander->dArrival]);
					if (ASM::$ntm->size() > 2) {
						for ($i = 0; $i < ASM::$ntm->size() - 2; $i++) {
							ASM::$ntm->deleteById(ASM::$ntm->get($i)->id);
						}
					}
					ASM::$ntm->changeSession($_NTM465);
					######################################33

				# défaite
				} else {
					for ($i = 0; $i < count($this->commanders); $i++) {
						if ($this->commanders[$i]->statement == COM_DEAD) {
							unset($this->commanders[$i]);
							$this->commanders = array_merge($this->commanders);
						}
					}

					$this->sendNotifForConquest(self::CONQUERPLAYERWHITBATTLEFAIL, $commander, $reportIds);
				}

			} else {
				# si c'est la même couleur
				if ($this->rPlayer == $commander->rPlayer) {
					# si c'est une de nos planètes
					# on tente de se poser
					$this->uChangeBase($commander, $commanderPlace, $playerBonus);
				} else {
					# si c'est une base alliée
					# on repart
					$this->comeBack($commander, $commanderPlace, $playerBonus);
					$this->sendNotif(self::CHANGELOST, $commander);
				}
			}

		# colonisation
		} else {
			$commander->travelType = NULL;
			$commander->travelLength = NULL;

			# faire un combat
			LiveReport::$type = Commander::COLO;
			LiveReport::$dFight = $commander->dArrival;
			LiveReport::$isLegal = Report::LEGAL;

			$this->startFight($commander, $commanderPlayer);

			# victoire
			if ($commander->getStatement() !== COM_DEAD) {
				# attribuer le rPlayer à la Place !
				$this->rPlayer = $commander->rPlayer;
				$this->commanders[] = $commander;
				$this->playerColor = $commander->playerColor;
				$this->typeOfBase = 4; 

				# créer une base
				$ob = new OrbitalBase();
				$ob->rPlace = $this->id;
				$ob->setRPlayer($commander->getRPlayer());
				$ob->setName('colonie');
				$ob->iSchool = 500;
				$ob->iAntiSpy = 500;
				$ob->resourcesStorage = 2000;
				$ob->uOrbitalBase = Utils::now();
				$ob->dCreation = Utils::now();
				$ob->updatePoints();

				$_OBM_UC1 = ASM::$obm->getCurrentSession();
				ASM::$obm->newSession();
				ASM::$obm->add($ob);
				ASM::$obm->changeSession($_OBM_UC1);

				# attibuer le commander à la place
				$commander->rBase = $this->id;
				$commander->statement = COM_AFFECTED;
				$commander->line = 2;

				# ajout de la place en session
				if (CTR::$data->get('playerId') == $commander->getRPlayer()) {
					CTR::$data->addBase('ob', 
						$ob->getId(), 
						$ob->getName(), 
						$this->rSector, 
						$this->rSystem,
						'1-' . Game::getSizeOfPlanet($this->population),
						OrbitalBase::TYP_NEUTRAL);
				}
				
				# création du rapport
				$report = $this->createReport();

				$this->danger = 0;

				$this->sendNotif(self::CONQUEREMPTYSSUCCESS, $commander, $report->id);
			
			# défaite
			} else {
				# création du rapport
				$report = $this->createReport();

				# mise à jour du danger
				$percentage = (($report->pevAtEndD + 1) / ($report->pevInBeginD + 1)) * 100;
				$this->danger = round(($percentage * $this->danger) / 100);

				$this->sendNotif(self::CONQUEREMPTYFAIL, $commander);

				# enlever le commandant de la place
				for ($i = 0; $i < count($commanderPlace->commanders); $i++) {
					if ($commanderPlace->commanders[$i]->getId() == $commander->getId()) {
						unset($commanderPlace->commanders[$i]);
						$commanderPlace->commanders = array_merge($commanderPlace->commanders);
					}
				}
			}
		}
	}

	# retour à la maison
	public function uComeBackHome($commander, $commanderBase) {
		$commander->travelType = NULL;
		$commander->travelLength = NULL;
		$commander->dArrival = NULL;

		$commander->statement = Commander::AFFECTED;

		$this->sendNotif(self::COMEBACK, $commander);

		if ($commander->resources > 0) {
			$commanderBase->increaseResources($commander->resources, TRUE);
			$commander->resources = 0;
		}
	}

	# HELPER

	# comeBack
	public function comeBack($commander, $commanderPlace, $playerBonus) {
		$length   = Game::getDistance($this->getXSystem(), $commanderPlace->getXSystem(), $this->getYSystem(), $commanderPlace->getYSystem());
		$duration = Game::getTimeToTravel($commanderPlace, $this, $playerBonus->bonus);

		$commander->startPlaceName = $this->baseName;
		$commander->destinationPlaceName = $commander->oBName;
		$commander->move($commander->rBase, $this->id, Commander::BACK, $length, $duration);
	}

	private function lootAnEmptyPlace($commander, $playerBonus) {
		$bonus = ($commander->rPlayer != CTR::$data->get('playerId'))
			? $playerBonus->bonus->get(PlayerBonus::SHIP_CONTAINER)
			: CTR::$data->get('playerBonus')->get(PlayerBonus::SHIP_CONTAINER);
	
		$storage = $commander->getPevToLoot() * Commander::COEFFLOOT;
		$storage += round($storage * ((2 * $bonus) / 100));

		$resourcesLooted = 0;
		$resourcesLooted = ($storage > $this->resources) ? $this->resources : $storage;

		$this->resources -= $resourcesLooted;
		$commander->resources = $resourcesLooted;

		LiveReport::$resources = $resourcesLooted;
	}

	private function lootAPlayerPlace($commander, $playerBonus, $placeBase) {
		$bonus = ($commander->rPlayer != CTR::$data->get('playerId'))
			? $playerBonus->bonus->get(PlayerBonus::SHIP_CONTAINER)
			: CTR::$data->get('playerBonus')->get(PlayerBonus::SHIP_CONTAINER);

		$resourcesToLoot = $placeBase->getResourcesStorage() - Commander::LIMITTOLOOT;

		$storage = $commander->getPevToLoot() * Commander::COEFFLOOT;
		$storage += round($storage * ((2 * $bonus) / 100));

		$resourcesLooted = 0;
		$resourcesLooted = ($storage > $resourcesToLoot) ? $resourcesToLoot : $storage;

		if ($resourcesLooted > 0) {
			$placeBase->decreaseResources($resourcesLooted);
			$commander->resources = $resourcesLooted;

			LiveReport::$resources = $resourcesLooted;
		}
	}

	private function startFight($commander, $player, $enemyCommander = NULL, $enemyPlayer = NULL, $pvp = FALSE) {
		if ($pvp == TRUE) {
			$commander->setArmy();
			$enemyCommander->setArmy();

			$fc = new FightController();
			$fc->startFight($commander, $player, $enemyCommander, $enemyPlayer);
		} else {
			$commander->setArmy();
			$computerCommander = $this->createVirtualCommander();

			$fc = new FightController();
			$fc->startFight($commander, $player, $computerCommander);
		}
	}

	private function createReport() {
		$report = new Report();

		$report->rPlayerAttacker = LiveReport::$rPlayerAttacker;
		$report->rPlayerDefender =  LiveReport::$rPlayerDefender;
		$report->rPlayerWinner = LiveReport::$rPlayerWinner;
		$report->avatarA = LiveReport::$avatarA;
		$report->avatarD = LiveReport::$avatarD;
		$report->nameA = LiveReport::$nameA;
		$report->nameD = LiveReport::$nameD;
		$report->levelA = LiveReport::$levelA;
		$report->levelD = LiveReport::$levelD;
		$report->experienceA = LiveReport::$experienceA;
		$report->experienceD = LiveReport::$experienceD;
		$report->palmaresA = LiveReport::$palmaresA;
		$report->palmaresD = LiveReport::$palmaresD;
		$report->resources = LiveReport::$resources;
		$report->expCom = LiveReport::$expCom;
		$report->expPlayerA = LiveReport::$expPlayerA;
		$report->expPlayerD = LiveReport::$expPlayerD;
		$report->rPlace = $this->id;
		$report->type = LiveReport::$type;
		$report->round = LiveReport::$round;
		$report->importance = LiveReport::$importance;
		$report->squadrons = LiveReport::$squadrons;
		$report->dFight = LiveReport::$dFight;
		$report->isLegal = LiveReport::$isLegal;
		$report->placeName = ($this->baseName == '') ? 'planète rebelle' : $this->baseName;
		$report->setArmies();
		$report->setPev();
		
		$id = ASM::$rpm->add($report);
		LiveReport::clear();

		return $report;
	}

	private function sendNotif($case, $commander, $report = NULL) {
		switch ($case) {
			case self::CHANGESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Déplacement réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId(), $commander->getName())
					->addTxt(' est arrivé sur ')
					->addLnk('map/base-' . $this->id, $this->baseName)
					->addTxt('.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;

			case self::CHANGEFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Déplacement réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId(), $commander->getName())
					->addTxt(' s\'est posé sur ')
					->addLnk('map/base-' . $this->id, $this->baseName)
					->addTxt('. Il est en garnison car il n\'y avait pas assez de place en orbite.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CHANGELOST:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Déplacement raté');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId(), $commander->getName())
					->addTxt(' n\'est pas arrivé sur ')
					->addLnk('map/base-' . $this->id, $this->baseName)
					->addTxt('. Cette base ne vous appartient pas. Elle a pu être conquise entre temps.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTEMPTYSSUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a pillé la planète rebelle située aux coordonnées ')
					->addLnk('map/place-' . $this->id, Game::formatCoord($this->xSystem, $this->ySystem, $this->position, $this->rSector))
					->addTxt('.')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResourcesTransported()), 'ressources pillées')
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTEMPTYFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage raté');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/view-memorial', $commander->getName())
					->addTxt(' est tombé lors de l\'attaque de la planète rebelle située aux coordonnées ')
					->addLnk('map/place-' . $this->id, Game::formatCoord($this->xSystem, $this->ySystem, $this->position, $this->rSector))
					->addTxt('.')
					->addSep()
					->addTxt('Il a désormais rejoint le Mémorial. Que son âme traverse l\'Univers dans la paix.')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTPLAYERWHITBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a pillé la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResourcesTransported()), 'ressources pillées')
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de pillage');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a pillé votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResourcesTransported()), 'ressources pillées')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTPLAYERWHITBATTLEFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage raté');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/view-memorial', $commander->getName())
					->addTxt(' est tombé lors du pillage de la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addTxt('Il a désormais rejoint le Mémorial. Que son âme traverse l\'Univers dans la paix.')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de combat');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a attaqué votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addTxt('Vous avez repoussé l\'ennemi avec succès.')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTPLAYERWHITOUTBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a pillé la planète non défendue ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResourcesTransported()), 'ressources pillées')
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de pillage');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a pillé votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('. Aucune flotte n\'était en position pour la défendre. ')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResourcesTransported()), 'ressources pillées')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::LOOTLOST:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Erreur de coordonnées');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' n\'a pas attaqué la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' car son joueur est de votre faction, sous la protection débutant ou un allié.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUEREMPTYSSUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Colonisation réussie');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a colonisé la planète rebelle située aux coordonnées ')  
					->addLnk('map/place-' . $this->id , Game::formatCoord($this->xSystem, $this->ySystem, $this->position, $this->rSector) . '.')
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addTxt('Votre empire s\'étend, administrez votre ')
					->addLnk('bases/base-' . $this->id, 'nouvelle planète')
					->addTxt('.')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUEREMPTYFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Colonisation ratée');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/view-memorial', $commander->getName())
					->addTxt(' est tombé lors de l\'attaque de la planète rebelle située aux coordonnées ')
					->addLnk('map/place-' . $this->id, Game::formatCoord($this->xSystem, $this->ySystem, $this->position, $this->rSector))
					->addTxt('.')
					->addSep()
					->addTxt('Il a désormais rejoint le Mémorial. Que son âme traverse l\'Univers dans la paix.')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUERPLAYERWHITOUTBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Conquête réussie');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a conquis la planète non défendue ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addTxt('Elle est désormais votre, vous pouvez l\'administrer ')
					->addLnk('bases/base-' . $this->id, 'ici')
					->addTxt('.')
					->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Planète conquise');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a conquis votre planète non défendue ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addTxt('Impliquez votre faction dans une action punitive envers votre assaillant.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUERLOST:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Erreur de coordonnées');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' n\'a pas attaqué la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' car le joueur est dans votre faction, sous la protection débutant ou votre allié.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::COMEBACK:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Rapport de retour');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' est de retour sur votre base ')
					->addLnk('map/place-' . $commander->getRBase(), $commander->getBaseName())
					->addTxt(' et rapporte ')
					->addStg(Format::number($commander->getResourcesTransported()))
					->addTxt(' ressources à vos entrepôts.')
					->addEnd();
				ASM::$ntm->add($notif);
				break;
			
			default: break;
		}
	}

	private function sendNotifForConquest($case, $commander, $reports = array()) {
		$nbrBattle = count($reports);
		switch($case) {
			case self::CONQUERPLAYERWHITBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Conquête réussie');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a conquis la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addTxt($nbrBattle . Format::addPlural($nbrBattle, ' combats ont eu lieu.', ' seul combat a eu lieu'))
					->addSep()
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addSep()
					->addTxt('Elle est désormais vôtre, vous pouvez l\'administrer ')
					->addLnk('bases/base-' . $this->id, 'ici')
					->addTxt('.');
				for ($i = 0; $i < $nbrBattle; $i++) {
					$notif->addSep();
					$notif->addLnk('fleet/view-archive/report-' . $reports[$i], 'voir le ' . Format::ordinalNumber($i + 1) . ' rapport');
				}
				$notif->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Planète conquise');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a conquis votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addTxt($nbrBattle . Format::addPlural($nbrBattle, ' combats ont eu lieu.', ' seul combat a eu lieu'))
					->addSep()
					->addTxt('Impliquez votre faction dans une action punitive envers votre assaillant.');
				for ($i = 0; $i < $nbrBattle; $i++) {
					$notif->addSep();
					$notif->addLnk('fleet/view-archive/report-' . $reports[$i], 'voir le ' . Format::ordinalNumber($i + 1) . ' rapport');
				}
				$notif->addEnd();
				ASM::$ntm->add($notif);
				break;
			case self::CONQUERPLAYERWHITBATTLEFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Conquête ratée');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/view-memorial/', $commander->getName())
					->addTxt(' est tombé lors de la tentive de conquête de la planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $this->rPlayer, $this->playerName)
					->addTxt('.')
					->addSep()
					->addTxt($nbrBattle . Format::addPlural($nbrBattle, ' combats ont eu lieu.', ' seul combat a eu lieu'))
					->addSep()
					->addTxt('Il a désormais rejoint de Mémorial. Que son âme traverse l\'Univers dans la paix.');
				for ($i = 0; $i < $nbrBattle; $i++) {
					$notif->addSep();
					$notif->addLnk('fleet/view-archive/report-' . $reports[$i], 'voir le ' . Format::ordinalNumber($i + 1) . ' rapport');
				}
				$notif->addEnd();
				ASM::$ntm->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($this->rPlayer);
				$notif->setTitle('Rapport de combat');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a tenté de conquérir votre planète ')
					->addLnk('map/place-' . $this->id, $this->baseName)
					->addTxt('.')
					->addSep()
					->addTxt($nbrBattle . Format::addPlural($nbrBattle, ' combats ont eu lieu.', ' seul combat a eu lieu'))
					->addSep()
					->addTxt('Vous avez repoussé l\'ennemi avec succès. Bravo !');
				for ($i = 0; $i < $nbrBattle; $i++) {
					$notif->addSep();
					$notif->addLnk('fleet/view-archive/report-' . $reports[$i], 'voir le ' . Format::ordinalNumber($i + 1) . ' rapport');
				}
				$notif->addEnd();
				ASM::$ntm->add($notif);
				break;

			default: break;
		}
	}

	public function createVirtualCommander() {
		$population = $this->population;
		$vCommander = new Commander();
		$vCommander->id = 'Null';
		$vCommander->rPlayer = ID_GAIA;
		$vCommander->name = 'rebelle';
		$vCommander->avatar = 't3-c4';
		$vCommander->sexe = 1;
		$vCommander->age = 42;
		$vCommander->statement = 1;
		$vCommander->level = ceil((((($this->maxDanger / (self::DANGERMAX / self::LEVELMAXVCOMMANDER))) * 9) + ($this->population / (self::POPMAX / self::LEVELMAXVCOMMANDER))) / 10);

		$nbrsquadron = ceil($vCommander->level * (($this->danger + 1) / ($this->maxDanger + 1)));

		$army = array();
		$squadronsIds = array();

		for ($i = 0; $i < $nbrsquadron; $i++) {
			$aleaNbr = ($this->coefHistory * $this->coefResources * $this->position * $i) % SquadronResource::size();
			$army[] = SquadronResource::get($vCommander->level, $aleaNbr);
			$squadronsIds[] = 0;
		}

		for ($i = $vCommander->level - 1; $i >= $nbrsquadron; $i--) {
			$army[$i] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, Utils::now());
			$squadronsIds[] = 0;
		}

		$vCommander->setSquadronsIds($squadronsIds);
		$vCommander->setArmyInBegin($army);
		$vCommander->setArmy();
		$vCommander->setPevInBegin();

		return $vCommander;
	}
}