<?php
class ColorResource {
	# constants for factions
	const EMPIRE = 1;
	const KOVAHK = 2;
	const NEGORA = 3;
	const CARDAN = 4;
	const NERVE = 5;
	const APHERA = 6;
	const SYNELLE = 7;

	# constants for the actions bonuses of the factions
	const BONUS_EMPIRE_CRUISER = 5; # price 5% less for cruiser and heavy cruiser
	const BONUS_NEGORA_ROUTE = 3; # price 3% less for the price of a commercial route
	const BONUS_CARDAN_COLO = 10; # price 10% less for colo or conquest
	const BONUS_APHERA_TECHNO = 4; # 4% less time to build technologies

	public static function getInfo($id, $info) {
		if ($id <= count(self::$colors)) {
			if (in_array($info, array(
				'id', 
				'officialName', 
				'popularName', 
				'government', 
				'factionPoint', 
				'status', 
				'devise', 
				'desc1', 
				'desc2', 
				'desc3', 
				'desc4', 
				'mandateDuration',
				'senateDesc',
				'campaignDesc'))) {
				return self::$colors[$id - 1][$info];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	private static $colors = array(
		array(
			'id' => 1,
			'officialName' => 'Ordre Impérial',
			'popularName' => 'l’Empire',
			'government' => 'Maison Akhénienne',
			'factionPoint' => 'Points de Prestige',
			'status' => array('Noble', 'Dynaste', 'Dynastaire-trésorier', 'Dynastaire-chef de guerre', 'Dynastaire-ministre', 'Empereur'),
			'devise' => 'Des nefs d’acier,<br />Naquit l’équilibre',
			'desc1' => 'Faction centrale de la Galaxie de l’Œil, l’Ordre Impérial cherche a bâtir un empire puissant, dont les ramifications mèneraient jusqu’aux confins de la Galaxie. Un empire sous l’égide de la Maison Akhénienne, totalitaire et immuable.',
			'desc2' => 'Ordre à la forte puissance militaire, les Akhéniens ne croient qu’en une chose, la suprématie de l’Empereur, seul et unique guide de cette Faction.',
			'desc3' => 'Leur technologie, fiable et robuste, repose sur de longues strates de savoir-faire et sur un base solide d’expérience en matière d’armement et d’aérospatial. Industriellement très développée, l’économie Akhénienne est une machine bien rodée, pouvant allègrement soutenir l’effort de guerre.',
			'desc4' => 'Leurs grandes richesses, principalement acquises sur les plateformes de forage et dans d’énormes concessions minières, leur apportent des ressources de qualités et en grand nombre. Les différents accords, traités et taxes commerciales ainsi que leur situation centrale dans la Galaxie, leur apportent également des revenus stables et constants.',
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un Akhénien gagne du prestige en construisant des bâtiments et des vaisseaux du chantier de ligne, mais également en conquérant les bases orbitales d\'autres joueurs. Il en perd par contre lorsqu\'il détruit un de ses bâtiments ou qu\'il perd une planète.',
			'campaignDesc' => 'explicatif d\'une campagne'),
		array(
			'id' => 2,
			'officialName' => 'Province de Kovahk',
			'popularName' => 'l’Esseim',
			'government' => 'Maison des Kovahkarh',
			'factionPoint' => 'Points de Bataille',
			'status' => array('Guerrier', 'Dynaste', 'Financier', 'Chef de guerre', 'Ministre', 'Baron'),
			'devise' => 'Eclats de métal dans le ciel',
			'desc1' => 'Fiers sont les soldats Kovahkarh, sans pareil est leur honneur dans la Galaxie de l’Oeil. Kovahk est une faction guerrière aux moeurs parfois brutaux et au caractère bien trempé tel l’acier qu’ils vénèrent et adulent.',
			'desc2' => 'Libérateur des populations oppressées de l’Ancien Empire, Kovahk vénère le pouvoir du métal rarissime et infaillible, un métal qui résisterai à toutes attaques ennemies. Habiles forgerons capable de créer les alliages les plus robustes, l’essaim Kovahkarh étend son influence sur la Galaxie.',
			'desc3' => 'Leur longue tradition de pillage et d’exploration de la Galaxie, leur a amené de très grandes richesses ainsi qu’une habilté hors du commun pour la construction de vaisseaux de combats rapides et puissants.',
			'desc4' => 'Basé sur système politique très martial et militaire dirigé par un Duc, la société Kovahkarh fait preuve d’une grande discipline et d’une rigueur sans égale. Uni dans un même idéal de victoire et d’expansion, cette faction est une des plus solide de la Galaxie.',
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un Kovahkarh gagne du prestige en construisant des vaisseaux, en gagnant des combats et en créant des bases militaires. Il en perd lorsqu\'il se fait tuer au combat ou qu\'il perd une de ces bases militaires.',
			'campaignDesc' => 'Les membres du Sénat peuvent se présenter aux élections pour prendre une place politiquement importante dans la faction.<br />Une fois que toutes les candidatures ont été déposées, tous les membres de Kovahk peuvent voter pour le candidat de leur choix'),
		array(
			'id' => 3,
			'officialName' => 'Province de Négora',
			'popularName' => 'Négore',
			'government' => 'Maison Négienne',
			'factionPoint' => 'Points de Marchandage',
			'status' => array('Commerçant', 'Négociant', 'Financier', 'Chef de guerre', 'Ministre', 'Viziduc'),
			'devise' => 'Toutes les richesses,<br />Passent par Négore',
			'desc1' => 'La maison Négienne, la plus riches de la Galaxie de l’Œil, est composée essentiellement de grands marchands et de financiers expérimentés. Considérée comme la banque de la galaxie depuis très longtemps, son économie est basée sur les échanges et le commerce.',
			'desc2' => 'Parfois peu regardant sur la provenance des marchandises, les Négiens sont d’adroits négociateurs n’hésitant pas à profiter des opportunités qu’offrent la contre-bande, le trafic d’armes ainsi que la vente d’esclaves au détriment des accords et traités commerciaux.',
			'desc3' => 'Vivant dans le luxe et l’opulence, les Vizirs de Négore sont à la tête de fortunes faramineuses permettant les caprices les plus fous; Orgies, casinos, courses et paris sont le quotidiens de cette province aux moeurs débridés.',
			'desc4' => 'Doté de moyens militaires fastueux, la flotte Négienne est rutilante et persuasive, disposant d’une prodigieuse armada de vaisseaux destinée à assurer sa prospérité et son avenir économique dans la Galaxie.',
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un Négien gagne du prestige en créant des routes commerciales et en faisant des ventes/achats intéressants sur le marché. Par contre il en perd lorsqu\'une de ses routes commerciales est détruite ou qu\'il fait une vente ou un achat pas rentable.',
			'campaignDesc' => 'explicatif d\'une campagne'),
		array(
			'id' => 4,
			'officialName' => 'Marche de Cardan',
			'popularName' => 'la Marche',
			'government' => 'Eglise Cardanienne',
			'factionPoint' => 'Points de Foi',
			'status' => array('Fidèle', 'Prêtre', 'Archiprêtre-trésorier', 'Archiprêtre-chef de guerre', 'Archiprêtre-ministre', 'Guide'),
			'devise' => 'La lumière vous balaiera',
			'desc1' => 'L’Eglise Cardanienne est la seule faction théocratique de la Galaxie. Elle fût, pendant de longues années, un mouvement disciminé, peuplée uniquement par des moines et des hommes pieux. Mais des dérives fanatiques ont poussées la Marche de Cardan à devenir une faction belliqueuse et extrémiste, éblouie par un pouvoir suprême et divin qui les mènera à la victoire.',
			'desc2' => 'Les rites prennent une place très importants dans le mode de vie des Cardamines. Nombreux sacrifices et rituels sont fait à chaque Segment en l’honneur des Dieux. Les fidèles se doivent de se plier à la loi Cardamine et respecter les ordre du Guide Suprême.',
			'desc3' => 'Une grande armée de puissants guerriers vouées au culte de Cardan, fanatiques experts dans le  maniement des armes, les combattants de Cardan n’ont peur que d’une seule chose : ne pas mourir en martyres et décevoir l’Ordre Suprême.',
			'desc4' => 'Ils ne pratiquent que peu de commerce avec les nations étrangères, préférant se suffire à eux même, même si cela doit aboutir à la famine et à la mort des plus démunis.',
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un membre de Cardan gagne du prestige en colonisant/conquérant une planète se situant en-dehors des territoires de Cardan et en faisant un don à sa faction. Il en perd par contre lorsqu\'il perd une planète.',
			'campaignDesc' => 'explicatif d\'une campagne'),
		array(
			'id' => 5,
			'officialName' => 'Province de Nerve',
			'popularName' => 'la Nerve',
			'government' => 'République Nervéenne',
			'factionPoint' => 'Points d\'industrie',
			'status' => array('Citoyen', 'Député', 'Ministre-trésorier', 'Ministre-chef de guerre', 'Ministre-ministre', 'Président'),
			'devise' => 'Jamais ne tombera,<br />La ville aux Milles Sous-sols',
			'desc1' => 'La république Nervéenne est composée d’une grande communautés préférant vivre à l’écart, cachée dans d’incroyables labyrinthes sous-terrain. Elle est connue principalement pour sa capacité à camoufler la quasi-totalité de ces infrastructures à ses ennemis ainsi que pour ses qualités de bâtisseurs hors-normes.',
			'desc2' => 'Communauté soudée autour de son Chancelier, la Nerve est une faction de grands travailleurs et de bâtisseurs parmi les plus fameux de toute la Galaxie. Réalisant des édifices enfouis d’une finesse été d’une complexité incroyable.',
			'desc3' => 'N’ayant pas de grande connaissance de l’art de la guerre, mais contraint de lutter pour la préservation de leur mode de vie, ils se sont adaptés et ont formés de redoutables forteresses défensives.',
			'desc4' => 'Grâce à un dédale de culture hydroponique et une grande connaissance en agro-alchimie mais également en extraction de minerai, l’industriel Nervéen produit une grande quantité de ressources attirant ainsi les marchands les plus riches de la galaxie, leur permettant ainsi un essor prospère.',
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un Nervéen gagne du prestige en créant des Centres Industriels, en construisant la raffinerie et les chantiers de construction de vaisseaux et en prenant des planètes (proportionnel au coefficient ressource de la planète). Il en perd en détruisant un Centre Industriel, en détruisant la raffinerie et les chantiers et en perdant le contrôle d\'une planète.',
			'campaignDesc' => 'explicatif d\'une campagne'),
		array(
			'id' => 6,
			'officialName' => 'Province d’Aphéra',
			'popularName' => 'Aphéra',
			'government' => 'République Aphérane',
			'factionPoint' => 'Points de Technologie',
			'status' => array('Citoyen', 'Député', 'Conseiller-trésorier', 'Conseiller-chef de guerre', 'Conseiller-ministre', 'Chancelier'),
			'devise' => 'Au travers du vide,<br />Nos oiseaux perçents,<br/>Levez les yeux',
			'desc1' => 'La république d’Aphéra, réputée pour son potentiel scientifique, est une faction composée des plus grands chercheurs. Avide de technologie et de progrès, les citées d’Aphéra s’étendent au dessus des nuages, créant de fantastiques villes volantes.',
			'desc2' => 'Conscient de bienfait de la nature, Aphéra vit en harmonie avec son environnement dont elle tire la majorité de ses ressources énergétiques. Développement durable, énergies renouvelables, cette faction fait preuve d’une conscience écologique très important, préférant la technologie de pointe à une industrie de masse.',
			'desc3' => 'Entrainés depuis leur plus jeune âge, les pilotes aphérans sont de fins tacticiens et des as du pilotage, faisant preuve d’une maitrise quasi absolue au commandes de leurs vaisseaux, le ciel est leur terrain de jeu.',
			'desc4' => 'N’appréciant pas d’être à l’écart des décisions politique, les aphérants savent faire preuve de poigne lors que cela est nécessaire, déployant des moyens militaires impressionnant pour faire valoir leur idéologie démocratique au travers de toute la Galaxie.',
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un Aphéran gagne du prestige en menant à terme une Recherche, en développant une Technologie et en réusissant un espionnage.',
			'campaignDesc' => 'explicatif d\'une campagne'),
		array(
			'id' => 7,
			'officialName' => 'Marche de Synelle',
			'popularName' => 'Synelle',
			'government' => 'Fédération Synélectique',
			'factionPoint' => 'Points de Sagesse',
			'status' => array('Fédéré', 'Conseiller', 'Consul-trésorier', 'Consul-chef de guerre', 'Consul-ministre', 'Fédérant'),
			'devise' => 'Au plus loin des Guerres,<br />La vie prends racine',
			'desc1' => 'La fédération Synélectique, basée sur la connaissance et le savoir, est une faction composée de libres penseurs, de diplomates, d’érudits et de philosophes. Préférant une stratégie plus réfléchie à des attaques éclaires, le système militaire de Synelle est puissant et méthodique.',
			'desc2' => 'Le cursus académique et la formation prennent une place extrêmement important dans le mode de vie des Syns. Vivent selon les principes du Premier Consul, cette société calme et autarcique, renferme une très grande partie du patrimoine historique de la Galaxie dans de gigantesques bibliothèques.',
			'desc3' => 'Fière de son indépendance, Synelle la sage, dispose d’un important arsenal militaire prêt à défendre ses richesses contre quiconque viendra à les défier. Synelle aime rester en bon terme avec les différentes alliances peuplant la galaxie, préfèrent la diplomatie à la guerre.',
			'desc4' => 'Cette province, considérée comme le frigo de la galaxie de l’Œil de par son importante production de denrée alimentaire, n’est pas à la pointe de la technologie, préférant vivre simplement en accord avec ses principes de vie.',
			'mandateDuration' => 604800,
			'senateDesc' => 'Le sénat est composé des membres de la faction qui possèdent le plus de prestige. Un membre de Synelle gagne du prestige en étant victorieux lors de la défense d\'une de ses planètes',
			'campaignDesc' => 'explicatif d\'une campagne')
	);
}
?>