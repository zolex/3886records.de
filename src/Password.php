<?php

class Password
{
	protected $stretches = 3;
	protected $separator = '#';
	protected $algorithm = 'sha512';
	protected $pepper = '450396288feacd4c2513e72bc4f3b4e2';
	protected $salt;

	public function __construct($pass, $salt = null) {

		if (null === $salt) {

			srand();
			$salt = hash('md5', uniqid(rand()));
		}

		$this->salt = $salt;
		$this->encrypted = $this->encrypt($pass);
	}

	protected function encrypt($string) {

		for ($n = 0; $n < $this->stretches; $n++) {

			$string = hash($this->algorithm, $string . $this->separator . $this->salt . $this->separator . $this->pepper);
		}

		return $string;
	}

	public function setPepper($pepper) {

		$this->pepper = (string)$pepper;
		return $this;
	}

	public function getSalt() {

		return $this->salt;
	}

	public function __toString() {

		return $this->encrypted;
	}
}