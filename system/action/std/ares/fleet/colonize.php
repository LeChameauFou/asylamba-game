<?php

# send a fleet to loot a place

# int commanderid 			id du commandant à envoyer
# int placeid				id de la place attaquée

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Demeter\Model\Color;

$commanderId = Utils::getHTTPData('commanderid');
$placeId = Utils::getHTTPData('placeid');


if ($commanderId !== FALSE AND $placeId !== FALSE) {
	$S_COM1 = ASM::$com->getCurrentSession();
	ASM::$com->newSession(ASM_UMODE);
	ASM::$com->load(array('c.id' => $commanderId, 'c.rPlayer' => CTR::$data->get('playerId')));
	
	$S_PLM1 = ASM::$plm->getCurrentSession();
	ASM::$plm->newSession(ASM_UMODE);
	ASM::$plm->load(array('id' => $placeId));

	# load the technologies
	$technologies = new Technology(CTR::$data->get('playerId'));

	# check si technologie CONQUEST débloquée
	if ($technologies->getTechnology(Technology::COLONIZATION) == 1) {
		# check si la technologie BASE_QUANTITY a un niveau assez élevé
		$maxBasesQuantity = $technologies->getTechnology(Technology::BASE_QUANTITY) + 1;
		$obQuantity = CTR::$data->get('playerBase')->get('ob')->size();

		# count ob quantity via request to be sure (the session is sometimes not valid)
		$db = Database::getInstance();
		$qr = $db->prepare('SELECT COUNT(*) AS count FROM `orbitalBase` WHERE `rPlayer`=?'); 
		$qr->execute([CTR::$data->get('playerId')]);
		$aw = $qr->fetch();
		$obQuantity = $aw['count'];

		$coloQuantity = 0;
		$S_COM2 = ASM::$com->getCurrentSession();
		ASM::$com->newSession();
		ASM::$com->load(array('c.rPlayer' => CTR::$data->get('playerId'), 'c.statement' => Commander::MOVING));
		for ($i = 0; $i < ASM::$com->size(); $i++) { 
			if (ASM::$com->get($i)->travelType == Commander::COLO) {
				$coloQuantity++;
			}
		}
		ASM::$com->changeSession($S_COM2);
		$totalBases = $obQuantity + $coloQuantity;
		if ($totalBases < $maxBasesQuantity) {
			if (ASM::$com->size() > 0) {
				if (ASM::$plm->size() > 0) {
					$commander = ASM::$com->get();
					$place = ASM::$plm->get();

					if ($place->typeOfPlace == Place::TERRESTRIAL) {

						ASM::$plm->load(array('id' => $commander->getRBase()));
						$home = ASM::$plm->getById($commander->getRBase());

						$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
						$duration = Game::getTimeToTravel($home, $place, CTR::$data->get('playerBonus'));

						# compute price
						$price = $totalBases * CREDITCOEFFTOCOLONIZE;

						# calcul du bonus
						$_CLM46 = ASM::$clm->getCurrentSession();
						ASM::$clm->newSession();
						ASM::$clm->load(['id' => CTR::$data->get('playerInfo')->get('color')]);
						
						if (in_array(ColorResource::COLOPRICEBONUS, ASM::$clm->get()->bonus)) {
							$price -= round($price * ColorResource::BONUS_CARDAN_COLO / 100);
						}
						ASM::$clm->changeSession($_CLM46);

						if (CTR::$data->get('playerInfo')->get('credit') >= $price) {
							if ($commander->getPev() > 0) {
								if ($commander->statement == Commander::AFFECTED) {

									$S_SEM = ASM::$sem->getCurrentSession();
									ASM::$sem->newSession();
									ASM::$sem->load(array('id' => $place->rSector));

									$_CLM2 = ASM::$clm->getCurrentSession();
									ASM::$clm->newSession();
									ASM::$clm->load(array('id' => ASM::$sem->get()->rColor));
									
									$sectorColor = ASM::$clm->get();
									$isFactionSector = (ASM::$sem->get()->rColor == $commander->playerColor || $sectorColor->colorLink[CTR::$data->get('playerInfo')->get('color')] == Color::ALLY) ? TRUE : FALSE;
									
									ASM::$sem->changeSession($S_SEM);
									ASM::$clm->changeSession($_CLM2);

									if ($length <= Commander::DISTANCEMAX || $isFactionSector) {
										$commander->destinationPlaceName = $place->baseName;
										if ($commander->move($place->getId(), $commander->rBase, Commander::COLO, $length, $duration)) {
											# debit credit
											$S_PAM2 = ASM::$pam->getCurrentSession();
											ASM::$pam->newSession(ASM_UMODE);
											ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
											ASM::$pam->get()->decreaseCredit($price);
											ASM::$pam->changeSession($S_PAM2);

											if (CTR::$get->exist('redirect')) {
												CTR::redirect('map/place-' . CTR::$get->get('redirect'));
											}
										}
									} else {
										CTR::$alert->add('Cet emplacement est trop éloigné.', ALERT_STD_ERROR);	
									}
								} else {
									CTR::$alert->add('Cet officier est déjà en déplacement.', ALERT_STD_ERROR);	
								}
							} else {
								CTR::$alert->add('Vous devez affecter au moins un vaisseau à votre officier.', ALERT_STD_ERROR);	
							}
						} else {
							CTR::$alert->add('Vous n\'avez pas assez de crédits pour coloniser cette planète.', ALERT_STD_ERROR);
						}
					} else {
						CTR::$alert->add('Ce lieu n\'est pas habitable.', ALERT_STD_ERROR);
					}
				} else {
					CTR::$alert->add('Ce lieu n\'existe pas.', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('Ce commandant ne vous appartient pas ou n\'existe pas.', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('Vous avez assez de conquête en cours ou un niveau administration étendue trop bas.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Vous devez développer votre technologie colonisation.', ALERT_STD_ERROR);
	}
	ASM::$com->changeSession($S_COM1);
	ASM::$plm->changeSession($S_PLM1);
} else {
	CTR::$alert->add('Manque de précision sur le commandant ou la position.', ALERT_STD_ERROR);
}