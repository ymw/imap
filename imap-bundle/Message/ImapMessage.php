<?php

/**
 * Created by PhpStorm.
 * Project: imap
 * User: m.benhenda
 * Date: 15/01/2018
 * Time: 15:07
 */

namespace YMW\ImapBundle\Message;

use YMW\ImapBundle\MailBox\MailBox;
use YMW\ImapBundle\Tools\Imap;

class ImapMessage
{
	private $mailBox;
	private $uid;
	private $header;
	private $body;
	private $attachements;

	public function __construct(MailBox $mailBox, $uid)
	{
		$this->mailBox = $mailBox;
		$this->uid = $uid;
		$this->header = imap_headerinfo($this->mailBox, $this->uid);
		$this->body = imap_fetchbody($this->mailBox, $this->uid);
		$this->attachements = $this->parseStructure();
	}

	/**
	 * @return string
	 */
	public function EmailBody()
	{
		return $this->body;
	}

	/**
	 * @return object
	 */
	public function headerInfo()
	{
		return $this->header;
	}

	public function getAttachements()
	{
		return $this->attachements;
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	private function parseStructure()
	{
		/** @var \stdClass $structure */
		$structure = imap_fetchstructure($this->mailBox, $this->uid);
		if (FALSE === $structure) {
			throw new \Exception('FetchStructure failed: ' . imap_last_error());
		}
		$attachments = array();
		if (!isset($structure->parts)) {
			return $attachments;
		}
		foreach ($structure->parts as $index => $part)
		{
			if (!$part->ifdisposition) continue;
			$attachment = new \stdClass;
			$attachment->isAttachment = FALSE;
			$attachment->number = $index + 1;
			$attachment->bytes = $part->bytes;
			$attachment->encoding = $part->encoding;
			$attachment->filename = NULL;
			$attachment->name = NULL;
			$part->ifdparameters
			&& ($attachment->filename = $this->getAttribute($part->dparameters, 'filename'))
			&& $attachment->isAttachment = TRUE;
			$part->ifparameters
			&& ($attachment->name = $this->getAttribute($part->parameters, 'name'))
			&& $attachment->isAttachment = TRUE;
			$attachment->isAttachment
			&& $attachments[] = new ImapAttachment($this, $attachment);
		}

		return $attachments;
	}

	private function getAttribute($params, $name)
	{
		foreach ($params as $object)
		{
			if ($object->attribute == $name) {
				return Imap::decodeToUTF8($object->value);
			}
		}
		return NULL;
	}

}