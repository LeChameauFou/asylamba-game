<?php
# dock1 component
# in athena.bases package

# affichage du chantier alpha

# require
	# {orbitalBase}		ob_dock1

# work

use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Chronos;
use Asylamba\Modules\Zeus\Model\PlayerBonus;

$S_SQM1 = ASM::$sqm->getCurrentSession();
ASM::$sqm->changeSession($ob_dock1->dock1Manager);
$s = array('', '', '', '', '', '');
$technology = new Technology(CTR::$data->get('playerId'));

#place dans le hangar
$totalSpace = OrbitalBaseResource::getBuildingInfo(2, 'level', $ob_dock1->getLevelDock1(), 'storageSpace');
$storage = $ob_dock1->getShipStorage();
$inStorage = 0;

for ($m = 0; $m < 6; $m++) {
	$inStorage += ShipResource::getInfo($m, 'pev') * $storage[$m];
}

$inQueue = 0;

if (ASM::$sqm->size() > 0) {
	for ($j = 0; $j < ASM::$sqm->size(); $j++) {
		$inQueue += ShipResource::getInfo(ASM::$sqm->get($j)->shipNumber, 'pev') * ASM::$sqm->get($j)->quantity;
	}
}

for ($i = 0; $i < 6; $i++) {
	# calcul du nombre de vaisseaux max
	$maxShipResource = floor($ob_dock1->getResourcesStorage() / ShipResource::getInfo($i, 'resourcePrice'));
	$maxShipResource = ($maxShipResource < 100) ? $maxShipResource : 99;
	$maxShipPev = $totalSpace - $inStorage - $inQueue;
	$maxShipPev = floor($maxShipPev /ShipResource::getInfo($i, 'pev'));
	$maxShipPev = ($maxShipPev < 100) ? $maxShipPev : 99;
	$maxShip    = ($maxShipResource <= $maxShipPev) ? $maxShipResource : $maxShipPev;

	# display
	$name = ShipResource::getInfo($i, 'codeName');
	$picto = MEDIA . 'ship/picto/' . ShipResource::getInfo($i, 'imageLink') . '.png';
	$disability = 'disable';

	if (($answer = ShipResource::haveRights($i, 'techno', $technology)) !== TRUE) {
		# technology
		$but  = '<span class="button disable">';
			$but .= $answer;
		$but .= '</span>';
	} elseif (!ShipResource::haveRights($i, 'shipTree', $ob_dock1)) {
		# ship tree
		$but  = '<span class="button disable">';
			$but .= 'il vous faut augmenter votre chantier alpha au niveau ' .  ShipResource::dockLevelNeededFor($i);
		$but .= '</span>';
	} else {
		# usable ship
		$disability = '';

		if (!ShipResource::haveRights($i, 'queue', $ob_dock1, ASM::$sqm->size())) {
			# queue size
			$but = '<span class="button disable">';
				$but .= 'file de construction pleine<br />';
				$but .= '<span class="final-cost">' . Format::numberFormat(ShipResource::getInfo($i, 'resourcePrice')) . '</span> ';
				$but .= '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png"> et ';
				$but .= '<span class="final-time">' . Chronos::secondToFormat(ShipResource::getInfo($i, 'time'), 'lite') . '</span> ';
				$but .= '<img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
			$but .= '</span>';
		} elseif ($maxShip < 1) {
			$but = '<span class="button disable">';
				$but .= 'pas assez de ressources / hangar plein<br />';
				$but .= '<span class="final-cost">' . Format::numberFormat(ShipResource::getInfo($i, 'resourcePrice')) . '</span> ';
				$but .= '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png"> et ';
				$but .= '<span class="final-time">' . Chronos::secondToFormat(ShipResource::getInfo($i, 'time'), 'lite') . '</span> ';
				$but .= '<img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
			$but .= '</span>';
		} else {
			$but  = '<input class="ship-pack" type="text" maxlength="2" name="quantity" value="' . $maxShip . '" data-max-ship="' . $maxShip . '" />';

			$but .= '<a href="' . Format::actionBuilder('buildship', ['baseid' => $ob_dock1->getId(), 'ship' => $i, 'quantity' => $maxShip]) . '" class="button">';
				$but .= 'construire <span class="final-number">' . $maxShip . '</span> ' . ShipResource::getInfo($i, 'codeName') . ' pour<br />';
				$but .= '<span class="final-cost">' . Format::numberFormat(ShipResource::getInfo($i, 'resourcePrice') * $maxShip) . '</span> ';
				$but .= '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png"> et ';
				$but .= '<span class="final-time">' . Chronos::secondToFormat(ShipResource::getInfo($i, 'time') * $maxShip, 'lite') . '</span> ';
				$but .= '<img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
			$but .= '</a>';
		}
	}

	$s[$i] .= '<div class="build-item ' . $disability . ' dynamic-ship-box" data-ship-second="' . ShipResource::getInfo($i, 'time') . '" 
		data-ship-cost="' . ShipResource::getInfo($i, 'resourcePrice') . '" data-maxship="' . $maxShip . '">';
		$s[$i] .= '<div class="name">';
			$s[$i] .= '<img src="' . $picto . '" alt="" />';
			$s[$i] .= '<strong>' . $name . '</strong>';
			$s[$i] .= '<em>' . ShipResource::getInfo($i, 'name') . '</em>'; 
			$s[$i] .= '<a href="#" class="addInfoPanel info hb lt" title="plus d\'info" data-ship-id="' . $i . '" data-info-type="ship">+</a>';
		$s[$i] .= '</div>';
		$s[$i] .= '<div class="ship-illu"><img src="' . MEDIA . 'ship/img/' . Format::paddingNumber($i + 1, 2) . '-' . Format::paddingNumber(CTR::$data->get('playerInfo')->get('color'), 2) . '.png" alt="" /></div>';
		$s[$i] .= $but;
	$s[$i] .= '</div>';
}

