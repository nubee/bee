<?php

abstract class nbOutput
{

    protected $formatter;

    abstract function write($text);

    public function __construct()
    {
        $this->formatter = new nbFormatter();
    }

    public function setFormatter(nbFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

}