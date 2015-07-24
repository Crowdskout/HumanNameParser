<?php
namespace Elite50\HumanNameParser;

use Elite50\HumanNameParser\Exceptions\CannotParseException;
use Elite50\HumanNameParser\Exceptions\InvalidStringException;

/**
 * Does cutting and matching stuff with a name string.
 * Note that the string has to be UTF8-encoded.
 */
class Name {
	private $string;

	function __construct($string)
	{
		$this->setString($string);
	}

	/**
     * Set String.
     *
	 * Checks encoding, normalizes whitespace/punctuation, and sets the name string
	 *
	 * @param string $string a UTF8-encoded string
	 *
	 * @return bool True on success
	 *
	 * @throws InvalidStringException
	 */
	public function setString($string)
	{
		if (!mb_check_encoding($string)) {
			throw new InvalidStringException("Name is not encoded in UTF-8");
		}

		$this->string = $string;
		$this->norm();

		return true;
	}

    /**
     * Get String.
     *
     * @return string
     */
	public function getString()
	{
		return $this->string;
	}


	/**
	 * Chop With Regex.
	 *
	 * Uses a regex to chop off and return part of the name string
	 * There are two parts: first, it returns the matched substring,
	 * and then it removes that substring from $this->string and normalizes.
	 *
	 * @param string $regex matches the part of the name string to chop off
	 * @param int $submatchIndex which of the parenthesized submatches to use
	 * @param string $regexFlags optional regex flags
	 *
	 * @return string the part of the name string that got chopped off
	 *
	 * @throws CannotParseException
	 */
	public function chopWithRegex($regex, $submatchIndex = 0, $regexFlags = '')
	{
		$regex = $regex . "ui" . $regexFlags; // unicode + case-insensitive
		preg_match($regex, $this->string, $m);
		$subset = (isset($m[$submatchIndex])) ? $m[$submatchIndex] : '';

		if ($subset){
			$this->string = preg_replace($regex, ' ', $this->string, -1, $numReplacements);
			if ($numReplacements > 1){
				throw new CannotParseException("The regex being used to find the name has multiple matches.", $this->string);
			}
			$this->norm();
			return $subset;
		}
		else {
			return '';
		}
	}

	/**
	 * Flip.
     *
     * Flips the front and back parts of a name with one another
     * Front and back are determined by a specified character somewhere in the
     * middle of the string.
     *
     * @param  string $flipAroundChar The character(s) demarcating the two halves you want to flip.
     * @return bool True on success.
     *
     * @throws CannotParseException
     */
	public function flip($flipAroundChar)
	{
		$subStrings = preg_split("/$flipAroundChar/u", $this->string);

		if (count($subStrings) == 2) {
			$this->string = $subStrings[1] . " " . $subStrings[0];
			$this->norm();
		} elseif (count($subStrings) > 2) {
			throw new CannotParseException("Can't flip around multiple '$flipAroundChar' characters in name string.", $this->string);
		}

		return true; // if there's 1 or 0 $flipAroundChar found
	}

	/**
     * Norm.
     *
	 * Removes extra whitespace and punctuation from $this->string
	 * Strips whitespace chars from ends, strips redundant whitespace, converts whitespace chars to " ".
	 *
	 * @return bool True on success
	 */
	private function norm()
	{
		$this->string = preg_replace( "#^\s*#u", "", $this->string );
		$this->string = preg_replace( "#\s*$#u", "", $this->string );
		$this->string = preg_replace( "#\s+#u", " ", $this->string );
		$this->string = preg_replace( "#,$#u", " ", $this->string );
		return true;
	}
}