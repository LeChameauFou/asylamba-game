<?php
# generator component
# in athena.bases package

# affichage du générateur

# require
	# {orbitalBase}		ob_generator

# work

use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Chronos;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;

$q = '';
$b = array('', '', '', '', '', '', '', '', '', '');
$realSizeQueue = 0;

for ($i = 0; $i < OrbitalBaseResource::BUILDING_QUANTITY; $i++) {
	$name 		= ucfirst(OrbitalBaseResource::getBuildingInfo($i, 'name'));
	$aLevel[$i] = intval(call_user_func(array($ob_generator, 'getLevel' . $name)));
	$rLevel[$i] = intval(call_user_func(array($ob_generator, 'getReal' . $name . 'Level')));
}

# queue
$S_BQM1 = ASM::$bqm->getCurrentSession();
ASM::$bqm->changeSession($ob_generator->buildingManager);

$q .= '<div class="queue">';
$nextTime = 0;
$nextTotalTime = 0;

for ($i = 0; $i < OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::GENERATOR, 'level', $ob_generator->levelGenerator, 'nbQueues'); $i++) {
	if (ASM::$bqm->get($i) !== FALSE) {
		$qe = ASM::$bqm->get($i);

		$realSizeQueue++;
		$nextTime = Utils::interval(Utils::now(), $qe->dEnd, 's');
		$nextTotalTime += OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'level', $qe->targetLevel, 'time');

		$q .= '<div class="item ' . (($realSizeQueue > 1) ? 'active' : '') . ' progress" data-progress-output="lite" data-progress-current-time="' . $nextTime . '" data-progress-total-time="' . $nextTotalTime . '">';
		$q .= '<a href="' . Format::actionBuilder('dequeuebuilding', ['baseid' => $ob_generator->getId(), 'building' => $qe->buildingNumber]) . '"' . 
				'class="button hb lt" title="annuler la construction (attention, vous ne récupérerez que ' . BQM_RESOURCERETURN * 100 . '% du montant investi)">×</a>';
		$q .= '<img class="picto" src="' . MEDIA . 'orbitalbase/' . OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'imageLink') . '.png" alt="" />';
		$q .= '<strong>';
			$q .= OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'frenchName');
			$q .= ' <span class="level">niv. ' . $qe->targetLevel . '</span>';
		$q .= '</strong>';
		if ($realSizeQueue > 1) {
			$q .= '<em><span class="progress-text">' . Chronos::secondToFormat($nextTime, 'lite') . '</span></em>';
			$q .= '<span class="progress-container"></span>';
		} else {
			$q .= '<em><span class="progress-text">' . Chronos::secondToFormat($nextTime, 'lite') . '</span></em>';

			$q .= '<span class="progress-container">';
				$q .= '<span style="width: ' . Format::percent($nextTotalTime - $nextTime, $nextTotalTime) . '%;" class="progress-bar">';
				$q .='</span>';
			$q .= '</span>';
		}
		$q .= '</div>';
	} else {
		$q .= '<div class="item empty">';
			$q .= '<span class="picto"></span>';
			$q .= '<strong>Emplacement libre</strong>';
			$q .= '<span class="progress-container"></span>';
		$q .= '</div>';
	}
}
$q .= '</div>';

ASM::$bqm->changeSession($S_BQM1);


