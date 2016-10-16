# Liquid template engine for PHP

Liquid is a PHP port of the [Liquid template engine for Ruby](https://github.com/Shopify/liquid), which was written by Tobias Lutke. Although there are many other templating engines for PHP, including Smarty (from which Liquid was partially inspired), Liquid had some advantages that made porting worthwhile:

 * Readable and human friendly syntax, that is usable in any type of document, not just html, without need for escaping.
 * Quick and easy to use and maintain.
 * 100% secure, no possibility of embedding PHP code.
 * Clean OO design, rather than the mix of OO and procedural found in other templating engines.
 * Seperate compiling and rendering stages for improved performance.
 * Easy to extend with your own "tags and filters":https://github.com/harrydeluxe/php-liquid/wiki/Liquid-for-programmers.
 * 100% Markup compatibility with a Ruby templating engine, making templates usable for either.
 * Unit tested: Liquid is fully unit-tested. The library is stable and ready to be used in large projects.

This fork is based on [php-liquid](https://github.com/kalimatas/php-liquid) by Guz Alexander.

## Installing

You can install this lib via [composer](https://getcomposer.org/):

Add the following repository to your composer Json

    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:SimoTod/php-liquid.git"
        }
    ],

And install the package

    composer require liquid/liquid

## Example template

	{% if products %}
		<ul id="products">
		{% for product in products %}
		  <li>
			<h2>{{ product.name }}</h2>
			Only {{ product.price | price }}

			{{ product.description | prettyprint | paragraph }}

			{{ 'it rocks!' | paragraph }}

		  </li>
		{% endfor %}
		</ul>
	{% endif %}

## How to use Liquid

The main class is `Liquid::Template` class. There are two separate stages of working with Liquid templates: parsing and rendering. Here is a simple example:

    use Liquid\Template;

    $template = new Template();
    $template->parse("Hello, {{ name }}!");
    echo $template->render(array('name' => 'Alex');

	// Will echo
	// Hello, Alex!

To find more examples have a look at the `examples` directory or at the original Ruby implementation repository's [wiki page](https://github.com/Shopify/liquid/wiki).

## Requirements

 * PHP 5.3+

## Issues

Have a bug? Please create an issue here on GitHub!

[https://github.com/SimoTod/php-liquid/issues](https://github.com/kalimatas/php-liquid/issues)
