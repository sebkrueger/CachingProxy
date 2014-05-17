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

    /**
     * Modifiy filepath definations in css files because of
     * different cachefolder path
     *
     * @param  string $csscontent     content processed of css file
     * @param  string $cssfilepath    absolut path to css file
     *
     * @return string   modified css content
     *
     */
    protected function modifyFilecontent($csscontent, $cssfilepath)
    {

        $relativeCssFilePath = preg_replace("#^".$this->docrootpath."#", "", $cssfilepath);

        // Use # insted of / in regexpress!
        // Search for every url() expr in css files
        // only if the start with ./
        // Don't matter if url in " or not
        $csscontent = preg_replace_callback('#url\("?\./([^"]+)"?\)#i',
            function($matches) {
                // $matches[1] contain first subpattern
                return 'url("'.strtoupper($matches[1]).'")';
            }, $csscontent);

        // return css content
        return $csscontent;
    }
}