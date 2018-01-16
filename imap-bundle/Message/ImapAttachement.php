<?php
/**
 * Created by PhpStorm.
 * Project: imap
 * User: m.benhenda
 * Date: 15/01/2018
 * Time: 15:47
 */

namespace YMW\ImapBundle\Message;


class ImapAttachment
{
	public function __construct(ImapMessage $message, $attachment)
	{
		$this->message = $message;
		$this->attachment = $attachment;
	}

	public function getAttachement()
	{
		return $this->attachment;
	}

	/**
	 * @return string;
	 */
	public function getBody()
	{
		return $this->message->EmailBody();
	}

	/**
	 * @return int
	 */
	public function getSize()
	{
		return (int)$this->attachment->bytes;
	}

	/**
	 * @return string
	 */
	public function getExtension()
	{
		return pathinfo($this->getFilename(), PATHINFO_EXTENSION);
	}

	/**
	 * @return mixed
	 */
	public function getFilename()
	{
		$filename = $this->attachment->filename;
		NULL === $filename && $filename = $this->attachment->name;
		return $filename;
	}

	/**
	 * @return bool|string
	 * @throws \Exception
	 */
	public function __toString()
	{
		$encoding = $this->attachment->encoding;
		switch ($encoding) {
			case 0: // 7BIT
			case 1: // 8BIT
			case 2: // BINARY
				return $this->getBody();
			break;
			case 3: // BASE-64
				return base64_decode($this->getBody());
			break;
			case 4: // QUOTED-PRINTABLE
				return imap_qprint($this->getBody());
		}
		throw new \Exception(sprintf('Encoding failed: Unknown encoding %s (5: OTHER).', $encoding));
	}
}