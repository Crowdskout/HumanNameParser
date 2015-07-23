<?php
namespace Elite50\HumanNameParser\Exceptions;

/**
 * Class CannotParseException
 * @package Elite50\HumanNameParser\Exceptions
 */
class CannotParseException extends \Exception
{
    /**
     * @var bool|string
     */
    public $name;

    /**
     * @param string $message
     * @param bool|string $name
     */
    public function __construct($message = '', $name = false)
    {
        $this->name = $name;

        parent::__construct($message);
    }
}
