<?php

if (!function_exists('decimal_a_hora')) {
    function decimal_a_hora($decimal)
    {
        $hours = floor($decimal);
        $minutes = round(($decimal - $hours) * 60);

        return "{$hours}hrs {$minutes}min";
    }
}
