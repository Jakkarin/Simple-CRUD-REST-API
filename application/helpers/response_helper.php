<?php

if ( ! function_exists('responseJSON'))
{
    function responseJSON($outputClass, $data)
    {
        return $outputClass
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}

if ( ! function_exists('responseJSONError'))
{
    function responseJSONError($outputClass, $code, $data)
    {
        return $outputClass
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}