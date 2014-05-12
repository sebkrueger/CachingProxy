<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : src/AbstractCachingProxy.php
   Autor    : (c) Sebastian Krüger <krueger@secra.de>
   Date     : 15.09.2013

   For the full copyright and license information, please view the LICENSE
   file that was distributed with this source code.

   Description: Basisklasse die einen Mechanismus zu Cachen von Dateien auf dem
                Server implemeniert. Anwendung ist später für CSS und Javscript
                Dateien vorgesehen

                Die zu Cachenden Dateien werden zu einer gesammten Datei zusammengefasst
                Externe Dateien werden nicht zusammengefasst, sondern vorerst einfach nur
                als Einbindung ausgegeben. Falls vorhanden wird die minifizierte Version der
                Datei vorgezogen. Zu guter letzt werden die Dateien auch noch per GZ gepackt
                um statische gepackte Dateien anbieten zu können

  ----------------------------------------------------------------------------*/

namespace secra\CachingProxy;

abstract class AbstractCachingProxy
{
    protected $internfilelist = array();      // array with files that should be cached later
    private $externfilelist = array();        // array with extern files

    protected $docrootpath = null;            // webserver document root path
    protected $cachepath = null;              // absolut path on webserver were cached files should be placed
    protected $relcachepath = null;           // relative cachepath for scripttags in html

    // In debugmode every file will be include in a single tag without modification
    protected $debugmode = false;

    /**
     * Implement later to set the document rootpath and cachepath
     *
     * @param  string $webserverRootPath     absolut path to webserver root
     * @param  string $cachePath             path to cachefile location based on webserver root path
     *
     * @return AbstractCachingProxy|null     objectinstance
    */
    public function __construct($webserverRootPath, $cachePath)
    {
        $this->setWebserverRootPath($webserverRootPath);
        $this->setCachepath($cachePath);
    }

    /**
     * Implement later html code return
     *
     * Implement this to get the specific html head code
     *
     * @codeCoverageIgnore
     *
     * @return string   the html scripttag code
    */
    abstract public function getIncludeHtml();

    /**
     * Delivers extension for cached files
     *
     * @codeCoverageIgnore
     *
     * @return string   file extension
     *
     */
    abstract protected function getCacheFileExtension();

    /**
     * Add files to proxy
     *
     * Add intern, project relative files or extern files, on different domain
     * to the filelist
     *
     * @param  string $filename     the filepath/URL to script
     *
     * @return boolean             false on error
     */
    public function addFile($filename)
    {
        // Fügt eine Datei zur Cacheliste hinzu, es wird hier schon nach internen oder
        // Externen Dateien unterschieden beginnen z.B. mit http, https, ftp und dann ://
        // or protocoll less // links
        if (!preg_match("#^[a-z]{3,5}://#i", $filename) && !preg_match("#^//#i", $filename)) {
            // intern files, work for the cache
            $absolutfilename = self::makeAbsolutPath($filename);
            if (!file_exists($absolutfilename)) {
                // the file did't exist
                return false;
            }

            // Falls möglich Minifizierte Version der Datei benutzen
            $minfilename = self::makeMinifiPath($absolutfilename);

            if (file_exists($minfilename)) {
                // Es scheint jemand die Datei gepackt zu haben
                $absolutfilename = $minfilename;
            }

            $this->internfilelist[] = $absolutfilename;
        } else {
            // Ist wohl eine Externe Pfadangabe, kann nicht gechached werden
            $this->externfilelist[] = $filename;
        }
        return true;
    }

    /**
     * Return list of all files can be include
     *
     * First deliver all intern then all extern files
     * the user can decide by himself, what he would like to do with the list
     *
     * @return string[]       list of files
     */
    public function getIncludeFileset()
    {
        // Return list of all files that can include
        // first all intern then all extern files
        // the user can decide by himself, what he would like to do with the list

        // Exclude double files from filelist
        $this->internfilelist = array_unique($this->internfilelist);
        $this->externfilelist = array_unique($this->externfilelist);

        $returnfilelist = array();

        // generate cachefile
        // the return will not be used in debugmode, but generate the files anyway
        $oneModifiedCacheFile = $this->getCacheFile();

        if ($this->debugmode===false) {
            // put intern files into the cached version
            if ($oneModifiedCacheFile!=null) {
                // only replace the intern file list, if theres intern files and the modified cache file exits
                $returnfilelist[] = $oneModifiedCacheFile;
            }
        } else {
            // we are in debugmode!
            // only put the internfiles to the list of returned files
            foreach ($this->internfilelist as $file) {
                // strip the absolut dir for inclusion and put it to the list
                // use the $ as reg_exp separater because don't expect it in path
                $returnfilelist[] = "/".preg_replace("$^".($this->docrootpath)."$", "", $file);
            }
        }

        // extern files will only add to the list
        foreach ($this->externfilelist as $file) {
            $returnfilelist[] = $file;
        }

        return $returnfilelist;
    }

    /**
     * Sweet as simple ... activate the debugmode
     *
     * @return null
     */
    public function enableDebugmode()
    {
        // sweet as simple ... activate the debugmode
        $this->debugmode=true;
        return null;
    }

    /**
     * belive it or not ... deactivate the debugmode
     *
     * @return null
     */
    public function disableDebugmode()
    {
        $this->debugmode=false;
        return null;
    }

