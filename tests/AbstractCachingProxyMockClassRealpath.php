<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : tests/AbstractCachingProxyMockClassRealpath.php
   Autor    : (c) Sebastian Krüger <krueger@secra.de>
   Date     : 19.03.2014

   For the full copyright and license information, please view the LICENSE
   file that was distributed with this source code.

   Description: extends the AbstractCachingProxy class only to test
                Realpath function behavior in without override it

  ----------------------------------------------------------------------------*/

namespace secra\CachingProxy;

class AbstractCachingProxyMockClassRealpath extends AbstractCachingProxy
{
    public function getCacheFileExtension()
    {
        // return dummy extension of cached files
        return '.test';
    }

    public function getIncludeHtml()
    {
        return "";
    }
}