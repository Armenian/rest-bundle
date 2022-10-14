<?php

declare(strict_types=1);


namespace DMP\RestBundle\Tests\Lib;

use JsonException;

class JsonHelper
{

    /**
     * @throws JsonException
     */
    public static function decode(string $rawString): array
    {
        return json_decode($rawString, true, 512, JSON_THROW_ON_ERROR);
    }


    /**
     * @throws JsonException
     */
    public static function encode(array $array): string
    {
        return json_encode($array, JSON_THROW_ON_ERROR, 512);
    }
}

