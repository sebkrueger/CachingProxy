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
    private $internfilelist = array();        // array with files that should be cached later
    private $externfilelist = array();        // array with extern files

    protected $cachepath = null;             // path were cached files should be placed
    protected $relcachepath = null;           // relative cachepath for scripttags in html
    protected $cachefileextension = null;     // fileending of cached files

    // In debugmode every file will be include in a single tag without modification
    private $debugmode = false;

    /**
     * Start with setting the specific cachepath from project root
     *
     * @param  $cachingpath string    path to cachefile location based on project root path
     */
    public function __construct($cachingpath)
    {
        return $this->setCachepath($cachingpath);
    }

    /**
     * Implement later html code return
     *
     * Implement this to get the specific html head code
     *
     * @return string   the html scripttag code
    */
    abstract public function getIncludeHtml();

    /**
     * Add files to proxy
     *
     * Add intern, project relative files or extern files, on different domain
     * to the filelist
     *
     * @param  $filename string    the filepath/URL to script
     *
     * @return boolean             false on error
     */
    public function addFile($filename)
    {
        // Fügt eine Datei zur Cacheliste hinzu, es wird hier schon nach internen oder
        // Externen Dateien unterschieden beginnen z.B. mit http, https, ftp und dann ://
        if (!preg_match("#^[a-z]{3,5}://#i",$filename)) {
            // Internes File, Arbeit für den Cache
            $absolutfilename = self::makeAbsolutPath($filename);
            if (!file_exists($absolutfilename)) {
               // Die Datei existiert nicht!
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
     * @return array       list of files
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
            $returnfilelist[] = $oneModifiedCacheFile;
        } else {
            // we are in debugmode!
            // only put the internfiles to the list of returned files
            foreach ($this->internfilelist as $file) {
                // strip the absolut dir for inclusion and put it to the list
                // Use the $ as reg_exp separater because don't expect it in path
                $returnfilelist[] = preg_replace("$^".($_SERVER["DOCUMENT_ROOT"])."$", "", $file);
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
     * Set path to cachefile folder
     *
     * The cachingpath must be set from docroot of project
     *
     * @param $cachepath string    path to cachefilefolder
     *
     * @return boolean       true if cachefolder exist
     */
    protected function setCachepath($cachepath)
    {
        // TODO: Check if there's a way to eleminate the absolut paths

        // Prüfen, ob der Pfad auf / endet, falls nicht jetzt hinzufügen
        if (!preg_match("#/$#", $cachepath)) {
            $cachepath .= "/";
        }

        // Prüfen ob der Pfad mit einem / anfängt jetzt hinzufügen, es sollen alle internen
        // Dateien später ausgehend vom Webserver Root eingebunden werden, wichtig auch für
        // über Rewrite eingebunden Dateien
        if (!preg_match("#^/#", $cachepath)) {
            $cachepath = "/".$cachepath;
        }

        // make cachepath absolut
        $absolutcachepath = self::makeAbsolutPath($cachepath);

        // Checken if path exist could be false/null because the use of makeAbsolutPath()!!
        if (is_dir($absolutcachepath)) {
            $this->cachepath=$absolutcachepath."/";
            $this->relcachepath=$cachepath;
            return true;
        } else {
            // folder did't exist
            return false;
        }
    }

    /**
     * Relative to absolut path
     *
     * Convert relativ path webserver root path to
     * absolut path from root in file system
     *
     * @param $path string    relative path to webserver root
     *
     * @return string         absolut path to webserver root
     */
    private function makeAbsolutPath($path)
    {
        // Note, if file/folder don't exists, realpath will return false
        return realpath($_SERVER["DOCUMENT_ROOT"]."/".$path);
    }

    /**
     * Add .min in file path
     *
     * Check if minified version of file exits
     * is exits use it
     *
     * @param $path string   path to notminified version of file
     *
     * @return string       path to minified version of file
     */
    private function makeMinifiPath($path)
    {
        // Prüft ob eine minifizierte Version der Datei existiert,
        // falls ja wird diese benutzt

        // split path at the dots
        $splitpath = explode(".",$path);

        $newfragments = array();

        for ($i=0;$i<count($splitpath);$i++) {
            if ($i==(count($splitpath)-1)) {
                // insert "min" bevor last element (file ending)
                $newfragments[]="min";
            }
            $newfragments[]=$splitpath[$i];
        }

        // now put the puzzelpices together
        return implode(".",$newfragments);
    }

    /**
     * Return one cached file
     *
     * Check if cached and gzipped version of file exits
     * if not convert all intern files to one file and
     * write it to one cache
     *
     * @return string       path to cached file
     */
    private function getCacheFile()
    {
        // Fragt an, ob eine Datei die zu der Dateisignatur der angefragten Dateinen passt,
        // schon in der Liste der Dateien ist
        $cachefilesignature = $this->calculateFileSignature();

        // Zusammenbau des Pfad, ausgehend vom Dokument root
        $cachefile = $cachefilesignature.$this->cachefileextension;

        $absolutcachepath = $this->cachepath.$cachefile;

        if ($cachefilesignature!=null && !file_exists($absolutcachepath)) {
            // Die Datei wurde noch nie in den Cache geschrieben, jetzt erzeugen -> the hard way!
            // and cachefilesignature has to be differnet from null

            // Dateien zusammenfügen
            foreach ($this->internfilelist as $file) {
                // Inhalt der aktuellen CSS Datei einlesen
                $filecontent = file_get_contents($file);

                // Um Sicherzugehen noch einen Zeilewechsel anfügen
                $filecontent .= "\n";

                // Beim schreiben Dateiinhalt anfügen und Datei zum Schreiben von anderen locken!
                if (file_put_contents($absolutcachepath, $filecontent , FILE_APPEND | LOCK_EX)===false) {
                    // Beim Schreiben der Datei ist was falsch gelaufen
                    // Cachedatei löschen und hoffen beim nächsten mal klappt es mit dem Schreiben
                    unlink($absolutcachepath);
                    return false;
                }
            }
            // Angsthasen pause
            usleep(5000);

            // jetzt noch gzip Version der Datei erzeugen, wenn wir schon mal dabei sind
            file_put_contents($absolutcachepath.".gz", gzencode(file_get_contents($absolutcachepath), 9));
        }

        return $this->relcachepath.$cachefile;
    }

    /**
     * Calculate a signature to detect file modification
     *
     * Calculate a signature of all intern files
     * base parameters are filename and file modfied date
     * hash function is md5
     *
     * @return string       signature
     */
    private function calculateFileSignature()
    {
        // Erzeugt aus allen Dateien im internen Array eine Signatur
        // Hierz wird der Dateiname+Änderungsdatum der Datei verrechnet und
        // als md5 zurückgegeben
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