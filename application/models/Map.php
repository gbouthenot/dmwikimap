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
    
    protected $_dungeonName;
    protected $_levelNumber;
    protected $_size;
    protected $_cells;
    
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
            throw new Exception("page $pagetitle not found");
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
        $sql="SELECT CellTypes FROM cellmaps WHERE LevelID=?";
        $page=$this->_db->retrieve_one($sql, array($levelid), "CellTypes");
        if ($page===false) {
            throw new Exception("page $levelid not found");
        }
        
        return utf8_decode($page);
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
        $wikipage=$this->_getWikiPage("$dungeonName/Levels_notes");

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
        self::$_notes=$aNotes;
        //echo print_r($aNotes,true);
        
    }
    
    
    
    
    
    
    
    
    public function __construct($dungeonName, $levelNumber)
    {
        $dungeonName=ucfirst(strtolower($dungeonName));
        $this->_dungeonName=$dungeonName;
        $this->_levelNumber=$levelNumber;
        $this->_initDb();
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

    public function getWidth()
    {
        return $this->_size[0];
    }

    public function getheight()
    {
        return $this->_size[1];
    }
    
    public function getCells()
    {
        return $this->_cells;
    }
    
}





?>