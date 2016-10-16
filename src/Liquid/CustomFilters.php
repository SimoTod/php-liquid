<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Liquid;

/**
 * A selection of custom filters.
 */
class CustomFilters
{
    public static function sort_key(array $input)
    {
        ksort($input);
        return $input;
    }

    public function asset_url($file)
    {
        return '/assets/'.$file;
    }
    public function global_asset_url($file)
    {
        return '//cdn.shopify.com/s/global/'.$file;
    }

    public function shopify_asset_url($file)
    {
        return '//cdn.shopify.com/s/shopify/'.$file;
    }

    public function stylesheet_tag($file)
    {
        return '<link href="'.$file.'" rel="stylesheet" type="text/css" media="all" />';
    }

    public function script_tag($file)
    {
        return '<script src="'.$file.'" type="text/javascript"></script>';
    }

    public function img_tag($file, $arg)
    {
        return '<img src="'.$file.'" alt="" class="" />';
    }

    public function img_url($file, $size)
    {
        switch ($size) {
            case 'pico':
                $arg = [16,16];
                break;
            case 'icon':
                $arg = [32,32];
                break;
            case 'thumb':
                $arg = [50,50];
                break;
            case 'small':
                $arg = [100,100];
                break;
            case 'compact':
                $arg = [160,160];
                break;
            case 'medium':
                $arg = [240,240];
                break;
            case 'large':
                $arg = [480,480];
                break;
            case 'grande':
                $arg = [600,600];
                break;
            case 'grande':
                $arg = [1024,1024];
                break;
            default:
                $args = explode('x', $size);
        }

        if (count($args) != 2) {
            $args = [1024,1024];
        }

        return 'https://placeholdit.imgix.net/~text?txtsize=20&txt=placeholder&w=480&h=439';
    }

    public function pluralize($items, $singular, $plural)
    {
        return ($items == 1) ? $singular : $plural;
    }

    public function customer_login_link($label)
    {
        return '<a href="#">'.$label.'</a>';
    }

    public function customer_logout_link($label)
    {
        return '<a href="#">'.$label.'</a>';
    }

    public function customer_register_link($label)
    {
        return '<a href="#">'.$label.'</a>';
    }

    public function money($value)
    {
        $locale_info = localeconv();
        $formatted_string = number_format(
            floatval($value),
            $locale_info['frac_digits'],
            $locale_info['decimal_point'],
            $locale_info['thousands_sep']
        );
        if (floatval($value) < 0) {
            $key_prefix = 'n';
        } else {
            $key_prefix = 'p';
        }

        if ($locale_info[$key_prefix.'_cs_precedes']) {
            if ($locale_info[$key_prefix.'_sep_by_space']) {
                $formatted_string = $locale_info['currency_symbol'].' '.$formatted_string;
            } else {
                $formatted_string = $locale_info['currency_symbol'].$formatted_string;
            }
        } else {
            if ($locale_info[$key_prefix.'_sep_by_space']) {
                $formatted_string = $formatted_string.' '.$locale_info['currency_symbol'];
            } else {
                $formatted_string = $formatted_string.$locale_info['currency_symbol'];
            }
        }

        return $formatted_string;
    }
}
