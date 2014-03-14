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
     * Start with setting the specific cachepath from project root
     *
     * @param  string $webserverRootPath     absolut path to webserver root
     * @param  string $cachePath             path to cachefile location based on webserver root path
     *
     */
    public function __construct($webserverRootPath, $cachePath)
    {
        $this->setWebserverRootPath($webserverRootPath);
        $this->setCachepath($cachePath);

        // define ending for css files
        $this->cachefileextension=".css";
    }

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
}