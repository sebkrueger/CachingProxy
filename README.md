CachingProxy
============

Caching Proxy Class

The CachingProxy include css and js files in other php scripts and build a path to the files,
with an timestamp of the last modification in it.

Main features are:
------------------
* detect last version of .css and .js Source files
* detect .min version of .css and .js files
* combine all .css and .js files to one cached file
* auto create gzip version of cached file
* depend on mod rewrite and browser, deliver precompressed files

The demo folder contain a sample webpage with the CSS and Javascript .