    /**
     * Set the relative Cachepath
     * use simple $_SERVER["DOCUMENT_ROOT"] to set this value
     *
     * Set absolut webserver rootpath
     *
     * @param string $documentRootPath     path to webserverroot
     *
     * @return boolean     false on error
     */
    protected function setWebserverRootPath($documentRootPath)
    {
        // Reset the documentrootpath
        $this->docrootpath = null;

        // Add trailing slash if not there
        if (!preg_match("#/$#", $documentRootPath)) {
            $documentRootPath .= "/";
        }

        if (file_exists($documentRootPath)) {
            $this->docrootpath = $documentRootPath;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set path to cachefile folder
     *
     * The cachingpath must be set relativ from docroot of project
     *
     * @param string $cachepath     path to cachefilefolder
     *
     * @return boolean              true if cachefolder exist, false if not
     */
    protected function setCachepath($cachepath)
    {

        // try to make cachepath absolut
        $absolutcachepath = self::makeAbsolutPath($cachepath);

        // check if path exist could be false/null because the use of makeAbsolutPath()!!
        if (is_dir($absolutcachepath)) {

            // extra check on trailing slash of $absolutcachepath because of realpath function use
            if (!preg_match("#/$#", $absolutcachepath)) {
                $absolutcachepath .= "/";
            }

            $this->cachepath=$absolutcachepath;

            // check if path has trailing slash, if not add now
            if (!preg_match("#/$#", $cachepath)) {
                $cachepath .= "/";
            }

            // check if path begin with slash, if not add it now because
            // later every intern files will include absolut to the webserver
            // root path, also importent, if mode rewrite is in use on the server
            if (!preg_match("#^/#", $cachepath)) {
                $cachepath = "/".$cachepath;
            }

            $this->relcachepath=$cachepath;
            return true;
        } else {
            // folder did't exist
            // TODO: throw next time an error!
            return false;
        }
    }

    /**
     *
     * Predefined Function to do something content specific work in
     * concrete classes right befor the minify process
     * default do nothing and return the sting one2one
     *
     * @param string $filecontent     filecontent to process
     * @param string $filepath        path to the file to convert it later
     *
     * @return string     modified filecontent
     */
    protected function modifyFilecontent($filecontent, $filepath)
    {
        // default -> return content one2one
        return $filecontent;
    }

    /**
     * Relative to absolut path
     *
     * Convert relativ path webserver root path to
     * absolut path from root in file system
     *
     * @param string $path     relative path to webserver root
     *
     * @return string         absolut path to webserver root
     */
    private function makeAbsolutPath($path)
    {
        // Note, if file/folder don't exists, realpath will return false
        return realpath($this->docrootpath.$path);
    }

    /**
     * Add .min in file path
     *
     * Check if minified version of file exits
     * is exits use it
     *
     * @param string $path    path to notminified version of file
     *
     * @return string       path to minified version of file
     */
    private function makeMinifiPath($path)
    {
        // check if there's a minified version of file, if yes there min version will be used

        // split path at the dots
        $splitpath = explode(".", $path);

        $newfragments = array();

        for ($i=0; $i<count($splitpath); $i++) {
            if ($i==(count($splitpath)-1)) {
                // insert "min" bevor last element (file ending)
                $newfragments[]="min";
            }
            $newfragments[]=$splitpath[$i];
        }

        // now put the puzzelpices together
        return implode(".", $newfragments);
    }

    /**
     * Return one cached file
     *
     * Check if cached and gzipped version of file exits
     * if not convert all intern files to one file and
     * write it to one cache
     *
     * @return string|false|null       path to cached file or null if no intern files and false on error
     */
    private function getCacheFile()
    {
        // ask if there file signature match with, requested files in the file list
        $cachefilesignature = $this->calculateFileSignature();

        // connect the path, related to document root
        $cachefile = $cachefilesignature.$this->getCacheFileExtension();

        $absolutcachepath = $this->cachepath.$cachefile;

        // set return value null in case there are no internfiles
        $returnfile = null;

        if ($cachefilesignature!=null) {
            // The cachefilesignature has to be different from null to start
            if (!file_exists($absolutcachepath)) {
                // the file has never been written, write now -> the hard way!
                // put files together
                foreach ($this->internfilelist as $file) {
                    // read content of current file
                    // if overwritten, modfiy the content and put the files together in one string
                    $filecontent = $this->modifyFilecontent(file_get_contents($file), $file);

                    // to be safe, add new line
                    $filecontent .= "\n";

                    // append content while writing and look file on other access tries!
                    if (file_put_contents($absolutcachepath, $filecontent, FILE_APPEND | LOCK_EX)===false) {
                        // TODO: this error check won't work well, maybe better use other function to write the file
                        return false;
                    }
                }
                // short delay to be safe
                usleep(5000);

                // now make the gzip version, once we on the way
                file_put_contents($absolutcachepath.".gz", gzencode(file_get_contents($absolutcachepath), 9));
            }

            // Files in Cachefolder still exits, assume we created it at another run
            // don't create them once again only build the path an return it
            $returnfile = $this->relcachepath.$cachefile;
        }

        return $returnfile;
    }

    /**
     * Calculate a signature to detect file modification
     *
     * Calculate a signature of all intern files
     * base parameters are filename and file modfied date
     * hash function is md5
     *
     * @return string|null       signature
     */
    private function calculateFileSignature()
    {
        // create a signature with all files in intern array structure
        // to make it unified, every filname and filechange date will calculate
        // together and return as md5 sum
        $tempstingbase = "";

        foreach ($this->internfilelist as $file) {
            $tempstingbase .= $file."->";
            $tempstingbase .= "(".filemtime($file).") ";
        }

        // when there are no intern files, function return null
        $signature=null;

        if (count($this->internfilelist)>0) {
            // there are intern files - simple md5 should do, no secure risk
            $signature = md5($tempstingbase);
        }

        return $signature;
    }

    // TODO: build cleanup function for old cached files
}