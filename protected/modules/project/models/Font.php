<?php

class Font {
	public $family = '';
	public $style = '';
	public $weight = '';
	public $face = array();

	public function __construct( $family = null ) {
		$this->family = $family;
	}

	public function addFontFace( $face ) {
		if ($this->family == $face->family) {
			$this->face[] = $face;
		}
	}

	public function panagram( $lang = 'ru' ) {
		$i18n = array();

		$i18n['en'] = array(
			"Pack my box with five dozen liquor jugs.",
			"Jackdaws love my big sphinx of quartz.",
			"The five boxing wizards jump quickly.",
			"How vexingly quick daft zebras jump!",
			"Bright vixens jump; dozy fowl quack.",
			"Sphinx of black quartz, judge my vow.",
			"Few quips galvanized the mock jury box.",
			"Quick brown fox jumps over the lazy dog.",
			"Quilt frenzy jackdaw gave them best pox.",
			"Jumpy halfling dwarves pick quartz box.",
			"Schwarzkopf vexed Iraq big-time in July.",
			"Vex quest wizard, judge my backflop hand.",
			"The jay, pig, fox, zebra and my wolves quack!",
			"Blowzy red vixens fight for a quick jump.",
			"Sex prof gives back no quiz with mild joy.",
			"The quick brown fox jumps over a lazy dog.",
			"A quick brown fox jumps over the lazy dog.",
			"Quest judge wizard bonks foxy chimp love.",
			"Boxers had zap of gay jock love, quit women.",
			"Joaquin Phoenix was gazed by MTV for luck.",
			"JCVD might pique a sleazy boxer with funk.",
			"Quizzical twins proved my hijack-bug fix.",
		);

		$i18n['ru'] = array(
			"Друг мой эльф! Яшке б свёз птиц южных чащ!",
			"В чащах юга жил бы цитрус? Да, но фальшивый экземпляр!",
			"Любя, съешь щипцы, — вздохнёт мэр, — кайф жгуч.",
			"Шеф взъярён тчк щипцы с эхом гудбай Жюль.",
			"Эй, жлоб! Где туз? Прячь юных съёмщиц в шкаф.",
			"Экс-граф? Плюш изъят. Бьём чуждый цен хвощ!",
			"Эх, чужак! Общий съём цен шляп (юфть) — вдрызг!",
			"Эх, чужд кайф, сплющь объём вши, грызя цент.",
			"Чушь: гид вёз кэб цапф, юный жмот съел хрящ.",
			"Съешь же ещё этих мягких французских булок, да выпей чаю.",
			"Широкая электрификация южных губерний даст мощный толчок подъёму сельского хозяйства.",
			"Разъяренный чтец эгоистично бьёт пятью жердями шустрого фехтовальщика.",
			"Наш банк вчера же выплатил Ф.Я. Эйхгольду комиссию за ценные вещи.",
		);

		return $i18n[ $lang ][ rand(0, count($i18n[ $lang ])-1) ];
	}

}