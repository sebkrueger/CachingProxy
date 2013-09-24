<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : CssCachingProxy.class.php
   Version  : 1.0
   Autor    : Sebastian Krüger
   Date     : 15.09.2013

   Description: extends the CachingProxy with css

  ----------------------------------------------------------------------------*/

namespace CachingProxy;

class CssCachingProxy extends CachingProxy {

    public function __construct() {
        // set path to css cache folder
        $this->setCachepath("/demo/css/cache/");
        // define ending for css files
        $this->cachefileextension=".css";
    }

    public function getIncludeHtml() {
        // return include html code
        $filelist = $this->getIncludeFileset();

        $htmlreturn = "";

        foreach($filelist AS $file) {
            $htmlreturn .= "<link rel=\"stylesheet\" type=\"text/css\" ";
            $htmlreturn .= "href=\"".$file."\" />\n";
        }

        return $htmlreturn;
    }
}