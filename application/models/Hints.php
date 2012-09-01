<?php
require_once "Gb/Cache.php";

/*
Structure of the file:

---BEGIN FILE---
HTCConverter Hints File

THE BEGINNING
(0,255,255)
You are very close to the surface.
Each team has its flaws and strengths.
Take the four guides for your first exploration.
Another time specialize in a guild to obtain great and quick firepower.
Even if you already have 4 characters, you may obtain some benefits by raising in the ranks of the guilds.
Some particular fates await the highest ranked members.
The hidden characters have hidden powers.

ORACLE
(0,5,9)(0,6,9)(0,7,9)(0,8,9)(0,5,10)(0,6,10)(0,7,10)(0,8,10)(0,6,11)(0,7,11)(0,8,11)(0,5,6)(0,5,7)(0,5,8)(0,6,8)(0,9,10)(0,8,8)(0,6,12)(0,4,9)(0,0,2)(0,3,7)(0,4,4)(0,3,9)(0,3,10)(0,3,11)(0,4,11)(0,4,12)(0,4,13)(0,4,14)(0,4,15)(0,3,13)(0,5,14)(0,3,15)(0,5,15)(0,6,15)(0,7,15)(0,8,15)(0,9,15)(0,3,6)(0,2,6)(0,3,5)(0,8,7)(0,8,6)(0,8,5)(0,8,4)(0,8,3)(0,7,7)(0,7,5)(0,9,3)(0,10,3)(0,11,3)(0,10,4)(0,10,5)(0,10,6)(0,10,7)(0,10,8)(0,0,5)(0,1,5)(0,1,4)(0,1,6)(0,1,3)(0,2,4)(0,4,5)(0,6,13)(0,7,13)(0,8,13)(0,9,13)(0,9,12)(0,10,13)(0,11,13)(0,12,13)(0,13,13)(0,14,13)(0,11,14)(0,12,14)(0,13,14)(0,14,14)(0,13,15)(0,14,15)(0,11,15)(0,14,16)(0,14,12)(0,14,11)(0,14,10)(0,14,9)(0,14,8)(0,3,3)(0,0,3)(0,3,4)(0,10,10)(0,10,11)(0,11,11)(0,12,11)(0,12,10)(0,12,9)(0,11,9)(0,12,8)(0,12,7)(0,13,7)(0,12,6)(0,12,5)(0,5,5)(0,5,4)(0,5,3)(0,6,3)
See now the words I've woven true,/Wordweaver I am, as I'm known now to you./I speak of great terrors from far below ground,/Within the Undercity, where they do abound.
---END FILE---
*/


