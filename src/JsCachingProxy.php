<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : src/JsCachingProxy.php
   Autor    : (c) Sebastian Krüger <krueger@secra.de>
   Date     : 15.09.2013

   For the full copyright and license information, please view the LICENSE
   file that was distributed with this source code.

   Description: extends the CachingProxy with js

  ----------------------------------------------------------------------------*/

namespace secra\CachingProxy;

class JsCachingProxy extends AbstractCachingProxy
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

        // define ending for packed javascript files
        $this->cachefileextension=".js";
    }

    /**
     * Delivers the set of html tags for webpage inclusion
     *
     * @return string   the html script tags
     *
     */
    public function getIncludeHtml()
    {
        // Gibt den Einbindungscode für die Dateien zurück
        $filelist = $this->getIncludeFileset();

        $htmlreturn = "";

        foreach ($filelist as $file) {
            $htmlreturn .= "<script type=\"text/javascript\" ";
            $htmlreturn .= "src=\"".$file."\"></script>\n";
        }

        return $htmlreturn;
    }
}