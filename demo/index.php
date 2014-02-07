<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : demo/index.php
   Version  : 1.0
   Autor    : Sebastian Krüger
   Date     : 15.09.2013

   Description: demoversion for caching proxy

  ----------------------------------------------------------------------------*/

require_once("../CachingProxy.class.php");
require_once("../CssCachingProxy.class.php");
require_once("../JsCachingProxy.class.php");
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Caching Proxy Demo Page</title>
    <meta charset="utf-8">
    <?php
    // init the CSS Caching object
    $css_cache = new \CachingProxy\CssCachingProxy();

    // In debug mode every file will insert in a single tag with modification
    $css_cache->EnableDebugmode();

    // Add some files
    $css_cache->addFile("/demo/css/main.css");
    $css_cache->addFile("/demo/css/element1.css");
    $css_cache->addFile("/demo/css/css_framework_abc/css_framework_abc.css");

    // print out the html head inlude for css
    echo $css_cache->getIncludeHtml();

    // init the js Caching object
    $js_cache = new \CachingProxy\JsCachingProxy();

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