<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : demo/index.php
   Autor    : (c) Sebastian Krüger <krueger@secra.de>
   Date     : 15.09.2013

   For the full copyright and license information, please view the LICENSE
   file that was distributed with this source code.

   Description: demoversion for caching proxy

  ----------------------------------------------------------------------------*/

require_once("../src/CachingProxy.class.php");
require_once("../src/CssCachingProxy.class.php");
require_once("../src/JsCachingProxy.class.php");
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Caching Proxy Demo Page</title>
    <meta charset="utf-8">
    <?php
    // init the CSS Caching object
    $css_cache = new \secra\CachingProxy\CssCachingProxy();

    // In debug mode every file will insert in a single tag with modification
    $css_cache->EnableDebugmode();

    // Add some files
    $css_cache->addFile("/demo/css/main.css");
    $css_cache->addFile("/demo/css/element1.css");
    $css_cache->addFile("/demo/css/css_framework_abc/css_framework_abc.css");

    // print out the html head inlude for css
    echo $css_cache->getIncludeHtml();

    // init the js Caching object
    $js_cache = new \secra\CachingProxy\JsCachingProxy();

    // Enable the Debugmode for js files
    $js_cache->EnableDebugmode();

    $js_cache->addFile("/demo/js/button.js");

    // print out the html head include for javascript
    echo $js_cache->getIncludeHtml();
    ?>
</head>
<body>
<p>CachingProxy Demopage</p>
<div id="bluebox"></div>
<br>
<input id="hitme" type="button" value="hit me to alter the box color from black to red!" />
<br>
<p>You must see 4 german flags, for correct css background image processing!</p>
<div class="bgImage bgImageVersion1"></div>
<div class="bgImage bgImageVersion2"></div>
<div class="bgImage bgImageVersion3"></div>
<div class="bgImage bgImageVersion4"></div>