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

class AbstractCachingProxyTestClass extends AbstractCachingProxy
{
    public function __construct($webserverRootPath, $cachePath)
    {
        $this->setWebserverRootPath($webserverRootPath);
        $this->setCachepath($cachePath);

        // define ending for cache files
        $this->cachefileextension=".test";
    }

    public function getIncludeHtml()
    {
        return "";
    }
}