ByMenu
======

Installation
------------

Usage
-----

### Create a menu

#### Code

```PHP
$menu = new Menu($id);
```

`$id` is the Menu Id. It will be used to generate HTML id.

##### Example

```php
$menu = new Menu('top');
```

### Add items

#### Code

```PHP
$item = new Item($menu, $id, $label, $url = null);
```

`$menu` is the Menu instance in which to insert the item
`$id` is the Item Id. It will be used to generate HTML id.
`$label` is the text of the label.
`$url` is the ... url. By default, if null, a <span> will be generated

#### Example

```php
$menu = new Menu('top');
$homeItem = new Item($menu, 'home', 'Back to home', '/');
$videoItem = new Item($menu, 'video', 'View video', '/video');
```

### Nested menu

#### Code

```php
$menu = new Menu('top');

$homeItem = new Item($menu, 'home', 'Back to home', '/');
$videoItem = new Item($menu, 'video', 'View video', '/video');

$videoMenu = new Menu('video');
$aviItem = new Item($videoMenu, 'avi', 'AVI', '/video/avi');
$mkvItem = new Item($videoMenu, 'mkv', 'MKV', '/video/mkv');

$videoItem->setSubMenu($videoMenu)