<?php
# propose a commercial route action

# int basefrom 		id (rPlace) de la base orbitale qui propose la route
# int baseto 		id (rPlace) de la base orbitale à qui la route est proposée

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Model\CommercialRoute;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Resource\ColorResource;

for ($i=0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseFrom 	= Utils::getHTTPData('basefrom');
$baseTo 	= Utils::getHTTPData('baseto');

if ($baseFrom !== FALSE AND $baseTo !== FALSE AND in_array($baseFrom, $verif)) {
	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession();
	ASM::$obm->load(array('rPlace' => $baseFrom));

	$proposerBase = ASM::$obm->get();

#	if ($proposerBase->getLevelSpatioport() > 0) {}

	ASM::$obm->load(array('rPlace' => $baseTo));

	$otherBase = ASM::$obm->get(1);

	# check s'il y a une place de route libre
	$S_CRM1 = ASM::$crm->getCurrentSession();
	ASM::$crm->newSession();
	ASM::$crm->load(array('rOrbitalBase' => $proposerBase->getId())); # routes avec n'importe quel statement
	ASM::$crm->load(array('rOrbitalBaseLinked' => $proposerBase->getId(), 'statement' => array(CRM_ACTIVE, CRM_STANDBY)));

	$nbrMaxCommercialRoute = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'level', $proposerBase->getLevelSpatioport(), 'nbRoutesMax');
	
	# check si on n'a pas déjà une route avec ce joueur
	$alreadyARoute = FALSE;
	for ($i = 0; $i < ASM::$crm->size(); $i++) { 
		if (ASM::$crm->get($i)->getROrbitalBaseLinked() == $proposerBase->getRPlace()) {
			if (ASM::$crm->get($i)->getROrbitalBase() == $otherBase->getRPlace()) {
				$alreadyARoute = TRUE;
			}
		}
		if (ASM::$crm->get($i)->getROrbitalBase() == $proposerBase->getRPlace()) {
			if (ASM::$crm->get($i)->getROrbitalBaseLinked() == $otherBase->getRPlace()) {
				$alreadyARoute = TRUE;
			}
		}
	}

	if ((ASM::$crm->size() < $nbrMaxCommercialRoute) && (!$alreadyARoute) && ($proposerBase->getLevelSpatioport() > 0) && ($otherBase->getLevelSpatioport() > 0)) {
		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => $otherBase->getRPlayer()));

		$player = ASM::$pam->get();

		$S_CLM1 = ASM::$clm->getCurrentSession();
		ASM::$clm->newSession();
		ASM::$clm->load(array('id' => array(CTR::$data->get('playerInfo')->get('color'), $player->rColor)));

		if (ASM::$clm->size() == 2) {
			if (ASM::$clm->get(0)->id == CTR::$data->get('playerInfo')->get('color')) {
				$myColor = ASM::$clm->get(0);
				$otherColor = ASM::$clm->get(1);
			} else {
				$myColor = ASM::$clm->get(1);
				$otherColor = ASM::$clm->get(0);
			}
		} else {
			$myColor = ASM::$clm->get();
			$otherColor = ASM::$clm->get();
		}
		if ($myColor->colorLink[$player->rColor] != Color::ENEMY && $otherColor->colorLink[CTR::$data->get('playerInfo')->get('color')] != Color::ENEMY) {

			if (ASM::$obm->size() == 2 && ($proposerBase->getRPlayer() != $otherBase->getRPlayer()) && (ASM::$pam->size() == 1)) {
				$distance = Game::getDistance($proposerBase->getXSystem(), $otherBase->getXSystem(), $proposerBase->getYSystem(), $otherBase->getYSystem());
				$bonusA = ($proposerBase->getSector() != $otherBase->getSector()) ? CRM_ROUTEBONUSSECTOR : 1;
				$bonusB = (CTR::$data->get('playerInfo')->get('color')) != $player->getRColor() ? CRM_ROUTEBONUSCOLOR : 1;
				$price = Game::getRCPrice($distance);
				$income = Game::getRCIncome($distance, $bonusA, $bonusB);
				
				if ($distance == 1) {
					$imageLink = '1-' . rand(1, 3);
				} elseif ($distance < 26) {
					$imageLink = '2-' . rand(1, 3);
				} elseif ($distance < 126) {
					$imageLink = '3-' . rand(1, 3);
				} else {
					$imageLink = '4-' . rand(1, 3);
				}

				# compute bonus
				$_CLM23 = ASM::$clm->getCurrentSession();
				ASM::$clm->newSession();
				ASM::$clm->load(['id' => CTR::$data->get('playerInfo')->get('color')]);
				if (in_array(ColorResource::COMMERCIALROUTEPRICEBONUS, ASM::$clm->get()->bonus)) {
					$priceWithBonus = round($price - ($price * ColorResource::BONUS_NEGORA_ROUTE / 100));
				} else {
					$priceWithBonus = $price;
				}

				ASM::$clm->changeSession($_CLM23);

				if (CTR::$data->get('playerInfo')->get('credit') >= $priceWithBonus) {
					# création de la route
					$cr = new CommercialRoute();
					$cr->setROrbitalBase($proposerBase->getId());
					$cr->setROrbitalBaseLinked($otherBase->getId());
					$cr->setImageLink($imageLink);
					$cr->setDistance($distance);
					$cr->setPrice($price);
					$cr->setIncome($income);
					$cr->setDProposition(Utils::now());
					$cr->setDCreation(NULL);
					$cr->setStatement(0);
					ASM::$crm->add($cr);
					
					# débit des crédits au joueur
					$S_PAM2 = ASM::$pam->getCurrentSession();
					ASM::$pam->newSession(ASM_UMODE);
					ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
					ASM::$pam->get()->decreaseCredit($priceWithBonus);
					ASM::$pam->changeSession($S_PAM2);

					$n = new Notification();
					$n->setRPlayer($otherBase->getRPlayer());
					$n->setTitle('Proposition de route commerciale');
					$n->addBeg()->addLnk('embassy/player-' . CTR::$data->get('playerId'), CTR::$data->get('playerInfo')->get('name'));
					$n->addTxt(' vous propose une route commerciale liant ');
					$n->addLnk('map/place-' . $proposerBase->getRPlace(), $proposerBase->getName())->addTxt(' et ');
					$n->addLnk('map/base-' . $otherBase->getRPlace(), $otherBase->getName())->addTxt('.');
					$n->addSep()->addTxt('Les frais de l\'opération vous coûteraient ' . Format::numberFormat($priceWithBonus) . ' crédits; Les gains estimés pour cette route sont de ' . Format::numberFormat($income) . ' crédits par relève.');
					$n->addSep()->addLnk('action/a-switchbase/base-' . $otherBase->getRPlace() . '/page-spatioport', 'En savoir plus ?');
					$n->addEnd();
					ASM::$ntm->add($n);

					CTR::$alert->add('Route commerciale proposée', ALERT_STD_SUCCESS);
				} else {
					CTR::$alert->add('impossible de proposer une route commerciale - vous n\'avez pas assez de crédits', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('impossible de proposer une route commerciale (2)', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('impossible de proposer une route commerciale à ce joueur, vos factions sont en guerre.', ALERT_STD_ERROR);
		}
		ASM::$clm->changeSession($S_CLM1);
		ASM::$pam->changeSession($S_PAM1);
	} else {
		CTR::$alert->add('impossible de proposer une route commerciale (3)', ALERT_STD_ERROR);
	}
	ASM::$crm->changeSession($S_CRM1);
	ASM::$obm->changeSession($S_OBM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour proposer une route commerciale', ALERT_STD_FILLFORM);
}