<?php
declare(strict_types=1);
// +----------------------------------------------------------------------
// | CodeEngine
// +----------------------------------------------------------------------
// | Copyright 艾邦
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: TaoGe <liangtao.gz@foxmail.com>
// +----------------------------------------------------------------------
// | Version: 2.0 2020-02-26 14:21
// +----------------------------------------------------------------------

namespace com\log;

/**
 * Class Color
 * @package com
 */
class Log
{

    private array $foreground_colors = array();

    private array $background_colors = array();

    public function __construct()
    {
        // Set up shell colors
        $this->foreground_colors['black']        = '0;30';
        $this->foreground_colors['dark_gray']    = '1;30';
        $this->foreground_colors['blue']         = '0;34';
        $this->foreground_colors['light_blue']   = '1;34';
        $this->foreground_colors['green']        = '0;32';
        $this->foreground_colors['light_green']  = '1;32';
        $this->foreground_colors['cyan']         = '0;36';
        $this->foreground_colors['light_cyan']   = '1;36';
        $this->foreground_colors['red']          = '0;31';
        $this->foreground_colors['light_red']    = '1;31';
        $this->foreground_colors['purple']       = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown']        = '0;33';
        $this->foreground_colors['yellow']       = '1;33';
        $this->foreground_colors['light_gray']   = '0;37';
        $this->foreground_colors['white']        = '1;37';
        $this->background_colors['black']        = '40';
        $this->background_colors['red']          = '41';
        $this->background_colors['green']        = '42';
        $this->background_colors['yellow']       = '43';
        $this->background_colors['blue']         = '44';
        $this->background_colors['magenta']      = '45';
        $this->background_colors['cyan']         = '46';
        $this->background_colors['light_gray']   = '47';
    }

    // Returns colored string
    public function getColoredString($string, $foreground_color = null, $background_color = null): string
    {
        $colored_string = "";
        // Check if given foreground color found
        if (isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
        }

        // Check if given background color found

        if (isset($this->background_colors[$background_color])) {
            $colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
        }

        // Add string and end coloring
        $colored_string .= $string . "\033[0m";
        return empty($colored_string) ? '' : $colored_string . PHP_EOL;
    }

    /**
     * Returns all foreground color names
     * @noinspection PhpUnused
     */
    public function getForegroundColors(): array
    {
        return array_keys($this->foreground_colors);
    }

    /**
     * Returns all background color names
     * @noinspection PhpUnused
     */
    public function getBackgroundColors(): array
    {
        return array_keys($this->background_colors);
    }

    public static function info(string $string)
    {
        $colors = new Log();
        $value  = 'Info    [' . date('Y-m-d H:i:s') . ']  ----  ' . $string;
        echo $colors->getColoredString($value, "light_gray");
    }

    public static function push(string $string)
    {
        $colors = new Log();
        $value  = 'Push    [' . date('Y-m-d H:i:s') . ']  ----  ' . $string;
        echo $colors->getColoredString($value, "purple");
    }

    public static function task(string $string)
    {
        $colors = new Log();
        $value  = 'Task    [' . date('Y-m-d H:i:s') . ']  ----  ' . $string;
        echo $colors->getColoredString($value, "cyan");
    }

    public static function go(string $string)
    {
        $colors = new Log();
        $value  = 'Go      [' . date('Y-m-d H:i:s') . ']  ----  ' . $string;
        echo $colors->getColoredString($value, "brown");
    }

    public static function success(string $string)
    {
        $colors = new Log();
        $value  = 'Success [' . date('Y-m-d H:i:s') . ']  ----  ' . $string;
        echo $colors->getColoredString($value, "green");
    }

    public static function warning(string $string)
    {
        $colors = new Log();
        $value  = 'Warning [' . date('Y-m-d H:i:s') . ']  ----  ' . $string;
        echo $colors->getColoredString($value, "yellow");
    }

    public static function error(string $string)
    {
        $value  = 'Error   [' . date('Y-m-d H:i:s') . ']  ----  ' . $string;
        $colors = new Log();
        echo $colors->getColoredString($value, "red");
    }
}
