<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : src/CssCachingProxy.php
   Autor    : (c) Sebastian Krüger <krueger@secra.de>
   Date     : 15.09.2013

   For the full copyright and license information, please view the LICENSE
   file that was distributed with this source code.

   Description: extends the CachingProxy with css

  ----------------------------------------------------------------------------*/

namespace secra\CachingProxy;

class CssCachingProxy extends AbstractCachingProxy
{
    /**
     * Delivers the set of html tags for webpage inclusion
     *
     * @return string   the html .css link tags
     *
     */
    public function getIncludeHtml()
    {
        // return include html code
        $filelist = $this->getIncludeFileset();

        $htmlreturn = "";

        foreach ($filelist as $file) {
            $htmlreturn .= "<link rel=\"stylesheet\" type=\"text/css\" ";
            $htmlreturn .= "href=\"".$file."\" />\n";
        }

        return $htmlreturn;
    }

    /**
     * Delivers extension for cached files
     *
     * @return string   file extension
     *
     */
    protected function getCacheFileExtension()
    {
        // return Extension of css files
        return '.css';
    }
}