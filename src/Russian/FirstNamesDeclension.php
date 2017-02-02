<?php
namespace morphos\Russian;

/**
 * Rules are from: http://www.imena.org/decl_mn.html
 * and http://www.imena.org/decl_fn.html
 */
class FirstNamesDeclension extends \morphos\NamesDeclension implements Cases {
	use RussianLanguage;

	protected $exceptions = array(
		'лев' => array(
			self::IMENIT_1 => 'Лев',
			self::RODIT_2 => 'Льва',
			self::DAT_3 => 'Льву',
			self::VINIT_4 => 'Льва',
			self::TVORIT_5 => 'Львом',
			self::PREDLOJ_6 => 'о Льве',
		),
		'павел' => array(
			self::IMENIT_1 => 'Павел',
			self::RODIT_2 => 'Павла',
			self::DAT_3 => 'Павлу',
			self::VINIT_4 => 'Павла',
			self::TVORIT_5 => 'Павлом',
			self::PREDLOJ_6 => 'о Павле',
		)
	);

	public function hasForms($name, $gender) {
		//var_dump(upper(slice($name, -1)));
		$name = lower($name);
		// man rules
		if ($gender === self::MAN) {
			// soft consonant
			if (lower(slice($name, -1)) == 'ь' && in_array(upper(slice($name, -2, -1)), self::$consonants)) {
				return true;
			} else if (in_array(upper(slice($name, -1)), array_diff(self::$consonants, array('Й', /*'Ч', 'Щ'*/)))) { // hard consonant
				return true;
			} else if (slice($name, -1) == 'й') {
				return true;
			}
		}

		// common rules
		if ((in_array(slice($name, -1), array('а', 'я')) && !in_array(upper(slice($name, -2, -1)), self::$vowels)) || in_array(slice($name, -2), array('ия', 'ья', 'ея'))) {
			return true;
		}

		return false;
	}

