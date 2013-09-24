<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : CachingProxy.class.php
   Version  : 1.0
   Autor    : Sebastian Krüger
   Date     : 15.09.2013

   Description: Basisklasse die einen Mechanismus zu Cachen von Dateien auf dem
                Server implemeniert. Anwendung ist später für CSS und Javscript
                Dateien vorgesehen

                Die zu Cachenden Dateien werden zu einer gesammten Datei zusammengefasst
                Externe Dateien werden nicht zusammengefasst, sondern vorerst einfach nur
                als Einbindung ausgegeben. Falls vorhanden wird die minifizierte Version der
                Datei vorgezogen. Zu guter letzt werden die Dateien auch noch per GZ gepackt
                um statische gepackte Dateien anbieten zu können

  ----------------------------------------------------------------------------*/

namespace CachingProxy;

class CachingProxy {
    protected $internfilelist = array();        // Array mit Dateien die gecached werden sollen
    protected $externfilelist = array();        // Array mit externen Dateien

    protected $cachepath = null;                // Wo sollen die Dateien gecached werden
    protected $relcachepath = null;             // Relativer Cachepath für spätere Pfadausgaben im html
    protected $cachefileextension = null;       // Dateierweitung des Cachefiles

    public function __construct($cachingpath) {
        // Man kann einen beliebigen Cachingpfad vom Rootpath des Projektes aus setzen
        return $this->setCachepath($cachingpath);
    }

    public function addFile($filename) {
        // Fügt eine Datei zur Cacheliste hinzu, es wird hier schon nach internen oder
        // Externen Dateien unterschieden beginnen z.B. mit http, https, ftp und dann ://
        if(!preg_match("#^[a-z]{3,5}://#i",$filename)) {
            // Internes File, Arbeit für den Cache
            $absolutfilename = self::makeAbsolutPath($filename);
            if(!file_exists($absolutfilename)) {
               // Die Datei existiert nicht!
               return false;
            }

            // Falls möglich Minifizierte Version der Datei benutzen
            $minfilename = self::makeMinifiPath($absolutfilename);

            if(file_exists($minfilename)) {
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

    public function getIncludeFileset() {
        // Gibt eine Liste aller Dateien zurück, die
        // eingebunden werden, erst die internen, dann die externen
        // Der Nutzer kann später selbst bestimmen, was er mit den Dateien macht!

        // Doppelte Dateien aus beden Dateilisten entfernen
        $this->internfilelist = array_unique($this->internfilelist);
        $this->externfilelist = array_unique($this->externfilelist);

        $returnfilelist = array();

        // zuvorderst die Internen Dateien auf die gecachte Version umrechnen
        $returnfilelist[] = $this->getCacheFile();

        // Externe Dateien einfach anhängen
        foreach($this->externfilelist AS $file) {
            $returnfilelist[] = $file;
        }

        return $returnfilelist;
    }

    protected function setCachepath($cachepath) {
        // Setzten des Cachingpath, es muss immer vom Docroot des Projekts aus angegeben werden

        // Prüfen, ob der Pfad auf / endet, falls nicht jetzt hinzufügen
        if(!preg_match("#/$#",$cachepath)) {
            $cachepath .= "/";
        }

        // Prüfen ob der Pfad mit einem / anfängt jetzt hinzufügen, es sollen alle internen
        // Dateien später ausgehend vom Webserver Root eingebunden werden, wichtig auch für
        // über Rewrite eingebunden Dateien
        if(!preg_match("#^/#",$cachepath)) {
            $cachepath = "/".$cachepath;
        }

        // Cachepfad absolut machen
        $absolutcachepath = self::makeAbsolutPath($cachepath);

        // Checken ob der Pfad auch exitiert evtl. false/null wegen makeAbsolutPath()!!
        if(is_dir($absolutcachepath)) {
            $this->cachepath=$absolutcachepath."/";
            $this->relcachepath=$cachepath;
            return true;
        } else {
            // Verzeichnis exitiert nicht
            return false;
        }
    }

    protected function makeAbsolutPath($path) {
        // Macht aus einem relativen Pfad zum Projekt Root einen
        // absoluten Pfad auf dem Verzeichnis

        // Hinweis, wenn die Datei/Verzeichnis nicht existiert liefert realpath false
        return realpath((__DIR__)."/".$path);
    }

    protected function makeMinifiPath($path) {
        // Prüft ob eine minifizierte Version der Datei existiert,
        // falls ja wird diese benutzt

        // Pfad an den Punkten aufsplitten
        $splitpath = explode(".",$path);

        $newfragments = array();

        for($i=0;$i<count($splitpath);$i++) {
            if($i==(count($splitpath)-1)) {
                // Vor dem letzten Element ein "min" einfügen
                $newfragments[]="min";
            }
            $newfragments[]=$splitpath[$i];
        }

        // Puzzelteile wieder zusammenfügen
        return implode(".",$newfragments);
    }

    private function getCacheFile() {
        // Fragt an, ob eine Datei die zu der Dateisignatur der angefragten Dateinen passt,
        // schon in der Liste der Dateien ist
        $cachefilesignature = $this->calculateFileSignature();

        // Zusammenbau des Pfad, ausgehend vom Dokument root
        $cachefile = $cachefilesignature.$this->cachefileextension;

        $absolutcachepath = $this->cachepath.$cachefile;

        if(!file_exists($absolutcachepath)) {
            // Die Datei wurde noch nie in den Cache geschrieben, jetzt erzeugen -> the hard way!

            // Dateien zusammenfügen
            foreach($this->internfilelist as $file) {
                // Inhalt der aktuellen CSS Datei einlesen
                $filecontent = file_get_contents($file);

                // Um Sicherzugehen noch einen Zeilewechsel anfügen
                $filecontent .= "\n";

                // Beim schreiben Dateiinhalt anfügen und Datei zum Schreiben von anderen locken!
                if(file_put_contents($absolutcachepath, $filecontent , FILE_APPEND | LOCK_EX)===false) {
                    // Beim Schreiben der Datei ist was falsch gelaufen
                    // Cachedatei löschen und hoffen beim nächsten mal klappt es mit dem Schreiben
                    unlink($absolutcachepath);
                    return false;
                }
            }
            // Angsthasen pause
            usleep(5000);

            // jetzt noch gzip Version der Datei erzeugen, wenn wir schon mal dabei sind
            file_put_contents($absolutcachepath.".gz",gzencode(file_get_contents($absolutcachepath),9));
        }

        return  $this->relcachepath.$cachefile;
    }

    private function calculateFileSignature() {
        // Erzeugt aus allen Dateien im internen Array eine Signatur
        // Hierz wird der Dateiname+Änderungsdatum der Datei verrechnet und
        // als md5 zurückgegeben
        $tempstingbase = "";

        foreach($this->internfilelist AS $file) {
            $tempstingbase .= $file."->";
            $tempstingbase .= "(".filemtime($file).") ";
        }

        // simple md5 should do, no secure risk
        return md5($tempstingbase);
    }

    // TODO: build cleanup function for old cached files
}