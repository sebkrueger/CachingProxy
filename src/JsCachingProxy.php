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

    /**
     * Delivers extension for cached files
     *
     * @return string   file extension
     *
     */
    protected function getCacheFileExtension()
    {
        // return Extension of css files
        return '.js';
    }

}