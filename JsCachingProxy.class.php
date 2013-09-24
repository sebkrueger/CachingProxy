<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : JsCachingProxy.class.php
   Version  : 1.0
   Autor    : Sebastian Krüger
   Date     : 15.09.2013

   Description: extends the CachingProxy with js

  ----------------------------------------------------------------------------*/

namespace CachingProxy;

class JsCachingProxy extends CachingProxy {

    public function __construct() {
        // Setzten des Pfad zum CSS Dateien Cachen
        $this->setCachepath("/demo/js/cache/");
        // Dateiendung für die zuammengefassten Cache Dateien
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