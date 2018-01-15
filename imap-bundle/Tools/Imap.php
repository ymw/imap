<?php

/**
 * Created by PhpStorm.
 * Project: imap
 * User: m.benhenda
 * Date: 15/01/2018
 * Time: 15:56
 */
namespace YMW\ImapBundle\Tools;

class Imap
{
	/**
	 *
	 * =?x-unknown?B?
	 * =?iso-8859-1?Q?
	 * =?windows-1252?B?
	 *
	 * @param string $stringQP
	 * @param string $base (optional) charset (IANA, lowercase)
	 * @return string UTF-8
	 */
	public static function decodeToUTF8($stringQP, $base = 'windows-1252')
	{
		$pairs = array(
			'?x-unknown?' => "?$base?"
		);
		$stringQP = strtr($stringQP, $pairs);

		return imap_utf8($stringQP);
	}
}