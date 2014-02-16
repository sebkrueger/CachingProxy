CachingProxy
============

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

    $css_cache = new \secra\CachingProxy\CssCachingProxy();

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