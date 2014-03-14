CachingProxy
============

[![Build Status](https://travis-ci.org/sebkrueger/CachingProxy.png?branch=master)](https://travis-ci.org/sebkrueger/CachingProxy)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/sebkrueger/CachingProxy/badges/quality-score.png?s=6e879250b7b38e6ae55a7f553d73ae7207b1b36b)](https://scrutinizer-ci.com/g/sebkrueger/CachingProxy/)
[![Code Coverage](https://scrutinizer-ci.com/g/sebkrueger/CachingProxy/badges/coverage.png?s=b3c19baf3b814a2e46804d0dad23e7b007c034a9)](https://scrutinizer-ci.com/g/sebkrueger/CachingProxy/)

Caching Proxy Class

The CachingProxy include css and js files in other php scripts and build a path to the files,
with an timestamp of the last modification in it.

Main features are
-----------------
* detect last version of .css and .js Source files
* detect .min version of .css and .js files
* combine all .css and .js files to one cached file
* auto create gzip version of cached file
* depend on mod rewrite and browser, deliver precompressed files
* debugmode for development and native, unmodified inclusion of files

Usage
-----
For .css files use the following code in the head of your webpage.
The usage for .js should be obvious.

    // set the path to you webserver document root most of the time $_SERVER["DOCUMENT_ROOT"]
    // and in 2. parameter the path, were you would like to store your cached files absolut from document root
    $css_cache = new \secra\CachingProxy\CssCachingProxy($_SERVER["DOCUMENT_ROOT"], "/path/to/css/cache");

    // Add some files
    $css_cache->addFile("/path/to/file1.css");
    $css_cache->addFile("/path/to/file2.css");

    // Do the packing work and print the html into the head
    echo $css_cache->getIncludeHtml();

Demo page
---------
The /demo folder contain a sample webpage with the CSS and Javascript.

Changelog
---------
###Version 0.1 (16.	February 2014)
+ Add Composer Support
+ PSR-4 compatible
+ EditorConfig Support
+ Add Licence Text
+ Add usage example in readme