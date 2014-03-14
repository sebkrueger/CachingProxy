<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : tests/CssCachingProxyTestClass.php
   Autor    : (c) Sebastian Krüger <krueger@secra.de>
   Date     : 10.03.2014

   For the full copyright and license information, please view the LICENSE
   file that was distributed with this source code.

   Description: extends the CssCachingProxy and override base Class function to
                test the CssCachingProxy function

  ----------------------------------------------------------------------------*/

namespace secra\CachingProxy;

class CssCachingProxyTestClass extends CssCachingProxy
{
    protected $cachefileextension = null;     // fileending of cached files

    public function getCachefileExtension()
    {
        return $this->cachefileextension;
    }
}