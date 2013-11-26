<?php

namespace Mist\Log\_1_0;

trait Interpolate
{
    /**
     * Interpolates context values into the message placeholders.
     *
     * @var string $message Subject
     * @var string $context Replacment values
     *
     * @return string
     */
    protected function interpolate($message, array $context = array())
    {
        if (count($context) < 1 || strpos($message, '{') === false) {
            return $message;
        }

        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            if (is_string($val)) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}