Class Hints {
    protected $_aHints;              // array( hintId=>array(title, cellsForLevels, text), ... )
                                     // with cellsForLevels = array( level=>sCells, ... )
    protected $_aHintIdsByLevels;    // array( level=>array(hintId, ...) )
    protected $_isLoaded = false;    // true when hints are available
    
    public function __construct($filename) {
        if ( ! (is_file($filename) && is_readable($filename) ) ) {
            return;
        }
        
        // set the following FALSE to TRUE to invalidate file. You can also touch the hint file
        $cache = new Gb_Cache("dmhints_".$filename, $filename, FALSE);    // cache filename based on modification time

        if (isset($cache->aHints)) {
            $this->_aHints           = $cache->aHints;
            $this->_aHintIdsByLevels = $cache->aHintIdsByLevels;
        } else {
            $text = file_get_contents($filename);
            $this->_loadFromText($text);
            
            $cache->aHints           = $this->_aHints;
            $cache->aHintIdsByLevels = $this->_aHintIdsByLevels;
        }
        
        $this->_isLoaded = true;
    }
    
    
    
    public function isLoaded() {
        return $this->_isLoaded;
    }
    
    
    /**
     * Get all hints for a level
     * @param integer $level
     * @return array(hintid=>array(title, sCells, text)
     */
    public function getHintsForLevel($level) {
        if (!$this->isLoaded()) {
            return array();
        }
        
        if (empty($this->_aHintIdsByLevels[$level])) {
            return array();
        }
        
        $aHints   = null;
        $aHintIds = $this->_aHintIdsByLevels[$level];
        foreach ($aHintIds as $hintid) {
            $aHints[$hintid] = array(
                $this->_aHints[$hintid][0],            // title
                $this->_aHints[$hintid][1][$level],    // sCells for that level (because a hint can span multiple levels)
                $this->_aHints[$hintid][2]             // text
            );
        }
        
        return $aHints;
    }
    
    
    
    /**
     * Get a hint by its hintid
     * @param integer $hintid
     * @return array(title, array(level=>sCells, ...), text)
     */
    public function getHint($hintid) {
        return $this->_aHints[$hintid];
    }
    
    
    
    /**
     * Called by constructor
     * @param string $text
     */
    protected function _loadFromText($text) {
        $text = str_replace("\r", "", $text);
        $aLines = explode("\n", $text);

        if (count($aLines)<4) {
            return;
        }
        
        // remove header
        if ($aLines[0] == "HTCConverter Hints File") {
            array_shift($aLines);
            if ($aLines[0] == "") {
                array_shift($aLines);
            }
        }
        
        $aHints = $this->_parseStage1($aLines);
        $aHints = $this->_parseStage2($aHints);
        $this->_aHints = $aHints;
        $this->_aHintIdsByLevels = $this->_getHintIdsForLevels($aHints);
    }
    
    
    
    /**
     * Called by constructor
     * @param array $aLines lines of the hint files
     * @return array( array(title, rawcells, text), ...)
     */
    protected function _parseStage1(array $aLines) {
        $aHints = null;
        $nLines = count($aLines);
        for ($i = 0; $i<$nLines; $i++) {
            $title = $aLines[$i++];
            $cells = $aLines[$i++];
            $text  = "";
            for (; $i<$nLines; $i++) {
                $line = $aLines[$i];
                if (strlen($line) == 0) {
                    break;
                }
                if (strlen($text)) {
                    $text .= "//";
                }
                $text .= $line;
            }
            
            $text = str_replace("/", "<br />", $text);
            $aHints[] = array($title, $cells, $text);
        }
        
        return $aHints;
    }
    
    
    
    /**
     * Called by constructor
     * @param array $aHints array( array(title, rawcells, text), ...)
     * @return array( hintId=>array(title, array( level=>sCells, ... ), text), ... )
     */
    protected function _parseStage2(array $aHints) {
        // STAGE 2 : parse the cells
        $nHints = count($aHints);
        for ($i=0; $i<$nHints; $i++) {
            $sCells = $aHints[$i][1];
            $aCells = $this->_parseStage2ProcessCells($sCells);
            $aCells = $this->_parseStage2CompressCells($aCells);
            $aHints[$i][1] = $aCells;
        }
        
        return $aHints;
    }
    
    
    
    /**
     * Transform "(0,1,2)(0,4,5)(6,7,8)" into array(0=>array(array(1,2), array(4,5)), 6=>array(array(7,8)))
     * Called by constructor
     * @param string $sCells
     * @return array array(level=>array(array(x,y), ...), ...)
     */
    protected function _parseStage2ProcessCells($sCells) {
        $regexp = '/\\((\\d+,\\d+,\\d+)\\)/';
        $aMatches = null;
        preg_match_all($regexp, $sCells, $aMatches);
        // $aMatches = array(0 => array("(0,1,2)", "(0,4,5)", "(6,7,8)" ),
        //                   1 => array("0,1,2",   "0,4,5",   "6,7,8"   ))
        
        $aCells = null;
        $z = $x = $y = null;
        $aMatches = $aMatches[1];    // array("0,1,2",   "0,4,5",   "6,7,8"   ))
        foreach ($aMatches as $match) {
            list($z, $x, $y) = explode(",", $match);
            $aCells[$z][] = array($x, $y);
        }
        
        return $aCells;
        
    }
    
    
    /**
     * Called by constructor
     * @param array $aCellsByLevel array(level=>array(array(x,y), ...), ...)
     * @return array array(level=>sCells, ...)
     */
    protected function _parseStage2CompressCells(array $aCellsByLevel) {
        $aCells2 = null;
        foreach ($aCellsByLevel as $level=>$aCells) {
            $sCells = $this->_compressCells($aCells);
            $aCells2[$level] = $sCells;
        }
        return $aCells2;
    }
    
    
    
    /**
     * process the hints to index per level
     * @param array $aHints array(hintid=>array(title, array(level=>aCells, ...), text), ...)
     * @return array array(level=>array(hintid, ...), ...)
     */
    protected function _getHintIdsForLevels($aHints) {
        $aHintIdsByLevels = null;
        
        foreach ($aHints as $hintId=>$aHint) {
            $aCellsByLevels = $aHint[1];
            foreach (array_keys($aCellsByLevels) as $level) {
                $aHintIdsByLevels[$level][] = $hintId;
            }
        }
        
        return $aHintIdsByLevels;
    }
    
    
    
    /**
     * Compress the cells so that they take less space.
     * @param array $aCells array(array(1,1), array(1,3), array(1.4), array(1,5))
     * @return string "[1,1],[[1,3],[1,5]]" or ""
     */
    protected function _compressCells($aCells) {
        if (count($aCells)==1 && $aCells[0][0]==255 && $aCells[0][1]==255) {
            return "";
        } 
        // build a 32x32 bitmap filled with 0
        $aBitmap = null;
        for ($x=0; $x<32; $x++) {
            for ($y=0; $y<32; $y++) {
                $aBitmap[$x][$y] = 0;
            }
        }
        
        // fill this bitmap with 1 for the cells
        foreach ($aCells as $aCell) {
            $aBitmap[$aCell[0]][$aCell[1]] = 1;
        }
        
        // visual dump
        //for ($y=0; $y<32; $y++) { $line = ""; for ($x=0; $x<32; $x++) { $line .= $aBitmap[$x][$y]; } echo $line."\n"; }
        
        
        
        $aZones = null;
        
        for ($y1=0; $y1<32; $y1++) {
            for ($x1=0; $x1<32; $x1++) {
                // browse every cells
                if ($aBitmap[$x1][$y1] !== 1) {
                    // ignore this cell. Can be a 0 cell or a null cell
                    continue;
                }
                
                // current square : square : [ x1,y1 ] - [ (x2),(y2) ]
                // try to find a square. Begin to find x2
                for ($x2=$x1+1; $x2<32; $x2++) {
                    if ($aBitmap[$x2][$y1] === 0) {
                        // found a 0 cell : it should not be included.
                        break;
                    }
                    $aBitmap[$x2][$y1] = null;    // consider the cell as "done"
                }
                $x2--;
                
                // current square : square : [ x1,y1 ] - [ x2,(y2) ]
                // try to find a square. Begin to find y2
                $broke = false;
                $y2 = $y1;
                for ($y=$y1+1; $y<32; $y++) {    // begin at the next line
                    for ($x=$x1; $x<=$x2; $x++) {
                        // check horizontally
                        if ($aBitmap[$x][$y] === 0) {
                            $broke = true;
                            break;
                        }
                    }
                    if ($broke) {
                        break;
                    }
                    // line is ok
                    $y2++;
                    for ($x=$x1; $x<=$x2; $x++) {
                        $aBitmap[$x][$y] = null;        // consider the line treated
                    }
                    
                }
                
                if ($x1===$x2 && $y1===$y2) {
                    $zone = "[$x1,$y1]";
                } elseif ( (($x2 === $x1+1) && ($y1===$y2))
                        || (($y2 === $y1+1) && ($x1===$x2))
                         ) {
                    $zone = "[$x1,$y1],[$x1,$y1]";
                } else {
                    $zone = "[[$x1,$y1],[$x2,$y2]]";
                }
                $aZones[] = $zone;
            }
        }
        
        return implode(",", $aZones);
    }

}




