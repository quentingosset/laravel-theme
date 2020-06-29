<?php

namespace Nwidart\Themes\Exceptions;

class InvalidAssetPath extends \Exception
{
    public static function missingThemeName($asset)
    {
        return new static("Theme name was not specified in asset [$asset].");
    }
}
