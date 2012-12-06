<?php

class nbStreamOutput extends nbOutput
{

    private $stream = '';

    public function write($text)
    {
        $this->stream .= $this->formatter->format($text);
    }

    public function getStream()
    {
        $stream = $this->stream;
        $this->stream = '';
        return $stream;
    }

}