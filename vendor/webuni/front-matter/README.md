Front Matter
============

[![Packagist](https://img.shields.io/packagist/v/webuni/front-matter.svg?style=flat-square)](https://packagist.org/packages/webuni/front-matter)
[![Build Status](https://img.shields.io/github/workflow/status/webuni/front-matter/Tests/master.svg?style=flat-square)](https://github.com/webuni/front-matter/actions?query=workflow%3ATests+branch%3Amaster)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webuni/front-matter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webuni/front-matter/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/webuni/front-matter/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/webuni/front-matter/?branch=master)

The most universal Front matter (yaml, json, neon, toml) parser and dumper for PHP.
Front matter allows page-specific variables to be included at the top of a page.

Installation
------------

This library can be installed via Composer:

    composer require webuni/front-matter

Usage
-----

### Parse an arbitrary string

```php
<?php

$frontMatter = new \Webuni\FrontMatter\FrontMatter();

$document = $frontMatter->parse($string);

$data = $document->getData();
$content = $document->getContent();
```

### Check if a string has front matter

```php
<?php

$frontMatter = new \Webuni\FrontMatter\FrontMatter();

$hasFrontMatter = $frontMatter->exists($string);
```

### Twig loader

If you want to store metadata about twig template, e.g.:

```twig
{#---
title: Hello world
menu: main
weight: 20
---#}
{% extend layout.html.twig %}
{% block content %}
Hello world!
{% endblock %}
```

you can use `FrontMatterLoader`, that decorates another Twig loader:

```php
$frontMatter = \Webuni\FrontMatter\Twig\TwigCommentFrontMatter::create();
$loader = new \Twig\Loader\FilesystemLoader(['path/to/templates']);
$loader = new \Webuni\FrontMatter\Twig\FrontMatterLoader($frontMatter, $loader);

$twig = new \Twig\Environment($loader);
$content = $twig->render('template', []);
// rendered the valid twig template without front matter
```

It is possible to inject front matter to Twig template as variables:

```php
// …
$converter = \Webuni\FrontMatter\Twig\DataToTwigConvertor::vars();
$loader = new \Webuni\FrontMatter\Twig\FrontMatterLoader($frontMatter, $loader, $converter);
// …
```

Loaded Twig template has this code:

```twig
{% set title = "Hello world" %}
{% set menu = "main" %}
{% set weight = 20 %}
{% line 5 %}
{% extend layout.html.twig %}
{% block content %}
Hello world!
{% endblock %}
```

Available converters:

| Converter                                 | Twig                                                       |
| ----------------------------------------- | ---------------------------------------------------------- |
| `DataToTwigConvertor::nothing()`          | ``                                                         |
| `DataToTwigConvertor::vars()`             | `{% set key1 = value1 %}`                                  |
| `DataToTwigConvertor::vars(false)`        | `{% set key1 = key1 is defined ? key1 : value1 %}`         |
| `DataToTwigConvertor::var('name')`        | `{% set name = {key1: value1, key2: value2} %}`            |
| `DataToTwigConvertor::var('name', false)` | `{% set name = name is defined ? name : {key1: value1} %}` |

### Markdown

The most commonly used front matter is for markdown files:

```markdown
---
layout: post
title: I Love Markdown
tags:
  - test
  - example
---

# Hello World!
```

This library can be used with [league/commonmark](https://commonmark.thephpleague.com/):

```php
$frontMatter = new \Webuni\FrontMatter\FrontMatter();
$extension = new \Webuni\FrontMatter\Markdown\FrontMatterLeagueCommonMarkExtension($frontMatter);

$environment = \League\CommonMark\Environment::createCommonMarkEnvironment();
$environment->addExtension($extension);

$converter = new \League\CommonMark\CommonMarkConverter([], $environment);
$html = $converter->convertToHtml('markdown'); // html without front matter
```

Alternatives
------------

- https://github.com/spatie/yaml-front-matter
- https://github.com/mnapoli/FrontYAML
- https://github.com/Modularr/YAML-FrontMatter