# building
$technology = new Technology(CTR::$data->get('playerId'));
for ($i = 0; $i < OrbitalBaseResource::BUILDING_QUANTITY; $i++) {
	$level = $aLevel[$i];
	$nextLevel =  $rLevel[$i] + 1;

	$b[$i] .= ($rLevel[$i]) ? '<div class="build-item">' : '<div class="build-item disable">';
		$b[$i] .= '<div class="name">';
			$b[$i] .= '<img src="' . MEDIA . 'orbitalbase/' . OrbitalBaseResource::getBuildingInfo($i, 'imageLink') . '.png" alt="" />';
			$b[$i] .= '<strong>' . OrbitalBaseResource::getBuildingInfo($i, 'frenchName') . '</strong>';
			if ($level != 0) {
				$b[$i] .= '<span class="level hb lt" title="niveau actuel">' . $level . '</span>';
			}
			$b[$i] .= '<a href="#" class="addInfoPanel info hb lt" title="plus d\'info" data-building-id="' . $i . '" data-info-type="building" data-building-current-level="' . $level . '">+</a>';
		$b[$i] .= '</div>';

		$price = Format::numberFormat(OrbitalBaseResource::getBuildingInfo($i, 'level', ($nextLevel), 'resourcePrice'), -1) . ' <img src="' .  MEDIA. 'resources/resource.png" alt="ressources" class="icon-color" />';
		$time  = Chronos::secondToFormat(OrbitalBaseResource::getBuildingInfo($i, 'level', ($nextLevel), 'time'), 'lite') . ' <img src="' .  MEDIA. 'resources/time.png" alt="relèves" class="icon-color" />';

		if (($answer = OrbitalBaseResource::haveRights($i, $nextLevel, 'buildingTree', $ob_generator)) !== TRUE) {
			if ($answer == 'niveau maximum atteint') {
				$b[$i] .= '<span class="button disable">';
					$b[$i] .= '<span class="text">';
						$b[$i] .= 'construction impossible<br/>';
						$b[$i] .= 'niveau maximum atteint';
					$b[$i] .= '</span>';
				$b[$i] .= '</span>';
			} else {
				$b[$i] .= '<span class="button disable hb lt" title="' . $answer . '">';
					$b[$i] .= '<span class="text">';
						$b[$i] .= 'construction impossible<br/>';
						$b[$i] .= $price . ' | ' . $time;
					$b[$i] .= '</span>';
				$b[$i] .= '</span>';
			}
		} elseif (($answer = OrbitalBaseResource::haveRights($i, $nextLevel, 'techno', $technology)) !== TRUE) {
			$b[$i] .= '<span class="button disable hb lt" title="' . $answer . '">';
				$b[$i] .= '<span class="text">';
					$b[$i] .= 'construction impossible<br/>';
					$b[$i] .= $price . ' | ' . $time;
				$b[$i] .= '</span>';
			$b[$i] .= '</span>';
		} elseif (!OrbitalBaseResource::haveRights(OrbitalBaseResource::GENERATOR, $aLevel[OrbitalBaseResource::GENERATOR], 'queue', $realSizeQueue)) {
			$b[$i] .= '<span class="button disable hb lt" title="file de construction pleine, revenez dans un moment">';
				$b[$i] .= '<span class="text">';
					$b[$i] .= 'construction impossible<br/>';
					$b[$i] .= $price . ' | ' . $time;
				$b[$i] .= '</span>';
			$b[$i] .= '</span>';
		} elseif (!OrbitalBaseResource::haveRights($i, $nextLevel, 'resource', $ob_generator->getResourcesStorage())) {
			$missingResource = OrbitalBaseResource::getBuildingInfo($i, 'level', ($nextLevel), 'resourcePrice') - $ob_generator->getResourcesStorage();
			$b[$i] .= '<span class="button disable hb lt" title="pas assez de ressources, il manque ' . Format::numberFormat($missingResource) . ' ressource' . Format::plural($missingResource) . '">';
				$b[$i] .= '<span class="text">';
					$b[$i] .= 'construction impossible<br/>';
					$b[$i] .= $price . ' | ' . $time;
				$b[$i] .= '</span>';
			$b[$i] .= '</span>';
		} else {
			$b[$i] .= '<a class="button" href="' . Format::actionBuilder('buildbuilding', ['baseid' => $ob_generator->getId(), 'building' => $i]) . '">';
				$b[$i] .= '<span class="text">';
					$b[$i] .= 'augmenter vers le niveau ' . $nextLevel . '<br/>';
					$b[$i] .= $price . ' | ' . $time;
				$b[$i] .= '</span>';
			$b[$i] .= '</a>';
		}
	$b[$i] .= '</div>';
}

# display
echo '<div class="component">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/generator.png" alt="" />';
		echo '<h2>' . OrbitalBaseResource::getBuildingInfo(0, 'frenchName') . '</h2>';
		echo '<em>niveau ' . $ob_generator->getLevelGenerator() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box ' . ((CTR::$data->get('playerBonus')->get(PlayerBonus::GENERATOR_SPEED) == 0) ? 'grey' : '') . '">';
				echo '<span class="label">bonus de vitesse de construction</span>';
				echo '<span class="value">';
					echo Format::numberFormat(CTR::$data->get('playerBonus')->get(PlayerBonus::GENERATOR_SPEED)) . ' %';
				echo '</span>';
			echo '</div>';
			
			echo '<h4>File de construction</h4>';
			echo $q;
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component generator">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Bâtiments neutres</h4>';
			echo $b[0];
			echo $b[5];
			echo $b[7];
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component generator">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Bâtiments commerciaux</h4>';
			echo $b[1];
			echo $b[6];
			echo $b[9];
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component generator">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Bâtiments militaires</h4>';
			echo $b[2];
			echo $b[8];
			echo $b[3];
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>À propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . OrbitalBaseResource::getBuildingInfo(0, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
