<?php
/*
 *  TRINITY FRAMEWORK <http://www.invenzzia.org>
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 */

namespace Trinity\WebUtils\Model;

/**
 * The class provides some utilities for model classes.
 */
class Utils
{
	/**
	 * The slug generator. Conversion concept comes from Matteo Spinelli
	 *
	 * @author Matteo Spinelli
	 * @link http://cubiq.org/the-perfect-php-clean-url-generator
	 * @param string $string The string to convert.
	 * @return string
	 */
	static public function toSlug($string)
	{
		$clean = \iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		$clean = \preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = \strtolower(\trim($clean, '-'));
		$clean = \preg_replace("/[\/_|+ -]+/", '-', $clean);

		return $clean;
	} // end toSlug();

	/**
	 * Converts boolean values to "Yes/No" answers.
	 *
	 * @param \Opc\Translation $translation The translation interface
	 * @param boolean $state The state to convert
	 * @return string
	 */
	static public function yesno($translation, $state)
	{
		if($state === true)
		{
			return $translation->_('global', 'yes');
		}
		return $translation->_('global', 'no');
	} // end yesno();
} // end Utils;