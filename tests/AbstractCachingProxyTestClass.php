<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : tests/AbstractCachingProxyTestClass.php
   Autor    : (c) Sebastian Krüger <krueger@secra.de>
   Date     : 10.03.2014

   For the full copyright and license information, please view the LICENSE
   file that was distributed with this source code.

   Description: extends the AbstractCachingProxy class to build up reflection
                to invoke private methods, in other cases better use the
                unittest getMockForAbstractClass() function

  ----------------------------------------------------------------------------*/

namespace secra\CachingProxy;

/**
 * Override realpath() in current namespace for testing
 *
 * @param string $path     the file path
 *
 * @return string
 */
function realpath($path)
{
    return $path;
}

class AbstractCachingProxyTestClass extends AbstractCachingProxy
{
    public function __construct($webserverRootPath, $cachePath)
    {
        // In Testmode, direkt set the path because we don't test the setting functions
        // We expect dummy values, if no need to test file systemfuntions
        // and vfs filetree, if we test filesystem functions
        $this->docrootpath = $webserverRootPath;
        $this->cachepath = $webserverRootPath.$cachePath;
        $this->relcachepath = $cachePath;

        // define ending for cache files
        $this->cachefileextension=".test";
    }

    public function getIncludeHtml()
    {
        return "";
    }

    public function getDebugMode()
    {
        return $this->debugmode;
    }

    public function getDocrootpath()
    {
        return $this->docrootpath;
    }

    public function getCachepath()
    {
        return $this->cachepath;
    }

    public function getRelCachepath()
    {
        return $this->relcachepath;
    }
}