<?php
# galaxy loading
include_once GAIA;
$gc = new GalaxyManager();
$sm = new SectorManager();
$sm->load();

# bases loading
include_once ATHENA;
$S_OBM_MAP = ASM::$obm->getCurrentSession();
ASM::$obm->newSession();
ASM::$obm->load(array('rPlayer' => CTR::$data->get('playerId')));

# base choice
# si base donnée en argument
if (CTR::$get->exist('base')) {
	if (CTRHelper::baseExist(CTR::$get->get('base'))) {
		$defaultBase = ASM::$obm->getById(CTR::$get->get('base'));
		CTR::$data->get('playerParams')->add('base', CTR::$get->get('base'));
	} else {
		CTR::redirect('404');
	}
# si paramètre de base initialisé
} elseif (CTR::$data->get('playerParams')->exist('base')) {
	$defaultBase = ASM::$obm->getById(CTR::$data->get('playerParams')->get('base'));
# sinon base par défaut
} else {
	$defaultBase = ASM::$obm->get(0);
}

# map default position
$x = $defaultBase->getXSystem();
$y = $defaultBase->getYSystem();
$systemId = 0;

# other default location
# par place
if (CTR::$get->exist('place')) {
	$S_PLM_MAP = ASM::$plm->getCurrentSession();
	ASM::$plm->newSession();
	ASM::$plm->load(array('id' => CTR::$get->get('place')));

	if (ASM::$plm->size() == 1) {
		$x = ASM::$plm->get(0)->getXSystem();
		$y = ASM::$plm->get(0)->getYSystem();
		$systemId = ASM::$plm->get(0)->getRSystem();
	}
	
	ASM::$plm->changeSession($S_PLM_MAP);
# par system
} elseif (CTR::$get->exist('system')) {
	$_SYS_MAP = ASM::$sys->getCurrentSession();
	ASM::$sys->newSession();
	ASM::$sys->load(array('id' => CTR::$get->get('system')));

	if (ASM::$sys->size() == 1) {
		$x = ASM::$sys->get(0)->xPosition;
		$y = ASM::$sys->get(0)->yPosition;
		$systemId = CTR::$get->get('system');
	}
	
	ASM::$sys->changeSession($_SYS_MAP);
# par coordonnée
} elseif (CTR::$get->exist('x') && CTR::$get->exist('y')) {
	$x = CTR::$get->get('x');
	$y = CTR::$get->get('y');
}

# control include
include 'mapElement/subnav.php';
include 'mapElement/option.php';
include 'mapElement/nav.php';
include 'mapElement/content.php';
include 'mapElement/movers.php';
include 'mapElement/coordbox.php';

if ($systemId != 0) {
	$S_SYS1 = ASM::$sys->getCurrentSession();
	ASM::$sys->newSession();
	ASM::$sys->load(array('id' => $systemId));

	if (ASM::$sys->size() == 1) {
		# objet système
		$system = ASM::$sys->get();

		# objet place
		$places = array();
		$S_PLM1 = ASM::$plm->getCurrentSession();
		ASM::$plm->newSession();
		ASM::$plm->load(array('rSystem' => $systemId), array('position'));
		for ($i = 0; $i < ASM::$plm->size(); $i++) {
			$places[] = ASM::$plm->get($i);
		}
		ASM::$plm->changeSession($S_PLM1);

		# inclusion du "composant"
		echo '<div id="action-box" style="bottom: 0px;">';
			include PAGES . 'desktop/mapElement/actionbox.php';
		echo '</div>';
	}
	ASM::$sys->changeSession($S_SYS1);
} elseif (CTR::$get->exist('view') && CTR::$get->get('view') == 'ranking') {
	# inclusion du composant
	if (CTR::$get->exist('mode') && in_array(CTR::$get->get('mode'), array('general', 'victory', 'defeat', 'faction'))) {
		$mode = CTR::$get->get('mode');
	} else {
		$mode = 'general';
	}

	echo '<div id="action-box" style="bottom: 0px;">';
		include PAGES . 'desktop/mapElement/rankingbox.php';
	echo '</div>';
} else {
	echo '<div id="action-box"></div>';
}

# map sytems
echo '<div id="map" ';
	echo 'data-begin-x-position="' . $x . '" ';
	echo 'data-begin-y-position="' . $y . '" ';
	echo 'data-related-place="' . $defaultBase->getId() . '"';
echo '>';
	include 'mapElement/layer/background.php';

	include 'mapElement/layer/sectors.php';

	include 'mapElement/layer/spying.php';
	include 'mapElement/layer/ownbase.php';
	include 'mapElement/layer/commercialroutes.php';
	include 'mapElement/layer/fleetmovements.php';
	include 'mapElement/layer/attacks.php';

	include 'mapElement/layer/systems.php';
echo '</div>';

ASM::$obm->changeSession($S_OBM_MAP);
?>