echo '<div class="component">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/dock1.png" alt="" />';
		echo '<h2>' . OrbitalBaseResource::getBuildingInfo(2, 'frenchName') . '</h2>';
		echo '<em>niveau ' . $ob_dock1->getLevelDock1() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box ' . ((CTR::$data->get('playerBonus')->get(PlayerBonus::DOCK1_SPEED) == 0) ? 'grey' : '') . '">';
				echo '<span class="label">bonus de vitesse de production</span>';
				echo '<span class="value">';
					echo Format::numberFormat(CTR::$data->get('playerBonus')->get(PlayerBonus::DOCK1_SPEED)) . ' %';
				echo '</span>';
			echo '</div>';

			echo '<h4>File de construction</h4>';
			echo '<div class="queue">';
				$realSizeQueue = 0;

				for ($i = 0; $i < OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::DOCK1, 'level', $ob_dock1->levelDock1, 'nbQueues'); $i++) {
					if (ASM::$sqm->get($i) !== FALSE) {
						$queue = ASM::$sqm->get($i);
						$realSizeQueue++;
						$totalTimeShips = $queue->quantity * ShipResource::getInfo($queue->shipNumber, 'time');
						$remainingTime = Utils::interval(Utils::now(), $queue->dEnd, 's');

						echo $realSizeQueue > 1
							? '<div class="item">'
							: '<div class="item active progress" data-progress-output="lite" data-progress-current-time="' . $remainingTime . '" data-progress-total-time="' . $totalTimeShips . '">';
						echo '<a href="' . Format::actionBuilder('dequeueship', ['baseid' => $ob_dock1->getId(), 'dock' => '1', 'queue' => $queue->id]) . '"' . 
							'class="button hb lt" title="annuler la commande (attention, vous ne récupérerez que ' . SQM_RESOURCERETURN * 100 . '% du montant investi)">×</a>';
						echo  '<img class="picto" src="' . MEDIA . 'ship/picto/' . ShipResource::getInfo($queue->shipNumber, 'imageLink') . '.png" alt="" />';
						echo '<strong>' . $queue->quantity . ' ' . ShipResource::getInfo($queue->shipNumber, 'codeName') . Format::addPlural($queue->quantity) . '</strong>';
						
						if ($realSizeQueue > 1) {
							echo '<em><span class="progress-text">' . Chronos::secondToFormat($remainingTime, 'lite') . '</span></em>';
							echo '<span class="progress-container"></span>';
						} else {
							echo '<em><span class="progress-text">' . Chronos::secondToFormat($remainingTime, 'lite') . '</span></em>';
							echo '<span class="progress-container">';
								echo '<span style="width: ' . Format::percent($totalTimeShips - $remainingTime, $totalTimeShips) . '%;" class="progress-bar">';
								echo '</span>';
							echo '</span>';
						}
						echo '</div>';
					} else {
						echo '<div class="item empty">';
							echo '<span class="picto"></span>';
							echo '<strong>Emplacement libre</strong>';
							echo '<span class="progress-container"></span>';
						echo '</div>';
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component dock1">';
	echo '<div class="head skin-5">';
		echo '<h2>Chasseurs</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo $s[0];
			echo $s[1];
			echo $s[2];
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component dock1">';
	echo '<div class="head skin-5">';
		echo '<h2>Corvettes</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo $s[3];
			echo $s[4];
			echo $s[5];
		echo '</div>';
	echo '</div>';
echo '</div>';


echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Hangars</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="' . APP_ROOT . 'fleet/view-movement">intégrer à vos armées</a></span>';
				echo '<span><a href="#" class="sh hb lt" title="information" data-target="info-dock1">?</a></span>';
			echo '</div>';

			echo '<p class="info" id="info-dock1" style="display:none;">Le hangar de votre chantier vous sert à stocker des vaisseaux. Les vaisseaux 
			sont stockés directement après leur construction. Si vous ne disposez pas suffisamment de place, vous ne pourrez plus en construire. Faites 
			donc bien attention de vider votre hangar régulièrement.<br />
			Chaque vaisseau prend une place différente dans votre hangar. Cette place correspond au point équivalent vaisseau (PEV). Le hangar dispose 
			d’un certain nombre de PEV qui augmente en fonction du niveau de votre chantier.<br />
			Le bouton intégration vous renverra à l’amirauté, ce qui vous permettra de vider votre hangar et de répartir vos vaisseaux dans vos flottes 
			en orbite autour de la planète sur laquelle vous avez construit votre chantier.</p>';

			echo '<div class="component market-sell">';
				for ($i = 0; $i < 6; $i++) {
					if ($storage[$i] !== 0) {
						echo '<div class="queue sh" data-target="sell-ships-' . $i . '">';
							echo '<div class="item">';
								echo '<img class="picto" src="' . MEDIA . 'ship/picto/' . ShipResource::getInfo($i, 'imageLink') . '.png" alt="" />';
								echo '<strong><span class="big">' . $storage[$i] . '</span> ' . ShipResource::getInfo($i, 'codeName') . Format::addPlural($storage[$i]) . '</strong>';
								echo '<em>' . ($storage[$i] * ShipResource::getInfo($i, 'pev')) . ' PEV</em>';
							echo '</div>';
						echo '</div>';
					}
					
					echo '<form id="sell-ships-' . $i . 
					'" class="sell-form"
					" data-max-quantity="' . $storage[$i] .
					'" data-min-price="' . (ShipResource::getInfo($i, 'resourcePrice') / 2) . 
					'" action="' . Format::actionBuilder('recycleship', 
						['baseid' => $ob_dock1->getId(), 'typeofship' => $i]) . 
					'" method="post" style="display:none;">';
						
						echo '<h4>recycler des vaisseaux</h4>';
						echo '<hr />';
						echo '<div class="label-box sf-quantity">';
							echo '<label for="sell-market-quantity-ship" class="label">Quantité</label>';
							echo '<input id="sell-market-quantity-ship" class="value" type="text" name="quantity" autocomplete="off" />';
						echo '</div>';

						echo '<div class="label-box sf-min-price">';
							echo '<span class="label">Ressources</span>';
							echo '<span class="value"></span>';
							echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/resource.png">';
						echo '</div>';

						echo '<hr />';
						echo '<p><input type="submit" value="Recycler" /></p>';
					echo '</form>';
				}
				echo '<div class="number-box">';	
					echo '<span class="label">capacité du hangar</span>';
					echo '<span class="value">';
						echo $inStorage . ' <img class="icon-color" alt="" src="' . MEDIA . 'resources/pev.png">';
						echo ' / ';
						echo $totalSpace . ' <img class="icon-color" alt="" src="' . MEDIA . 'resources/pev.png">';
					echo '</span>';
					$percent = Format::numberFormat($inStorage / $totalSpace * 100);
					$percent = $percent > 100 ? 100 : $percent;
					echo '<span class="progress-bar hb bl" title="remplissage : ' . $percent . '%">';
						echo '<span style="width:' . $percent . '%;" class="content"></span>';
					echo '</span>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>À propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . OrbitalBaseResource::getBuildingInfo(2, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$sqm->changeSession($S_SQM1);
