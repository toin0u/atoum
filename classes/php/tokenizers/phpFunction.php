<?php

namespace mageekguy\atoum\php\tokenizers;

use
	mageekguy\atoum\php,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers\phpFunction
;

class phpFunction extends php\tokenizer
{
	private $stack = null;

	public function __construct($string = null)
	{
		$this->putInString($this->name)->valueOfToken(T_STRING)->afterToken(T_FUNCTION)->skipToken(T_WHITESPACE);

		parent::__construct($string);
	}

	public function getName()
	{
		return ($this->name ?: null);
	}

	public function canTokenize(php\tokenizer\tokens $tokens)
	{
		return $tokens->currentTokenHasName(T_FUNCTION) && $tokens->nextTokenHasName(T_STRING, array(T_WHITESPACE));
	}

	public function tokenize($string)
	{
		$tokens = new tokenizer\tokens($string);

		if ($tokens->valid() === true && $tokens->current()->getName() === T_OPEN_TAG)
		{
			$tokens->next();
		}

		return $this->setFromTokens($tokens);
	}

	public function getIteratorInstance()
	{
		return new phpFunction\iterator();
	}

	protected function start(tokenizer\tokens $tokens)
	{
		$this->name = null;

		return parent::start($tokens);
	}

	protected function appendCurrentToken(tokenizer\tokens $tokens)
	{
		parent::appendCurrentToken($tokens);

		switch ($tokens->current()->getValue())
		{
			case '{':
				++$this->stack;
				break;

			case '}':
				--$this->stack;
				break;
		}

		if ($this->stack === 0)
		{
			$this->stack = null;
			$this->stop();
		}

		return $this;
	}
}

?>
