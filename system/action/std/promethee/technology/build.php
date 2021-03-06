<?php
# building a technology action

# int baseid 		id de la base orbitale
# int techno 	 	id de la technologie

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Modules\Promethee\Model\TechnologyQueue;
use Asylamba\Modules\Promethee\Resource\TechnologyResource;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Zeus\Helper\TutorialHelper;
use Asylamba\Modules\Demeter\Resource\ColorResource;

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = Utils::getHTTPData('baseid');
$techno = Utils::getHTTPData('techno');


if ($baseId !== FALSE AND $techno !== FALSE AND in_array($baseId, $verif)) {
	if (TechnologyResource::isATechnology($techno) && !TechnologyResource::isATechnologyNotDisplayed($techno)) {
		
		$S_TQM1 = ASM::$tqm->getCurrentSession();
		ASM::$tqm->newSession(ASM_UMODE);
		ASM::$tqm->load(array('rPlayer' => CTR::$data->get('playerId'), 'technology' => $techno));

		if (ASM::$tqm->size() == 0) {

			$technos = new Technology(CTR::$data->get('playerId'));
			$targetLevel = $technos->getTechnology($techno) + 1;
			ASM::$tqm->newSession(ASM_UMODE);
			ASM::$tqm->load(array('rPlace' => $baseId), array('dEnd'));
			for ($i = 0; $i < ASM::$tqm->size(); $i++) { 
				if (ASM::$tqm->get($i)->technology == $techno) {
					$targetLevel++;
				}
			}

			$S_OBM1 = ASM::$obm->getCurrentSession();
			ASM::$obm->newSession(ASM_UMODE);
			ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));

			if (ASM::$obm->size() > 0) {
				$ob = ASM::$obm->get();

				$S_RSM1 = ASM::$rsm->getCurrentSession();
				ASM::$rsm->newSession(ASM_UMODE);
				ASM::$rsm->load(array('rPlayer' => CTR::$data->get('playerId')));

				if (TechnologyResource::haveRights($techno, 'resource', $targetLevel, $ob->getResourcesStorage())
					AND TechnologyResource::haveRights($techno, 'credit', $targetLevel, CTR::$data->get('playerInfo')->get('credit'))
					AND TechnologyResource::haveRights($techno, 'queue', $ob, ASM::$tqm->size())
					AND TechnologyResource::haveRights($techno, 'levelPermit', $targetLevel)
					AND TechnologyResource::haveRights($techno, 'technosphereLevel', $ob->getLevelTechnosphere())
					AND (TechnologyResource::haveRights($techno, 'research', $targetLevel, ASM::$rsm->get()->getResearchList()) === TRUE)
					AND TechnologyResource::haveRights($techno, 'maxLevel', $targetLevel)
					AND TechnologyResource::haveRights($techno, 'baseType', $ob->typeOfBase)) {

					# tutorial
					if (CTR::$data->get('playerInfo')->get('stepDone') == FALSE) {
						switch (CTR::$data->get('playerInfo')->get('stepTutorial')) {
							case TutorialResource::SHIP0_UNBLOCK:
								if ($techno == Technology::SHIP0_UNBLOCK) {
									TutorialHelper::setStepDone();
								}
								break;
							case TutorialResource::SHIP1_UNBLOCK:
								if ($techno == Technology::SHIP1_UNBLOCK) {
									TutorialHelper::setStepDone();
								}
								break;
						}
					}

					// load du joueur
					$S_PAM1 = ASM::$pam->getCurrentSession();
					ASM::$pam->newSession(ASM_UMODE);
					ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

					// construit la nouvelle techno
					$tq = new TechnologyQueue();
					$tq->rPlayer = CTR::$data->get('playerId');
					$tq->rPlace = $baseId;
					$tq->technology = $techno;
					$tq->targetLevel = $targetLevel;
					$time = TechnologyResource::getInfo($techno, 'time', $targetLevel);
					$bonusPercent = CTR::$data->get('playerBonus')->get(PlayerBonus::TECHNOSPHERE_SPEED);
					if (CTR::$data->get('playerInfo')->get('color') == ColorResource::APHERA) {
						# bonus if the player is from Aphera
						$bonusPercent += ColorResource::BONUS_APHERA_TECHNO;
					}

					# ajout du bonus du lieu
					$bonusPercent += Game::getImprovementFromScientificCoef($ob->planetHistory);
					
					$bonus = round($time * $bonusPercent / 100);
					if (ASM::$tqm->size() == 0) {
						$tq->dStart = Utils::now();
					} else {
						$tq->dStart = ASM::$tqm->get(ASM::$tqm->size() - 1)->dEnd;
					}
					$tq->dEnd = Utils::addSecondsToDate($tq->dStart, round($time - $bonus));
					ASM::$tqm->add($tq);

					// débit resources
					$ob->decreaseResources(TechnologyResource::getInfo($techno, 'resource', $targetLevel));
					
					// débit des crédits
					ASM::$pam->get()->decreaseCredit(TechnologyResource::getInfo($techno, 'credit', $targetLevel));
					
					// ajout de l'event dans le contrôleur
					CTR::$data->get('playerEvent')->add($tq->dEnd, EVENT_BASE, $baseId);

					if (DATA_ANALYSIS) {
						$db = Database::getInstance();
						$qr = $db->prepare('INSERT INTO 
							DA_BaseAction(`from`, type, opt1, opt2, weight, dAction)
							VALUES(?, ?, ?, ?, ?, ?)'
						);
						$qr->execute([CTR::$data->get('playerId'), 2, $techno, $targetLevel, (DataAnalysis::resourceToStdUnit(TechnologyResource::getInfo($techno, 'resource', $targetLevel)) + DataAnalysis::creditToStdUnit(TechnologyResource::getInfo($techno, 'credit', $targetLevel))), Utils::now()]);
					}

					// alerte
					CTR::$alert->add('Développement de la technologie programmée', ALERT_STD_SUCCESS);
					ASM::$pam->changeSession($S_PAM1);
				} else {
					CTR::$alert->add('les conditions ne sont pas remplies pour développer une technologie', ALERT_STD_ERROR);
				}
				ASM::$rsm->changeSession($S_RSM1);
			} else {
				CTR::$alert->add('cette base ne vous appartient pas', ALERT_STD_ERROR);	
			}
			ASM::$obm->changeSession($S_OBM1);
		} else {
			CTR::$alert->add('Cette technologie est déjà en construction', ALERT_STD_ERROR);
		}
		ASM::$tqm->changeSession($S_TQM1);
	} else {
		CTR::$alert->add('la technologie indiquée n\'est pas valide', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour développer une technologie', ALERT_STD_FILLFORM);
}
