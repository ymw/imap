<?php

/**
 * Created by PhpStorm.
 * Project: imap
 * User: m.benhenda
 * Date: 15/01/2018
 * Time: 14:27
 */

namespace YMW\ImapBundle\MailBox;

class MailBox
{
	private $server;
	private $stream;

	/**
	 * MailBox constructor.
	 *
	 * @param array $configs
	 *
	 * @throws \Exception
	 */
	public function __construct(array $configs)
	{
		$folder = $configs['folder']?:'';
		$options = $configs['options']?:NIL;
		$nTries = $configs['n_tries']?:NIL;
		$optionsMailBox = '';
		if (array_key_exists('options_mail_box', $configs)) {
			foreach ($configs['options'] as $option) {
				$optionsMailBox .= '/'.$option;
			}
		}
		$this->server = '{'.$configs['server'].':'.$configs['port'].$optionsMailBox.'}'.$folder;

		$inbox = $this->imapOpen($this->server, $configs['username'], $configs['password'], $options, $nTries, $configs['params']);
		if (FALSE === $inbox) {
			throw new \Exception('Connect failed: ' . imap_last_error());
		}
		$this->stream = $inbox;
	}

	public function imapOpen($mailBox, $username, $password, $options, $nTries, $params)
	{
		try {
			return @imap_open($mailBox, $username, $password, $options, $nTries, $params);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage() . ' in line ' . $e->getLine() . ' in file : '. $e->getFile());
		}
	}

	/**
	 * @return \stdClass
	 * @throws \Exception
	 */
	public function check()
	{
		$info = imap_check($this->stream);
		if (FALSE === $info) {
			throw new \Exception('Check failed: ' . imap_last_error());
		}

		return $info;
	}

	/**
	 * @return resource
	 */
	public function getStream()
	{
		return $this->stream;
	}

	/**
	 * @param $criteria
	 * @param null $options
	 * @param null $charset
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function search( $criteria, $options = NULL, $charset = NULL)
	{
		$emails = imap_search($this->stream, $criteria, $options, $charset);
		if (FALSE === $emails) {
			throw new \Exception('Search failed: ' . imap_last_error());
		}

		return $emails;
	}

	/**
	 * @param int $number
	 * @return IMAPMessage
	 */
	public function getMessageByNumber($number)
	{
		return new IMAPMessage($this, $number);
	}

	public function getOverview($sequence = NULL)
	{
		if (NULL === $sequence) {
			$sequence = sprintf('1:%d', count($this));
		}
		return new IMAPOverview($this, $sequence);
	}
	/**
	 * Retrieve an external iterator
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing Iterator or
	 * Traversable
	 */
	public function getIterator()
	{
		return $this->getOverview()->getIterator();
	}
	/**
	 * @return int
	 */
	public function count()
	{
		return $this->check()->Nmsgs;
	}


}