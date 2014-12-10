<?php

class CloudloaderException extends Exception
{
    public $field;

    public function __construct($message, $field)
    {
        parent::__construct($message);
        $this->field = $field;
    }
}

?>