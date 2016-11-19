CRUD Generator for repeated use
===============================

This packet contains a CRUD generator for the framework YII2.

This generator generates a controller and views that implement CRUD operations for the specified data model using the great modules of kartik-v.

Many parameters of the Grid can be defined and this data is stored for reusing the generator to update the Grid settings till final version. This settings inculdes the handling of foreign keys, search fields, column formats, withs, visibility, order, labels and alignment and also additional	buttons (including glyphicons) in the footer area for actions and mass actions.

The template is splitted into two parts, so you can write already code in one part and update settings of the grid in the other part by using the generator.
        	
Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yasheena/yii2-gii "*"
```

or add

```
"yasheena/yii2-gii": "*"
```

to the require-dev section of your `composer.json` file.


Usage
-----

Once the extension is installed, you can find a new CRUD generator in your generator list.