//$Hints = new Hints("hcsb.txt");

//$aLevelHints = $Hints->getHintsForLevel(7);
//foreach ($aLevelHints as $hintid=>$aHint) {
//    print "$hintid: {$aHint[0]}:\n{$aHint[2]}\n"; 
//}

//$text = "(4,0,0)(4,31,22)(4,31,21)(4,31,20)(4,31,19)(4,31,18)(4,31,17)(4,31,16)(4,31,14)(4,31,13)(4,31,12)(4,31,11)(4,31,10)(4,31,9)(4,31,8)(4,31,6)(4,31,5)(4,31,4)(4,31,3)(4,31,2)(4,31,1)(4,31,0)(3,31,29)(3,31,30)(3,31,31)(3,30,30)(3,29,30)(4,31,31)(4,31,30)(4,31,29)(5,31,31)(5,31,30)(5,31,29)(5,31,28)(5,31,16)(5,31,15)(5,31,14)(5,30,11)(5,30,10)(5,31,10)(5,30,9)(5,31,8)(5,31,7)(5,31,6)(5,30,5)(5,30,4)(5,30,3)(5,29,3)(5,31,4)(5,31,3)(5,31,2)(5,31,1)(5,31,0)(5,31,22)(5,31,21)(5,31,20)(5,31,19)(5,31,18)(5,30,18)(5,30,17)(5,30,21)(6,31,28)(6,31,27)(6,30,27)(6,30,26)(6,29,26)(6,29,25)(6,30,25)(6,31,25)(6,31,26)(6,31,24)(6,30,24)(6,31,23)(6,31,22)(6,31,21)(6,31,20)(6,31,19)(6,31,18)(6,31,17)(6,30,17)(6,31,16)(6,31,15)(6,31,14)(6,31,13)(6,31,12)(6,30,12)(6,30,13)(6,30,11)(6,31,10)(6,30,9)(6,30,8)(6,31,8)(6,31,7)(6,31,6)(6,31,5)(6,31,4)(6,31,3)(6,31,2)(6,31,1)(6,31,0)(6,30,0)(6,30,1)(6,30,2)(6,29,1)";
//$text2 = $Hints->_parseStage2ProcessCells($text);
//echo $text2;



//$aHint = $Hints->getHintsForLevel(25);
//$zones = compressCells($aCells);
//print_r($aHint);
//echo "$zones\n";


/*
$level = 6;
$aHints = $Hints->getHintsForLevel($level);

foreach ($aHints as $hintid=>$aHint) {
//    if ($hintid!=25) continue;
    $title = $aHint[0];
    $aCells = $aHint[1][$level];
    $text = $aHint[2];
    $compress = compressCells($aCells);
    echo "<dmhint><span class='title'>$title</span><span class='text'>$text</span><span class='cells'>$compress</span></dmhint>\n";
}
*/

