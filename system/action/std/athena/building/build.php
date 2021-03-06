<?php

# building a building action

# int baseid 		id de la base orbitale
# int building 	 	id du bâtiment

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\DataAnalysis;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Zeus\Helper\TutorialHelper;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Athena\Model\BuildingQueue;
use Asylamba\Modules\Zeus\Model\PlayerBonus;

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = Utils::getHTTPData('baseid');
$building = Utils::getHTTPData('building');


if ($baseId !== FALSE AND $building !== FALSE AND in_array($baseId, $verif)) {
	if (OrbitalBaseResource::isABuilding($building)) {
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));

		if (ASM::$obm->size() > 0) {
			$ob = ASM::$obm->get();

			$S_BQM1 = ASM::$bqm->getCurrentSession();
			ASM::$bqm->newSession(ASM_UMODE);
			ASM::$bqm->load(array('rOrbitalBase' => $baseId), array('dEnd'));

			$currentLevel = call_user_func(array($ob, 'getReal' . ucfirst(OrbitalBaseResource::getBuildingInfo($building, 'name')) . 'Level'));
			$technos = new Technology(CTR::$data->get('playerId'));
			if (OrbitalBaseResource::haveRights($building, $currentLevel + 1, 'resource', $ob->getResourcesStorage())
				AND OrbitalBaseResource::haveRights(OrbitalBaseResource::GENERATOR, $ob->getLevelGenerator(), 'queue', ASM::$bqm->size()) 
				AND (OrbitalBaseResource::haveRights($building, $currentLevel + 1, 'buildingTree', $ob) === TRUE)
				AND OrbitalBaseResource::haveRights($building, $currentLevel + 1, 'techno', $technos)) {

				# tutorial
				if (CTR::$data->get('playerInfo')->get('stepDone') == FALSE) {
					switch (CTR::$data->get('playerInfo')->get('stepTutorial')) {
						case TutorialResource::GENERATOR_LEVEL_2:
							if ($building == OrbitalBaseResource::GENERATOR AND $currentLevel + 1 >= 2) {
								TutorialHelper::setStepDone();
							}
							break;
						case TutorialResource::REFINERY_LEVEL_3:
							if ($building == OrbitalBaseResource::REFINERY AND $currentLevel + 1 >= 3) {
								TutorialHelper::setStepDone();
							}
							break;
						case TutorialResource::STORAGE_LEVEL_3:
							if ($building == OrbitalBaseResource::STORAGE AND $currentLevel + 1 >= 3) {
								TutorialHelper::setStepDone();
							}
							break;
						case TutorialResource::DOCK1_LEVEL_1:
							if ($building == OrbitalBaseResource::DOCK1 AND $currentLevel + 1 >= 1) {
								TutorialHelper::setStepDone();
							}
							break;
						case TutorialResource::TECHNOSPHERE_LEVEL_1:
							if ($building == OrbitalBaseResource::TECHNOSPHERE AND $currentLevel + 1 >= 1) {
								TutorialHelper::setStepDone();
							}
							break;
						case TutorialResource::REFINERY_LEVEL_10:
							if ($building == OrbitalBaseResource::REFINERY AND $currentLevel + 1 >= 10) {
								TutorialHelper::setStepDone();
							}
							break;
						case TutorialResource::STORAGE_LEVEL_8:
							if ($building == OrbitalBaseResource::STORAGE AND $currentLevel + 1 >= 8) {
								TutorialHelper::setStepDone();
							}
							break;
						case TutorialResource::DOCK1_LEVEL_6:
							if ($building == OrbitalBaseResource::DOCK1 AND $currentLevel + 1 >= 6) {
								TutorialHelper::setStepDone();
							}
							break;
						case TutorialResource::REFINERY_LEVEL_16:
							if ($building == OrbitalBaseResource::REFINERY AND $currentLevel + 1 >= 16) {
								TutorialHelper::setStepDone();
							}
							break;
						case TutorialResource::STORAGE_LEVEL_12:
							if ($building == OrbitalBaseResource::STORAGE AND $currentLevel + 1 >= 12) {
								TutorialHelper::setStepDone();
							}
							break;
						case TutorialResource::TECHNOSPHERE_LEVEL_6:
							if ($building == OrbitalBaseResource::TECHNOSPHERE AND $currentLevel + 1 >= 6) {
								TutorialHelper::setStepDone();
							}
							break;
						case TutorialResource::DOCK1_LEVEL_15:
							if ($building == OrbitalBaseResource::DOCK1 AND $currentLevel + 1 >= 15) {
								TutorialHelper::setStepDone();
							}
							break;
						case TutorialResource::REFINERY_LEVEL_20:
							if ($building == OrbitalBaseResource::REFINERY AND $currentLevel + 1 >= 20) {
								TutorialHelper::setStepDone();
							}
							break;
					}
				}

				# build the new building
				$bq = new BuildingQueue();
				$bq->rOrbitalBase = $baseId;
				$bq->buildingNumber = $building;
				$bq->targetLevel = $currentLevel + 1;
				$time = OrbitalBaseResource::getBuildingInfo($building, 'level', $currentLevel + 1, 'time');
				$bonus = $time * CTR::$data->get('playerBonus')->get(PlayerBonus::GENERATOR_SPEED) / 100;
				if (ASM::$bqm->size() == 0) {
					$bq->dStart = Utils::now();
				} else {
					$bq->dStart = ASM::$bqm->get(ASM::$bqm->size() - 1)->dEnd;
				}
				$bq->dEnd = Utils::addSecondsToDate($bq->dStart, round($time - $bonus));
				ASM::$bqm->add($bq);

				# debit resources
				$ob->decreaseResources(OrbitalBaseResource::getBuildingInfo($building, 'level', $currentLevel + 1, 'resourcePrice'));

				if (DATA_ANALYSIS) {
					$db = Database::getInstance();
					$qr = $db->prepare('INSERT INTO 
						DA_BaseAction(`from`, type, opt1, opt2, weight, dAction)
						VALUES(?, ?, ?, ?, ?, ?)'
					);
					$qr->execute([CTR::$data->get('playerId'), 1, $building, $currentLevel + 1, DataAnalysis::resourceToStdUnit(OrbitalBaseResource::getBuildingInfo($building, 'level', $currentLevel + 1, 'resourcePrice')), Utils::now()]);
				}

				# add the event in controller
				CTR::$data->get('playerEvent')->add($bq->dEnd, EVENT_BASE, $baseId);

				CTR::$alert->add('Construction programmée', ALERT_STD_SUCCESS);
			} else {
				CTR::$alert->add('les conditions ne sont pas remplies pour construire ce bâtiment', ALERT_STD_ERROR);
			}
			ASM::$bqm->changeSession($S_BQM1);
		} else {
			CTR::$alert->add('cette base ne vous appartient pas', ALERT_STD_ERROR);
		}

		ASM::$obm->changeSession($S_OBM1);
	} else {
		CTR::$alert->add('le bâtiment indiqué n\'est pas valide', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour construire un bâtiment', ALERT_STD_FILLFORM);
}