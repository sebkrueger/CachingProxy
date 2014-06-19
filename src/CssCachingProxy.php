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
        // Remove the path to docroot from full filepath and remove filename from cssfilepath
        $relativeCssPath = dirname(preg_replace("#^".$this->docrootpath."#", "", $cssfilepath));

        // Use # insted of / in regexpress!
        // Search for every url() expr in css files only if the start with ./
        // Don't matter if url in " or not
        $csscontent = preg_replace_callback(
            '#url\("?\./([^"]+)"?\)#i',
            function ($matches) use ($relativeCssPath)
            {
                // $matches[1] contain first subpattern
                return 'url("/'.$relativeCssPath.'/'.$matches[1].'")';
            },
            $csscontent
        );

        // Now search for path with ../ sequences
        $csscontent = preg_replace_callback(
            '#url\("?(../){1,20}([^"]+)"?\)#i',
            function ($matches) use ($relativeCssPath)
            {
                // $matches[0] contains whole matching pattern
                // $matches[1] contains ../ subpattern !! but only one time !!
                // $matches[2] contain path subpattern

                // Now count only how much ../ in the begining of the string to avoid counting of ../ in the middle

                // Where is the first .
                $posFirstDot = strpos($matches[0], ".");

                // Number of char that countain only ./ from the beginning of the string
                $charCount = strspn($matches[0], "./", $posFirstDot);

                // Cut the first part of the string
                $pathstring = substr($matches[0], $posFirstDot, $charCount);

                // Count the ../
                $pathdepth = substr_count($pathstring, '../');

                // Add starting slash, in case all folders has to be replaced, so the last can match in for loop
                // later the / is need because we transform the relativ to absolut path, so we need a starting slash
                $relativeCssPath = "/".$relativeCssPath;

                // Remove all folder that dots stand for
                for ($i=0; $i<$pathdepth; $i++) {
                    // find last occurrence of / in csspath, remove folder depth from last to first
                    $lastSlashPos = strrpos($relativeCssPath, "/");
                    // remove the last folder now, substr replace everything to the end of the string as default
                    $relativeCssPath = substr_replace($relativeCssPath, "", $lastSlashPos);
                }

                return 'url("'.$relativeCssPath.'/'.$matches[2].'")';
            },
            $csscontent
        );

        // return css content
        return $csscontent;
    }
}