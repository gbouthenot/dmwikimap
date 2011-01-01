<?php

require_once("Gb/Db.php");


Class Map
{

    /**
     * @var Gb_Db
     */
    protected $_db;
    
    /**
     * @var array [level] = array( array(array(x,y),"note")),... )
     */
    protected static $_notes;
    
    protected static $_comments;

    protected $_dungeonName;
    protected $_levelNumber;
    protected $_size;
    protected $_cells;
    protected $_versions;
    protected $_mapid;
    protected $_mapuserid;
    protected $_mapuserdatemodif;
    protected $_mapusername;
    protected $_mapusercomment;
    
    protected function _initDb()
    {
        $this->_db=new Gb_Db(
            array(
                "type"=>"Mysql",
                "host"=>"localhost",
                "user"=>"DBUSER",
                "pass"=>"DBPASS",
                "name"=>"DBNAME"
            )
        );
    }
    
    protected function _getWikiPage($pagetitle)
    {
        $sql="
            SELECT old_text
            FROM page
            JOIN revision ON rev_id=page_latest AND rev_page=page_id
            JOIN text on old_id=rev_text_id
            WHERE page_namespace=0 AND page_title=?
        ";
        
        $page=$this->_db->retrieve_one($sql, array($pagetitle), "old_text");
        if ($page===false) {
            return "";
        }
        
        return utf8_decode($page);
    }
    
    protected function _getCells($dungeonName, $levelNumber)
    {
        $levelNumber=(int) $levelNumber;
        if ($levelNumber>99) {
            throw new Exception("levelNumber $levelNumber incorrect");
        }
        // Level should be 2-char long, (00 to 99)
        $num=str_pad($levelNumber, 2, "0", STR_PAD_LEFT);
        $levelid="$dungeonName/Level$num";
        
        $aVersions=array();
        
        $sql="SELECT cma_id, cma_user_id, user_name, cma_datemodif, cma_comment, cma_cells FROM cellmap JOIN user ON user_id=cma_user_id WHERE cma_levelid=? ORDER BY cma_id DESC";
        $page=$this->_db->retrieve_all($sql, array($levelid));
        if (count($page)==0) {
            $cells=implode(",", array_fill(0, 1024, "1"));
        } else {
            $cells=null;
            foreach ($page as $l) {
                $aVersions[]=array(
                    "id"=>$l["cma_id"],
                    "user_name"=>utf8_decode($l["user_name"]),
                    "datemodif"=>$l["cma_datemodif"],
                    "comment"=>  utf8_decode($l["cma_comment"])
                );
                if ($this->_mapid==$l["cma_id"]) {
                    $cells=$l["cma_cells"];
                    $this->_mapuserid=$l["cma_user_id"];
                    $this->_mapusername=utf8_decode($l["user_name"]);
                    $this->_mapuserdatemodif=$l["cma_datemodif"];
                    $this->_mapusercomment=utf8_decode($l["cma_comment"]);
                }
            }
            // mapid not specified: return most recent
            if (empty($this->_mapid)) {
                $this->_mapid=$page[0]["cma_id"];
                $this->_mapuserid=$page[0]["cma_user_id"];
                $this->_mapusername=utf8_decode($page[0]["user_name"]);
                $this->_mapuserdatemodif=$page[0]["cma_datemodif"];
                $this->_mapusercomment=utf8_decode($page[0]["cma_comment"]);
                $cells=$page[0]["cma_cells"];
            }
        }
        
        $this->_versions=$aVersions;
        return utf8_decode($cells);
    }
    
    protected function _setCells($dungeonName, $levelNumber, $cells, $comment)
    {
        $levelNumber=(int) $levelNumber;
        if ($levelNumber>99) {
            throw new Exception("levelNumber $levelNumber incorrect");
        }
        // Level should be 2-char long, (00 to 99)
        $num=str_pad($levelNumber, 2, "0", STR_PAD_LEFT);
        
        $levelid="$dungeonName/Level$num";
        $this->_db->insert("cellmap", array(
            "cma_levelid"=>$levelid,
            "cma_cells"=>$cells,
            "cma_user_id"=>Auth::getId(),
            "cma_datemodif"=>new Zend_Db_Expr("NOW()"),
            "cma_comment"=>$comment
        ));
        $this->_cells=$cells;
    }
    
    
    protected function _initCells($dungeonName, $levelNumber)
    {
        $cells=$this->_getCells($dungeonName, $levelNumber);
        $aCells1=explode(",", $cells);
        

        if (count($aCells1)==32*32) {
            $w=$h=32;
        } else {
            throw new Exception("Unknown map size");
        }
        
        $this->_size=array($w, $h);
        
/*
        for ($y=0; $y<$h; $y++) {
            for ($x=0; $x<$w; $x++) {
                $aCells2[$y][$x]=$aCells1[$y*$w+$x];
            }
        }
        
        $this->cells=$aCells2;
*/
        $this->_cells=$aCells1;
    }
    
    protected function _initNotes($dungeonName)
    {
        if (self::$_notes !== null) {
            return;
        }
//        $wikipage=$this->_getWikiPage("$dungeonName/Levels_notes");
        
        $cacheWiki=new Gb_Cache("CacheWikiPage$dungeonName", 30);
        
        if (!isset($cacheWiki->wikipage)) {
            $wikipage="";
            $url="http://dmwiki.atomas.com/w/api.php?action=parse&format=php&page=".$dungeonName."/Levels_notes";
            Gb_Response::$footer.="file_get_contents()\n";
            $comments=file_get_contents($url);
            $comments=unserialize($comments);
            if (isset($comments["parse"]["text"]["*"])) {
                $wikipage=$comments["parse"]["text"]["*"];
            }
            $cacheWiki->wikipage=$wikipage;
        }
        $wikipage=$cacheWiki->wikipage;
        
        $cacheNotes=new Gb_Cache("CacheWikiNotes".md5($wikipage), 9999);
        
        if (!isset($cacheNotes->notes)) {
            $wikipage=str_replace("</p>","\n"  ,  $wikipage);
            $wikipage=str_replace("<p>", "",      $wikipage);
            $wikipage=str_replace("\n\n","\n",    $wikipage);
            $wikipage=str_replace("\n\n","\n",    $wikipage);
            $wikipage=str_replace("\n\n","\n",    $wikipage);
            $wikipage=str_replace("\n\n","\n",    $wikipage);
            
            /*
            $wikipage="
            bla\n
            bla\n
            {4,5,6}blabla\n
            {4,5,6}blabla<br>\n
            {1,2,3}     TTTTTTTTTTTT <br><br /><br><br /><br><br /><br>\n
            {7,8,99}    TTTTTTTTTTTT <br><br /><br><br /><br><br />\n
            ";
            */
            
            $lines=array();
            preg_match_all('/^\{(\d{1,2},\d{1,2},\d{1,2})\}\s*(.+?)\s*(?:<br>|<br \/>)*$/m', $wikipage, $lines);
            // Commentaire regexp:
            //        /^                              : début de ligne
            //        \{(\d{1,2},\d{1,2},\d{1,2})\}   : doit commencer par {n,n,n} n=nombre entre 0 et 99 à capturer
            //        \s*                             : éventuellement des espaces
            //        (.+?)                           : le texte à capturer +?=ungreedy
            //        \s*                             : éventuellement des espaces
            //        (?:<br>|<br \/>)*               : éventuellement des <br> ou <br /> (ne pas capturer)
            //        $/                              : fin de ligne
            //        m                               : traite ligne par ligne
            
            //echo print_r($lines,true);
            
            $aNotes=array();
            foreach ($lines[1] as $index=>$coords) {
                list($level, $x, $y)=explode(",", $coords);
                $text=$lines[2][$index];
                //$aNotes[$level][$x][$y]=$text;
                $aNotes[$level][]=array(array($x, $y), $text);
            }
            $cacheNotes->notes=$aNotes;
            
            
            
            // keep only the lines NOT starting with {
            $lines=array();
            preg_match_all('/^[^{].+/m', $wikipage, $lines);
            
            // build comments table
            $aComments=array();
            $currentlevel=null;
            foreach ($lines[0] as $line) {
                if ( strcasecmp(substr($line, 0, 15), '<a name="Level_') == 0 ) {
                    $currentlevel=(int) substr($line, 15, 2);
                }
                //if ( strcasecmp(substr($line, 0, 11), "[[Category:") == 0 ) {continue;}
                if ($currentlevel===null) {continue;}
                $aComments[$currentlevel][]=$line;
            }
            // join comments with newline (one entry per level)
            foreach ($aComments as $level=>$comments) {
                $aComments[$level]=implode("\n", $comments);
            }
            $cacheNotes->comments=$aComments;
        }
        self::$_comments=$cacheNotes->comments;
        self::$_notes=$cacheNotes->notes;
    }
    
    
    
    
    
    
    
    
    public function __construct($dungeonName, $levelNumber=null)
    {
        $this->_initDb();

        $mapid=null;
        if ($levelNumber===null) {
            $mapid=$dungeonName;
            $sql="SELECT cma_levelid, cma_cells FROM cellmap WHERE cma_id=?";
            $page=$this->_db->retrieve_one($sql, array($mapid));
            if ($page===false) {
                throw new Exception("mapid $mapid does not exist.");
            }
            $namelevel=Gb_String::explode("/", $page["cma_levelid"]);
            $dungeonName=$namelevel[0];
            $levelNumber=substr($namelevel[1], 5);
            $this->_mapid=$mapid;
        }
        
        $dungeonName=ucfirst(strtolower($dungeonName));
        $this->_dungeonName=$dungeonName;
        $this->_levelNumber=(int) $levelNumber;
        $this->_initNotes($dungeonName);
        $this->_initCells($dungeonName, $levelNumber);
    }
    
    
    public function getNotes()
    {
        if (isset(self::$_notes[$this->_levelNumber])) {
            return self::$_notes[$this->_levelNumber];
        }
        return array();
    }

    public function getComments()
    {
        $retcomments="";
        if (isset(self::$_comments[$this->_levelNumber])) {
            $retcomments=self::$_comments[$this->_levelNumber];
        }
        return $retcomments;
    }
    
    public function getVersions()
    {
        return $this->_versions;
    }
    
    public function getDungeonName()
    {
        return $this->_dungeonName;
    }

    public function getLevelNumber()
    {
        return $this->_levelNumber;
    }
    
    public function getSize()
    {
        return $this->_size;
    }

    public function getMapId()
    {
        if ($this->_mapid) {
            return $this->_mapid;
        } else {
            return 0;
        }
    }

    public function getMapUserId()
    {
        return $this->_mapuserid;
    }

    public function getMapUserName()
    {
        return $this->_mapusername;
    }

    public function getMapUserDatemodif()
    {
        return $this->_mapuserdatemodif;
    }

    public function getMapUserComment()
    {
        return $this->_mapusercomment;
    }
    
    public function getWidth()
    {
        return $this->_size[0];
    }

    public function getHeight()
    {
        return $this->_size[1];
    }
    
    public function getCells()
    {
        return $this->_cells;
    }
    
    public function setCells($cells, $comment)
    {
        return $this->_setCells($this->_dungeonName, $this->_levelNumber, $cells, $comment);
    }

    public function delete()
    {
        $this->_db->delete("cellmap", array($this->_db->quoteInto("cma_id=?", $this->_mapid)) );
    }
    
}





?>