	public function getForms($name, $gender) {
		$name = lower($name);
		if ($gender == self::MAN) {
			if (in_array(upper(slice($name, -1)), array_diff(self::$consonants, array('Й', /*'Ч', 'Щ'*/)))) { // hard consonant
				$prefix = name($name);
				// special cases for Лев, Павел
				if (isset($this->exceptions[$name]))
					return $this->exceptions[$name];
				else {
					return array(
						self::IMENIT_1 => $prefix,
						self::RODIT_2 => $prefix.'а',
						self::DAT_3 => $prefix.'у',
						self::VINIT_4 => $prefix.'а',
						self::TVORIT_5 => RussianLanguage::isHissingConsonant(slice($name, -1)) || slice($name, -1) == 'ц' ? $prefix.'ем' : $prefix.'ом',
						self::PREDLOJ_6 => $this->choosePrepositionByFirstLetter($prefix, 'об', 'о').' '.$prefix.'е',
					);
				}
			} else if (slice($name, -1) == 'ь' && in_array(upper(slice($name, -2, -1)), self::$consonants)) { // soft consonant
				$prefix = name(slice($name, 0, -1));
				return array(
					self::IMENIT_1 => $prefix.'ь',
					self::RODIT_2 => $prefix.'я',
					self::DAT_3 => $prefix.'ю',
					self::VINIT_4 => $prefix.'я',
					self::TVORIT_5 => $prefix.'ем',
					self::PREDLOJ_6 => $this->choosePrepositionByFirstLetter($prefix, 'об', 'о').' '.$prefix.'е',
				);
			} else if (in_array(slice($name, -2), array('ай', 'ей', 'ой', 'уй', 'яй', 'юй', 'ий'))) {
				$prefix = name(slice($name, 0, -1));
				$postfix = slice($name, -2) == 'ий' ? 'и' : 'е';
				return array(
					self::IMENIT_1 => $prefix.'й',
					self::RODIT_2 => $prefix.'я',
					self::DAT_3 => $prefix.'ю',
					self::VINIT_4 => $prefix.'я',
					self::TVORIT_5 => $prefix.'ем',
					self::PREDLOJ_6 => $this->choosePrepositionByFirstLetter($prefix, 'об', 'о').' '.$prefix.$postfix,
				);
			} else if (slice($name, -1) == 'а' && ($before = slice($name, -2, -1)) && self::isConsonant($before) && !in_array($before, array(/*'г', 'к', 'х', */'ц'))) {
				$prefix = name(slice($name, 0, -1));
				$postfix = (RussianLanguage::isHissingConsonant($before) || in_array($before, array('г', 'к', 'х'))) ? 'и' : 'ы';
				return array(
					self::IMENIT_1 => $prefix.'а',
					self::RODIT_2 => $prefix.$postfix,
					self::DAT_3 => $prefix.'е',
					self::VINIT_4 => $prefix.'у',
					self::TVORIT_5 => $prefix.'ой',
					self::PREDLOJ_6 => $this->choosePrepositionByFirstLetter($prefix, 'об', 'о').' '.$prefix.'е',
				);
			} else if (slice($name, -2) == 'ия') {
				$prefix = name(slice($name, 0, -1));
				return array(
					self::IMENIT_1 => $prefix.'я',
					self::RODIT_2 => $prefix.'и',
					self::DAT_3 => $prefix.'и',
					self::VINIT_4 => $prefix.'ю',
					self::TVORIT_5 => $prefix.'ей',
					self::PREDLOJ_6 => $this->choosePrepositionByFirstLetter($prefix, 'об', 'о').' '.$prefix.'и',
				);
			} else if (slice($name, -2) == 'ло' || slice($name, -2) == 'ко') {
				$prefix = name(slice($name, 0, -1));
				$postfix = slice($name, -2, -1) == 'к' ? 'и' : 'ы';
				return array(
					self::IMENIT_1 => $prefix.'о',
					self::RODIT_2 =>  $prefix.$postfix,
					self::DAT_3 => $prefix.'е',
					self::VINIT_4 => $prefix.'у',
					self::TVORIT_5 => $prefix.'ой',
					self::PREDLOJ_6 => $this->choosePrepositionByFirstLetter($prefix, 'об', 'о').' '.$prefix.'е',
				);
			}
		} else if ($gender == self::WOMAN) {
			if (slice($name, -1) == 'а' && !in_array(upper($before = (slice($name, -2, -1))), self::$vowels)) {
				$prefix = name(slice($name, 0, -1));
				if ($before != 'ц') {
					$postfix = (RussianLanguage::isHissingConsonant($before) || in_array($before, array('г', 'к', 'х'))) ? 'и' : 'ы';
					return array(
						self::IMENIT_1 => $prefix.'а',
						self::RODIT_2 => $prefix.$postfix,
						self::DAT_3 => $prefix.'е',
						self::VINIT_4 => $prefix.'у',
						self::TVORIT_5 => $prefix.'ой',
						self::PREDLOJ_6 => $this->choosePrepositionByFirstLetter($prefix, 'об', 'о').' '.$prefix.'е',
					);
				} else {
					return array(
						self::IMENIT_1 => $prefix.'а',
						self::RODIT_2 => $prefix.'ы',
						self::DAT_3 => $prefix.'е',
						self::VINIT_4 => $prefix.'у',
						self::TVORIT_5 => $prefix.'ей',
						self::PREDLOJ_6 => $this->choosePrepositionByFirstLetter($prefix, 'об', 'о').' '.$prefix.'е',
					);
				}
			} else if (slice($name, -1) == 'ь' && self::isConsonant(slice($name, -2, -1))) {
				$prefix = name(slice($name, 0, -1));
				return array(
					self::IMENIT_1 => $prefix.'ь',
					self::RODIT_2 => $prefix.'и',
					self::DAT_3 => $prefix.'и',
					self::VINIT_4 => $prefix.'ь',
					self::TVORIT_5 => $prefix.'ью',
					self::PREDLOJ_6 => $this->choosePrepositionByFirstLetter($prefix, 'об', 'о').' '.$prefix.'и',
				);
			} else if (RussianLanguage::isHissingConsonant(slice($name, -1))) {
				$prefix = name($name);
				return array(
					self::IMENIT_1 => $prefix,
					self::RODIT_2 => $prefix.'и',
					self::DAT_3 => $prefix.'и',
					self::VINIT_4 => $prefix,
					self::TVORIT_5 => $prefix.'ью',
					self::PREDLOJ_6 => $this->choosePrepositionByFirstLetter($prefix, 'об', 'о').' '.$prefix.'и',
				);
			}
		}

		// common rules for ия and я
		if (slice($name, -1) == 'я' and slice($name, -2, -1) != 'и') {
			$prefix = name(slice($name, 0, -1));
			return array(
				self::IMENIT_1 => $prefix.'я',
				self::RODIT_2 => $prefix.'и',
				self::DAT_3 => $prefix.'е',
				self::VINIT_4 => $prefix.'ю',
				self::TVORIT_5 => $prefix.'ей',
				self::PREDLOJ_6 => $this->choosePrepositionByFirstLetter($prefix, 'об', 'о').' '.$prefix.'е',
			);
		} else if (slice($name, -2) == 'ия') {
			$prefix = name(slice($name, 0, -1));
			return array(
				self::IMENIT_1 => $prefix.'я',
				self::RODIT_2 => $prefix.'и',
				self::DAT_3 => $prefix.'и',
				self::VINIT_4 => $prefix.'ю',
				self::TVORIT_5 => $prefix.'ей',
				self::PREDLOJ_6 => $this->choosePrepositionByFirstLetter($prefix, 'об', 'о').' '.$prefix.'и',
			);
		}

		$name = name($name);
        return array_fill_keys(array(self::IMENIT_1, self::RODIT_2, self::DAT_3, self::VINIT_4, self::TVORIT_5), $name) + array(self::PREDLOJ_6 => $this->choosePrepositionByFirstLetter($name, 'об', 'о').' '.$name);
	}

	public function getForm($name, $form, $gender) {
		$forms = $this->getForms($name, $gender);
		if ($forms !== false)
			if (isset($forms[$form]))
				return $forms[$form];
			else
				return $name;
		else
			return $name;
	}
}
