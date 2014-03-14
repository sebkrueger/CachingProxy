<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : tests/JsCachingProxyTestClass.php
   Autor    : (c) Sebastian Krüger <krueger@secra.de>
   Date     : 10.03.2014

   For the full copyright and license information, please view the LICENSE
   file that was distributed with this source code.

   Description: extends the JsCachingProxy and override base Class function to
                test the JsCachingProxy function

  ----------------------------------------------------------------------------*/

namespace secra\CachingProxy;

class JsCachingProxyTestClass extends JsCachingProxy
{
    protected $cachefileextension = null;     // fileending of cached files

    public function getCachefileExtension()
    {
        return $this->cachefileextension;
    }
}