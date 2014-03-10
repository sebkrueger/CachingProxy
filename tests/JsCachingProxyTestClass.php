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
    protected $cachepath = null;              // path were cached files should be placed
    protected $cachefileextension = null;     // fileending of cached files

    protected function setCachepath($cachepath)
    {
       $this->cachepath=$cachepath;
    }

    public function getCachepath()
    {
        return $this->cachepath;
    }

    public function getCachefileExtension()
    {
        return $this->cachefileextension;
    }
}