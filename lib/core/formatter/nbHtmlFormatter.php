<?php

class nbHtmlFormatter extends nbFormatter
{

    public function format($message)
    {
        $messagWithNewLines = parent::format($message);

        return preg_replace('/\r\n|\n|\r/', '<br />', $messagWithNewLines);
    }

}
