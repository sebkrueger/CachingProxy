<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : src/JsCachingProxy.class.php
   Autor    : (c) Sebastian Krüger <krueger@secra.de>
   Date     : 15.09.2013

   For the full copyright and license information, please view the LICENSE
   file that was distributed with this source code.

   Description: extends the CachingProxy with js

  ----------------------------------------------------------------------------*/

namespace secra\CachingProxy;

class JsCachingProxy extends CachingProxy {

    public function __construct() {
        // set default path to cache .js files, base is webserver document root path
        $this->setCachepath("/demo/js/cache/");
        // Dateiendung für die zusammengefassten Cache Dateien
        $this->cachefileextension=".js";
    }

    public function getIncludeHtml() {
        // Gibt den Einbindungscode für die Dateien zurück
        $filelist = $this->getIncludeFileset();

        $htmlreturn = "";

        foreach($filelist AS $file) {
            $htmlreturn .= "<script type=\"text/javascript\" ";
            $htmlreturn .= "src=\"".$file."\"></script>\n";
        }

        return $htmlreturn;
    }
}