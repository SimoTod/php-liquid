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

    /**
     * Concatenates (combines) an array with another array.
     * The resulting array contains all the elements of the original arrays.
     * concat will not remove duplicate entries from the concatenated array unless
     * you also use the uniq filter.
     * (https://help.shopify.com/themes/liquid/filters/array-filters#concat)
     *
     * @param array $input1
     * @param array $input1
     *
     * @return array
     */
    public static function concat(array $input1, array $input2)
    {
        return $input1 + $input2;
    }

    /**
     * Returns the item at the specified index location in an array.
     * Note that array numbering starts from zero, so the first item in an array
     * is referenced with [0]
     * (https://help.shopify.com/themes/liquid/filters/array-filters#index)
     *
     * @param array $input
     * @param int $pos
     *
     * @return mixed
     */
    public static function index(array $input, $pos)
    {
        return $input[intval($pos)];
    }

    /**
     * Generates a stylesheet tag.
     * (https://help.shopify.com/themes/liquid/filters/html-filters#stylesheet_tag)
     *
     * @param string $file
     *
     * @return string
     */
    public static function stylesheet_tag($file)
    {
        return '<link href="'.$file.'" rel="stylesheet" type="text/css" media="all" />';
    }

    /**
     * Generates a script tag.
     * (https://help.shopify.com/themes/liquid/filters/html-filters#script_tag)
     *
     * @param string $file
     *
     * @return string
     */
    public static function script_tag($file)
    {
        return '<script src="'.$file.'" type="text/javascript"></script>';
    }

    /**
     * Generates a img tag.
     * (https://help.shopify.com/themes/liquid/filters/html-filters#img_tag)
     *
     * @param string $file
     *
     * @return string
     */
    public static function img_tag($file, $arg)
    {
        return '<img src="'.$file.'" alt="" class="" />';
    }

    /**
     * Formats the price based on the local settings (with currency).
     * (https://help.shopify.com/themes/liquid/filters/money-filters#money)
     *
     * @param string|float $value
     *
     * @return string
     */
    public static function money($value)
    {
        return CustomFilters::money_with_currency($value);
    }

    /**
     * Formats the price based on the local settings (with currency).
     * (https://help.shopify.com/themes/liquid/filters/money-filters#money_with_currency)
     *
     * @param string|float $value
     *
     * @return string
     */
    public static function money_with_currency($value)
    {
        $formatted_string = CustomFilters::money_without_currency($value);

        $locale_info = localeconv();
        $key_prefix = floatval($value) < 0 ? 'n' : 'p';
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

    /**
     * Formats the price based on the local settings (with currency).
     * The input value is without decimal separator.
     * (https://help.shopify.com/themes/liquid/filters/money-filters#money_without_trailing_zeros)
     *
     * @param string|float $value
     *
     * @return string
     */
    public static function money_without_trailing_zeros($value)
    {
        $locale_info = localeconv();
        $value = floatval($value) / (10 * $locale_info['frac_digits']);
        return CustomFilters::money_with_currency($value);
    }

    /**
     * Formats the price based on the local settings (without currency).
     * (https://help.shopify.com/themes/liquid/filters/money-filters#money_without_currency)
     *
     * @param string|float $value
     *
     * @return string
     */
    public static function money_without_currency($value)
    {
        $locale_info = localeconv();
        $formatted_string = number_format(
            floatval($value),
            $locale_info['frac_digits'],
            $locale_info['decimal_point'],
            $locale_info['thousands_sep']
        );
        return $formatted_string;
    }

    /**
     * Converts a string into CamelCase.
     * (https://help.shopify.com/themes/liquid/filters/string-filters#camelcase)
     *
     * @param string $value
     *
     * @return string
     */
    public static function camel_case($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }

    /**
     * Converts a string into a handle.
     * (https://help.shopify.com/themes/liquid/filters/string-filters#handle-handleize)
     *
     * @param string $value
     *
     * @return string
     */
    public static function handle($value)
    {
        // Convert non ASCII chars
        foreach (static::$charsArray as $key => $val) {
            $value = str_replace($val, $key, $value);
        }
        $value = preg_replace('/[^\x20-\x7E]/u', '', $value);

        // Convert all dashes/underscores into separator
        $value = preg_replace('!['.preg_quote('_').']+!u', '-', $value);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $value = preg_replace('![^'.preg_quote('-').'\pL\pN\s]+!u', '', mb_strtolower($value));

        // Replace all separator characters and whitespace by a single separator
        $value = preg_replace('!['.preg_quote('-').'\s]+!u', '-', $value);

        return trim($value, '-');
    }

    /**
     * Converts a string into a handle.
     * (https://help.shopify.com/themes/liquid/filters/string-filters#handle-handleize)
     *
     * @param string $value
     *
     * @return string
     */
    public static function handleize($value)
    {
        return CustomFilters::handle($value);
    }

    /**
     * Converts a string into an MD5 hash.
     * (https://help.shopify.com/themes/liquid/filters/string-filters#md5)
     *
     * @param string $value
     *
     * @return string
     */
    public static function md5($value)
    {
        return hash('md5', $value);
    }

    /**
     * Converts a string into an SHA-1 hash.
     * (https://help.shopify.com/themes/liquid/filters/string-filters#sha1)
     *
     * @param string $value
     *
     * @return string
     */
    public static function sha1($value)
    {
        return hash('sha1', $value);
    }

    /**
     * Converts a string into an SHA-256 hash.
     * (https://help.shopify.com/themes/liquid/filters/string-filters#sha256)
     *
     * @param string $value
     *
     * @return string
     */
    public static function sha256($value)
    {
        return hash('sha256', $value);
    }

    /**
     * Converts a string into a SHA-1 hash using a hash message authentication code (HMAC).
     * (https://help.shopify.com/themes/liquid/filters/string-filters#hmac_sha1)
     *
     * @param string $value
     * @param string $secret
     *
     * @return string
     */
    public static function hmac_sha1($value, $secret)
    {
        return hash_hmac('sha1', $value, $secret);
    }

    /**
     * Converts a string into a SHA-256 hash using a hash message authentication code (HMAC).
     * (https://help.shopify.com/themes/liquid/filters/string-filters#hmac_sha256)
     *
     * @param string $value
     * @param string $secret
     *
     * @return string
     */
    public static function hmac_sha256($value, $secret)
    {
        return hash_hmac('sha256', $value, $secret);
    }

    /**
     * Outputs the singular or plural version of a string based on the value of a number.
     * The first parameter is the singular string and the second parameter is the plural string.
     *
     * @param array $items
     * @param string $singular
     * @param string $plural
     *
     * @return string
     */
    public static function pluralize($items, $singular, $plural)
    {
        return ($items == 1) ? $singular : $plural;
    }

    /**
     * Identifies all characters in a string that are not allowed in URLS,
     * and replaces the characters with their escaped variants.
     * (https://help.shopify.com/themes/liquid/filters/string-filters#url_escape)
     *
     * @param string $value
     *
     * @return string
     */
    public static function url_escape($value)
    {
        return str_replace('%26', '&', rawurlencode($value));
    }

    /**
     * Replaces all characters in a string that are not allowed in URLs with their escaped variants,
     * including the ampersand (&).
     * (https://help.shopify.com/themes/liquid/filters/string-filters#url_param_escape)
     *
     * @param string $value
     *
     * @return string
     */
    public static function url_param_escape($value)
    {
        return rawurlencode($value);
    }

    /**
     * Returns the URL of a file in the "assets" folder of a theme.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#asset_url)
     *
     * @param string $file
     *
     * @return string
     */
    public static function asset_url($file)
    {
        return '/assets/'.$file;
    }

    /**
     * Returns the asset URL of an image in the "assets" folder of a theme.
     * asset_image_url accepts an image size parameter.
     * N.B. The image size parameter is not considered (@TODO).
     * (https://help.shopify.com/themes/liquid/filters/url-filters#asset_img_url)
     *
     * @param string $file
     *
     * @return string
     */
    public static function asset_img_url($file)
    {
        return '/assets/'.$file;
    }

    /**
     * Returns the URL of a file in the Files page of the admin.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#file_url)
     *
     * @param string $file
     *
     * @return string
     */
    public static function file_url($file)
    {
        return '/files/'.$file;
    }

    /**
     * Returns the asset URL of an image in the Files page of the admin.
     * file_image_url accepts an image size parameter.
     * N.B. The image size parameter is not considered (@TODO).
     * (https://help.shopify.com/themes/liquid/filters/url-filters#file_image_url)
     *
     * @param string $file
     *
     * @return string
     */
    public static function file_image_url($file, $size)
    {
        return '/files/'.$file;
    }

    /**
     * Returns the URL of an image. Accepts image size parameters.
     * N.B. This function returns a placeholder with the right size, not the real image.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#img_url)
     *
     * @param mixed $object
     * @param string $size
     *
     * @return string
     */
    public static function img_url($object, $size)
    {
        switch ($size) {
            case 'pico':
                $args = [16,16];
                break;
            case 'icon':
                $args = [32,32];
                break;
            case 'thumb':
                $args = [50,50];
                break;
            case 'small':
                $args = [100,100];
                break;
            case 'compact':
                $args = [160,160];
                break;
            case 'medium':
                $args = [240,240];
                break;
            case 'large':
                $args = [480,480];
                break;
            case 'grande':
                $args = [600,600];
                break;
            case 'grande':
                $args = [1024,1024];
                break;
            default:
                $args = explode('x', $size);
        }

        if (count($args) != 2) {
            $args = [1024,1024];
        }

        return 'https://placeholdit.imgix.net/~text?txtsize=20&txt=placeholder&w=480&h=439';
    }

    /**
     * Returns the URL of a global asset. Global assets are kept in a directory on Shopify's servers.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#global_asset_url)
     *
     * @param string $file
     *
     * @return string
     */
    public static function global_asset_url($file)
    {
        return '//cdn.shopify.com/s/global/'.$file;
    }

    /**
     * Returns the URL of a global assets that are found on Shopify's servers.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#shopify_asset_url)
     *
     * @param string $file
     *
     * @return string
     */
    public static function shopify_asset_url($file)
    {
        return '//cdn.shopify.com/s/shopify/'.$file;
    }

    /**
     * Generates a link to the customer login page.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#customer_login_link)
     *
     * @param string $label
     *
     * @return string
     */
    public static function customer_login_link($label)
    {
        return '<a href="/account/login" id="customer_login_link">'.$label.'</a>';
    }

    /**
     * Generates a link to the customer logout page.
     * N.B. The link is empty as we are not inside shopify, but it won't break the layout.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#customer_login_link)
     *
     * @param string $label
     *
     * @return string
     */
    public static function customer_logout_link($label)
    {
        return '<a href="#">'.$label.'</a>';
    }

    /**
     * Generates a link to the customer register page.
     * N.B. The link is empty as we are not inside shopify, but it won't break the layout.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#customer_register_link)
     *
     * @param string $label
     *
     * @return string
     */
    public static function customer_register_link($label)
    {
        return '<a href="#">'.$label.'</a>';
    }

    /**
     * Generates an HTML link. The first parameter is the URL of the link,
     * and the optional second parameter is the title of the link.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#link_to)
     *
     * @param string $label
     *
     * @return string
     */
    public static function link_to($url, $label)
    {
        return '<a href="'.$url.'">'.$label.'</a>';
    }

    /**
     * Creates an HTML link to a collection page that lists all products belonging to a vendor.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#link_to_vendor)
     *
     * @param string $vendor
     * @param string $label
     *
     * @return string
     */
    public static function link_to_vendor($vendor, $label)
    {
        return '<a href="'.CustomFilters::url_for_vendor($vendor).'">'.$label.'</a>';
    }

    /**
     * Creates an HTML link to a collection page that lists all products belonging to a product type.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#link_to_type)
     *
     * @param string $type
     * @param string $label
     *
     * @return string
     */
    public static function link_to_type($type, $label)
    {
        return '<a href="'.CustomFilters::url_for_type($type).'">'.$label.'</a>';
    }

    /**
     * Creates a link to all products in a collection that have a given tag.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#link_to_tag)
     *
     * @param string $tag
     *
     * @return string
     */
    public static function link_to_tag($tag)
    {
        return '<a href="/frontpage/'.rawurlencode($tag).'">'.$tag.'</a>';
    }

    /**
     * Creates a link to all products in a collection that have a given tag
     * as well as any tags that have been already selected.
     * @TODO The link is not valid but it won't break the layout.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#link_to_type)
     *
     * @param string $tag
     *
     * @return string
     */
    public static function link_to_add_tag($tag)
    {
        return CustomFilters::link_to_tag($tag);
    }

    /**
     * Generates a link to all products in a collection that have the given tag
     * and all the previous tags that might have been added already.
     * @TODO The link is not valid but it won't break the layout.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#link_to_remove_tag)
     *
     * @param string $tag
     *
     * @return string
     */
    public static function link_to_remove_tag($tag)
    {
        return CustomFilters::link_to_tag($tag);
    }

    /**
     * Returns the URL of the payment type's SVG image.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#payment_type_img_url)
     *
     * @param string $payment_type
     *
     * @return string
     */
    public static function payment_type_img_url($payment_type)
    {
        $valid_values = ['visa', 'master', 'american_express', 'paypal', 'jcb', 'diners_club', 'maestro', 'discover', 'dankort', 'forbrugsforeningen', 'dwolla', 'bitcoin', 'dodgecoin', 'litecoin'];
        if (!in_array($payment_type, $valid_values)) {
            throw new RuntimeException($payment_type. " is not a valid payment type.");
        }
        return '//cdn.shopify.com/s/global/payment_types/creditcards_'.$payment_type.'.svg';
    }

    /**
     * Returns the collection image's URL. Accepts image size parameters.
     * N.B. This function returns a placeholder with the right size, not the real image.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#product_img_url)
     *
     * @param mixed $object
     * @param string $size
     *
     * @return string
     */
    public static function product_img_url($object, $size)
    {
        return CustomFilters::img_url($object, $size);
    }

    /**
     * Returns the collection image's URL. Accepts image size parameters.
     * N.B. This function returns a placeholder with the right size, not the real image.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#collection_img_url)
     *
     * @param mixed $object
     * @param string $size
     *
     * @return string
     */
    public static function collection_img_url($object, $size)
    {
        return CustomFilters::img_url($object, $size);
    }

    /**
     * Creates a URL that links to a collection page containing products with a specific product vendor.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#url_for_vendor)
     *
     * @param string $vendor
     * @param string $label
     *
     * @return string
     */
    public static function url_for_vendor($vendor)
    {
        return '/collections/vendors?q='.rawurlencode($vendor);
    }

    /**
     * Creates a URL that links to a collection page containing products with a specific product type.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#url_for_type)
     *
     * @param string $type
     * @param string $label
     *
     * @return string
     */
    public static function url_for_type($type)
    {
        return '/collections/types?q='.rawurlencode($type);
    }

    /**
     * Creates a collection-aware product URL.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#within)
     *
     * @param string $url
     *
     * @return string
     */
    public static function within($url)
    {
        return $url;
    }

    /**
     * Converts a timestamp into another date format.
     * (https://help.shopify.com/themes/liquid/filters/additional-filters#date)
     *
     * @param string $timestamp
     * @param string $format
     *
     * @return string
     */
    public static function date($timestamp, $format)
    {
        return strftime($format, $timestamp);
    }

    /**
     * The time_tag filter converts a timestamp into an HTML <time> tag:
     * (https://help.shopify.com/themes/liquid/filters/additional-filters#time_tag)
     *
     * @param string $timestamp
     * @param string $format
     *
     * @return string
     */
    public static function time_tag($timestamp, $format = false)
    {
        if (!$format) {
            $format = 'c';
        }
        return '<time datetime="'.date('c', $timestamp).'">'.date($format, $timestamp).'</time>';
    }

    /**
     * Convert hex value to RGBA. It defaults to an opacity of 1,
     * but you can specify an opacity between 0 and 1.
     * Shorthand hex values are accepted as well, for example, #812.
     * (https://help.shopify.com/themes/liquid/filters/additional-filters#hex_to_rgba)
     *
     * @param string $color
     * @param float $opacity
     *
     * @return string
     */
    public static function hex_to_rgba($color, $opacity = 1)
    {
        $default = 'rgb(0,0,0)';

        $opacity = floatval($opacity);
        if ($opacity>1) {
            $opacity = 1;
        }

        //Sanitize $color if "#" is provided
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if ($opacity) {
            if (abs($opacity) > 1) {
                $opacity = 1.0;
            }
            $output = 'rgba('.implode(",", $rgb).','.$opacity.')';
        } else {
            $output = 'rgb('.implode(",", $rgb).')';
        }

        //Return rgb(a) color string
        return $output;
    }

    /**
     * Wraps words inside search results with an HTML <strong> tag with the class highlight
     * if it matches the search term.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#highlight)
     *
     * @param string $snippet
     * @param string $term
     *
     * @return string
     */
    public static function highlight($snippet, $term)
    {
        return str_replace($term, '<strong>'.$term.'<strong>', $snippet);
    }

    /**
     * Wraps a tag link in a <span> with the class active if that tag is being used to filter a collection.
     * @TODO The link is not valid but it won't break the layout.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#highlight_active_tag)
     *
     * @param string $tag
     *
     * @return string
     */
    public static function highlight_active_tag($tag)
    {
        return CustomFilters::link_to_tag($tag);
    }

    /**
     * Converts a string into JSON format.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#json)
     *
     * @param mixed $object
     *
     * @return string
     */
    public static function json($object)
    {
        return json_encode($object);
    }

    /**
     * Formats the product variant's weight.
     * (https://help.shopify.com/themes/liquid/filters/url-filters#weight_unit)
     *
     * @param float $weight
     * @param string $weight_unit
     *
     * @return string
     */
    public static function weight_with_unit($weight, $weight_unit = 'lb')
    {
        return floatval($weight).' '.$weight_unit;
    }

    public static $charsArray = [
        '0'    => ['°', '₀', '۰'],
        '1'    => ['¹', '₁', '۱'],
        '2'    => ['²', '₂', '۲'],
        '3'    => ['³', '₃', '۳'],
        '4'    => ['⁴', '₄', '۴', '٤'],
        '5'    => ['⁵', '₅', '۵', '٥'],
        '6'    => ['⁶', '₆', '۶', '٦'],
        '7'    => ['⁷', '₇', '۷'],
        '8'    => ['⁸', '₈', '۸'],
        '9'    => ['⁹', '₉', '۹'],
        'a'    => ['à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'ā', 'ą', 'å', 'α', 'ά', 'ἀ', 'ἁ', 'ἂ', 'ἃ', 'ἄ', 'ἅ', 'ἆ', 'ἇ', 'ᾀ', 'ᾁ', 'ᾂ', 'ᾃ', 'ᾄ', 'ᾅ', 'ᾆ', 'ᾇ', 'ὰ', 'ά', 'ᾰ', 'ᾱ', 'ᾲ', 'ᾳ', 'ᾴ', 'ᾶ', 'ᾷ', 'а', 'أ', 'အ', 'ာ', 'ါ', 'ǻ', 'ǎ', 'ª', 'ა', 'अ', 'ا'],
        'b'    => ['б', 'β', 'Ъ', 'Ь', 'ب', 'ဗ', 'ბ'],
        'c'    => ['ç', 'ć', 'č', 'ĉ', 'ċ'],
        'd'    => ['ď', 'ð', 'đ', 'ƌ', 'ȡ', 'ɖ', 'ɗ', 'ᵭ', 'ᶁ', 'ᶑ', 'д', 'δ', 'د', 'ض', 'ဍ', 'ဒ', 'დ'],
        'e'    => ['é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'ë', 'ē', 'ę', 'ě', 'ĕ', 'ė', 'ε', 'έ', 'ἐ', 'ἑ', 'ἒ', 'ἓ', 'ἔ', 'ἕ', 'ὲ', 'έ', 'е', 'ё', 'э', 'є', 'ə', 'ဧ', 'ေ', 'ဲ', 'ე', 'ए', 'إ', 'ئ'],
        'f'    => ['ф', 'φ', 'ف', 'ƒ', 'ფ'],
        'g'    => ['ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ဂ', 'გ', 'گ'],
        'h'    => ['ĥ', 'ħ', 'η', 'ή', 'ح', 'ه', 'ဟ', 'ှ', 'ჰ'],
        'i'    => ['í', 'ì', 'ỉ', 'ĩ', 'ị', 'î', 'ï', 'ī', 'ĭ', 'į', 'ı', 'ι', 'ί', 'ϊ', 'ΐ', 'ἰ', 'ἱ', 'ἲ', 'ἳ', 'ἴ', 'ἵ', 'ἶ', 'ἷ', 'ὶ', 'ί', 'ῐ', 'ῑ', 'ῒ', 'ΐ', 'ῖ', 'ῗ', 'і', 'ї', 'и', 'ဣ', 'ိ', 'ီ', 'ည်', 'ǐ', 'ი', 'इ'],
        'j'    => ['ĵ', 'ј', 'Ј', 'ჯ', 'ج'],
        'k'    => ['ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك', 'က', 'კ', 'ქ', 'ک'],
        'l'    => ['ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل', 'လ', 'ლ'],
        'm'    => ['м', 'μ', 'م', 'မ', 'მ'],
        'n'    => ['ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن', 'န', 'ნ'],
        'o'    => ['ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ø', 'ō', 'ő', 'ŏ', 'ο', 'ὀ', 'ὁ', 'ὂ', 'ὃ', 'ὄ', 'ὅ', 'ὸ', 'ό', 'о', 'و', 'θ', 'ို', 'ǒ', 'ǿ', 'º', 'ო', 'ओ'],
        'p'    => ['п', 'π', 'ပ', 'პ', 'پ'],
        'q'    => ['ყ'],
        'r'    => ['ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر', 'რ'],
        's'    => ['ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص', 'စ', 'ſ', 'ს'],
        't'    => ['ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط', 'ဋ', 'တ', 'ŧ', 'თ', 'ტ'],
        'u'    => ['ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'û', 'ū', 'ů', 'ű', 'ŭ', 'ų', 'µ', 'у', 'ဉ', 'ု', 'ူ', 'ǔ', 'ǖ', 'ǘ', 'ǚ', 'ǜ', 'უ', 'उ'],
        'v'    => ['в', 'ვ', 'ϐ'],
        'w'    => ['ŵ', 'ω', 'ώ', 'ဝ', 'ွ'],
        'x'    => ['χ', 'ξ'],
        'y'    => ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'ÿ', 'ŷ', 'й', 'ы', 'υ', 'ϋ', 'ύ', 'ΰ', 'ي', 'ယ'],
        'z'    => ['ź', 'ž', 'ż', 'з', 'ζ', 'ز', 'ဇ', 'ზ'],
        'aa'   => ['ع', 'आ', 'آ'],
        'ae'   => ['ä', 'æ', 'ǽ'],
        'ai'   => ['ऐ'],
        'at'   => ['@'],
        'ch'   => ['ч', 'ჩ', 'ჭ', 'چ'],
        'dj'   => ['ђ', 'đ'],
        'dz'   => ['џ', 'ძ'],
        'ei'   => ['ऍ'],
        'gh'   => ['غ', 'ღ'],
        'ii'   => ['ई'],
        'ij'   => ['ĳ'],
        'kh'   => ['х', 'خ', 'ხ'],
        'lj'   => ['љ'],
        'nj'   => ['њ'],
        'oe'   => ['ö', 'œ', 'ؤ'],
        'oi'   => ['ऑ'],
        'oii'  => ['ऒ'],
        'ps'   => ['ψ'],
        'sh'   => ['ш', 'შ', 'ش'],
        'shch' => ['щ'],
        'ss'   => ['ß'],
        'sx'   => ['ŝ'],
        'th'   => ['þ', 'ϑ', 'ث', 'ذ', 'ظ'],
        'ts'   => ['ц', 'ც', 'წ'],
        'ue'   => ['ü'],
        'uu'   => ['ऊ'],
        'ya'   => ['я'],
        'yu'   => ['ю'],
        'zh'   => ['ж', 'ჟ', 'ژ'],
        '(c)'  => ['©'],
        'A'    => ['Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'Å', 'Ā', 'Ą', 'Α', 'Ά', 'Ἀ', 'Ἁ', 'Ἂ', 'Ἃ', 'Ἄ', 'Ἅ', 'Ἆ', 'Ἇ', 'ᾈ', 'ᾉ', 'ᾊ', 'ᾋ', 'ᾌ', 'ᾍ', 'ᾎ', 'ᾏ', 'Ᾰ', 'Ᾱ', 'Ὰ', 'Ά', 'ᾼ', 'А', 'Ǻ', 'Ǎ'],
        'B'    => ['Б', 'Β', 'ब'],
        'C'    => ['Ç', 'Ć', 'Č', 'Ĉ', 'Ċ'],
        'D'    => ['Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ'],
        'E'    => ['É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'Ë', 'Ē', 'Ę', 'Ě', 'Ĕ', 'Ė', 'Ε', 'Έ', 'Ἐ', 'Ἑ', 'Ἒ', 'Ἓ', 'Ἔ', 'Ἕ', 'Έ', 'Ὲ', 'Е', 'Ё', 'Э', 'Є', 'Ə'],
        'F'    => ['Ф', 'Φ'],
        'G'    => ['Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ'],
        'H'    => ['Η', 'Ή', 'Ħ'],
        'I'    => ['Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Î', 'Ï', 'Ī', 'Ĭ', 'Į', 'İ', 'Ι', 'Ί', 'Ϊ', 'Ἰ', 'Ἱ', 'Ἳ', 'Ἴ', 'Ἵ', 'Ἶ', 'Ἷ', 'Ῐ', 'Ῑ', 'Ὶ', 'Ί', 'И', 'І', 'Ї', 'Ǐ', 'ϒ'],
        'K'    => ['К', 'Κ'],
        'L'    => ['Ĺ', 'Ł', 'Л', 'Λ', 'Ļ', 'Ľ', 'Ŀ', 'ल'],
        'M'    => ['М', 'Μ'],
        'N'    => ['Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν'],
        'O'    => ['Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ø', 'Ō', 'Ő', 'Ŏ', 'Ο', 'Ό', 'Ὀ', 'Ὁ', 'Ὂ', 'Ὃ', 'Ὄ', 'Ὅ', 'Ὸ', 'Ό', 'О', 'Θ', 'Ө', 'Ǒ', 'Ǿ'],
        'P'    => ['П', 'Π'],
        'R'    => ['Ř', 'Ŕ', 'Р', 'Ρ', 'Ŗ'],
        'S'    => ['Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ'],
        'T'    => ['Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ'],
        'U'    => ['Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'Û', 'Ū', 'Ů', 'Ű', 'Ŭ', 'Ų', 'У', 'Ǔ', 'Ǖ', 'Ǘ', 'Ǚ', 'Ǜ'],
        'V'    => ['В'],
        'W'    => ['Ω', 'Ώ', 'Ŵ'],
        'X'    => ['Χ', 'Ξ'],
        'Y'    => ['Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Ÿ', 'Ῠ', 'Ῡ', 'Ὺ', 'Ύ', 'Ы', 'Й', 'Υ', 'Ϋ', 'Ŷ'],
        'Z'    => ['Ź', 'Ž', 'Ż', 'З', 'Ζ'],
        'AE'   => ['Ä', 'Æ', 'Ǽ'],
        'CH'   => ['Ч'],
        'DJ'   => ['Ђ'],
        'DZ'   => ['Џ'],
        'GX'   => ['Ĝ'],
        'HX'   => ['Ĥ'],
        'IJ'   => ['Ĳ'],
        'JX'   => ['Ĵ'],
        'KH'   => ['Х'],
        'LJ'   => ['Љ'],
        'NJ'   => ['Њ'],
        'OE'   => ['Ö', 'Œ'],
        'PS'   => ['Ψ'],
        'SH'   => ['Ш'],
        'SHCH' => ['Щ'],
        'SS'   => ['ẞ'],
        'TH'   => ['Þ'],
        'TS'   => ['Ц'],
        'UE'   => ['Ü'],
        'YA'   => ['Я'],
        'YU'   => ['Ю'],
        'ZH'   => ['Ж'],
        ' '    => ["\xC2\xA0", "\xE2\x80\x80", "\xE2\x80\x81", "\xE2\x80\x82", "\xE2\x80\x83", "\xE2\x80\x84", "\xE2\x80\x85", "\xE2\x80\x86", "\xE2\x80\x87", "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A", "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80"],
    ];
}
