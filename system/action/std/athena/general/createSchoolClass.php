<?php

# create school class action

# int baseid 		id de la base orbitale
# int school 		not used anymore
# string name 		name of the officer

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Zeus\Helper\CheckName;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Gaia\Resource\PlaceResource;
use Asylamba\Modules\Athena\Resource\SchoolClassResource;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Zeus\Helper\TutorialHelper;

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = Utils::getHTTPData('baseid');
$school = Utils::getHTTPData('school');
$name   = Utils::getHTTPData('name');

$cn = new CheckName();
$cn->maxLenght = 20;

if ($baseId !== FALSE AND $school !== FALSE AND $name !== FALSE AND in_array($baseId, $verif)) {
	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession();
	ASM::$obm->load(array('rPlace' => $baseId, 'rPlayer' => CTR::$data->get('playerId')));

	if (ASM::$obm->size() > 0) {
		$S_COM1 = ASM::$com->getCurrentSession();
		ASM::$com->newSession();
		ASM::$com->load(array('c.statement' => Commander::INSCHOOL, 'c.rBase' => $baseId));

		if (ASM::$com->size() < PlaceResource::get(ASM::$obm->get()->typeOfBase, 'school-size')) {
			ASM::$com->load(array('c.statement' => Commander::RESERVE, 'c.rBase' => $baseId));

			if (ASM::$com->size() < OrbitalBase::MAXCOMMANDERINMESS) {
				$school = intval($school);
				$nbrCommandersToCreate = rand(SchoolClassResource::getInfo($school, 'minSize'), SchoolClassResource::getInfo($school, 'maxSize'));

				if ($cn->checkLength($name) && $cn->checkChar($name)) {
					if (SchoolClassResource::getInfo($school, 'credit') <= CTR::$data->get('playerInfo')->get('credit')) {
						# tutorial
						if (CTR::$data->get('playerInfo')->get('stepDone') == FALSE) {
							switch (CTR::$data->get('playerInfo')->get('stepTutorial')) {
								case TutorialResource::CREATE_COMMANDER:
									TutorialHelper::setStepDone();
									break;
							}
						}

						# débit des crédits au joueur
						$S_PAM1 = ASM::$pam->getCurrentSession();
						ASM::$pam->newSession(ASM_UMODE);
						ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
						ASM::$pam->get()->decreaseCredit(SchoolClassResource::getInfo($school, 'credit'));
						ASM::$pam->changeSession($S_PAM1);

						for ($i = 0; $i < $nbrCommandersToCreate; $i++) {
							$newCommander = new Commander();
							$newCommander->upExperience(rand(SchoolClassResource::getInfo($school, 'minExp'), SchoolClassResource::getInfo($school, 'maxExp')));
							$newCommander->rPlayer = CTR::$data->get('playerId');
							$newCommander->rBase = $baseId;
							$newCommander->palmares = 0;
							$newCommander->statement = 0;
							$newCommander->name = $name;
							$newCommander->avatar = 't' . rand(1, 21) . '-c' . CTR::$data->get('playerInfo')->get('color');
							$newCommander->dCreation = Utils::now();
							$newCommander->uCommander = Utils::now();
							$newCommander->setSexe(1);
							$newCommander->setAge(rand(40, 70));

							ASM::$com->add($newCommander);
						}
						CTR::$alert->add($nbrCommandersToCreate . ' commandant' . Format::addPlural($nbrCommandersToCreate) . ' inscrit' . Format::addPlural($nbrCommandersToCreate) . ' au programme d\'entraînement.', ALERT_STD_SUCCESS);
					} else {
						CTR::$alert->add('vous n\'avez pas assez de crédit.', ALERT_STD_FILLFORM);
					}
				} else {
					CTR::$alert->add('le nom contient des caractères non autorisé ou trop de caractères.', ALERT_STD_FILLFORM);
				}
			} else {
				CTR::$alert->add('Vous ne pouvez pas créer de nouveaux officiers si vous en avez déjà ' . Orbitalbase::MAXCOMMANDERINMESS . ' ou plus.', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('Trop d\'officiers en formation. Déplacez des officiers dans le mess pour libérer de la place.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('cette base ne vous appartient pas', ALERT_STD_ERROR);
	}
	ASM::$obm->changeSession($S_OBM1);
}