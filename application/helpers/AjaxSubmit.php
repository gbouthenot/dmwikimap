<?php

/*
 * Attention: pour utilisation avec du texte (notament textarea, il faut encoder le texte avec
 * "commentaire=\"+encodeURIComponent(\$F(\"commentaire\"))+\""
 * et le décoder depuis php avec utf8_decode($_POST["commentaire"])
 */

//class Zend_View_Helper_AjaxSubmit
//{
    
    /**
     * Renvoie un <input type='submit' dont le résultat sera affiché dans un emplacement ajax
     *
     * @param string $url                Url à appeler pour effectuer la requête.
     * @param string $targetID[optional] Element Id cible ex: "'target'"
     * @param string $text[optional]     texte à afficher sur le bouton
     * @param string $arg[optional]      Les arguments à envoyer à l'url (urlencodé) exemples:
     *                                    "{itm_code:\$F('newcomp'), commentaire: encodeURIComponent(\$F('commentaire'))}"
     *                                    "itm_code=\"+\$F(\"newcomp\")+\"" ."&". "commentaire=\"+encodeURIComponent(\$F(\"commentaire\"))+\""
     *                                    "delete=\"+getChecked()+\""
     * @param array  $aOptions[optional]
     *                "jump"       => "#jmp" si rempli alors génère un <a> au lieu d'un <input> (défini par <a name="jmp"></a>)
     *                "method"     => "POST (par défaut)|GET"
     *                "title"      => "tooltip du bouton"
     *                "class"      => "class du bouton (par défaut submit)"
     *                "id"         => "id du bouton (aucun par défaut)"
     *                "condition"  => "getNbChecked() && confirm(\"Confirmez la suppression de \"+getNbChecked()+\" formateurs\")"
     *                "else"       => "alert(\"erreur\");"
     *                "onclick"    => "console.log('click');"
     *                "moreOptions"=> "onComplete: function(){geflash('flashid');}"
     * 
     *      * @return string lien généré
     * 
     *  En php, en cas d'erreur il faut faire
     *      header("HTTP/1.0 500 Erreur xxxx");
     */
    function ajaxSubmit($url, $targetID="''", $text="", $arg="{}", array $opt=array())
    {
        //$jump='#', $targetID, $method, $url, $arg, $text, $title="", $class=null, $flashid=null, $condition=null
        
        $method="POST";
        $title="";
        $class="class='submit'";
        $id="";
        $condition="";
        $else="";
        $moreOptions="";
        $onmouseover="";
        $onmouseout="";
        $jump="";
        $onclick="";
        
        $counter=rand(1,999999);
        if (!empty($opt["title"])) {
            $title     =htmlentities($opt["title"], ENT_QUOTES);
            $onmouseover="onmouseover='window.status=\"$title\"; return true;'";
            $onmouseout ="onmouseout='window.status=\"\"; return true;'";
        }
        if (!empty($opt["method"]))                $method     =$opt["method"];
        if (!empty($opt["jump"]))                  $jump       =$opt["jump"];
        if (!empty($opt["class"]))                 $class      ="class='{$opt["class"]}'";
        if (!empty($opt["id"]))                    $id         ="id='{$opt["id"]}'";
        if (!empty($opt["moreOptions"]))           $moreOptions=", ".$opt["moreOptions"];
        if (!empty($opt["condition"]))     {       $condition  =$opt["condition"];
        if (!empty($opt["else"]))                  $else       =$opt["else"];                        }
        if (!empty($opt["onclick"]))               $onclick    =$opt["onclick"].";";
        
        
        $text=htmlentities($text, ENT_QUOTES);

        //  $onsuccess="function(transport){}";
        //  $onsuccess="function(){setTimeout(function(){alert(1);new Ajax.Autocompleter(\"tuteur_auto_input\", \"tuteur_auto_div\", \"/gestion_e/index.php?action=c2i_admin&debug=1&ajax=1&nohtml=1&autocomplete=tuteurs\", {}); alert(2); if ($(\"$flashid\")) if ($(\"$flashid\").style.display==\"none\") {Effect.SlideDown(\"$flashid\", {queue: \"end\"});} else {new Effect.Highlight(\"$flashid\", {queue: \"end\"});}},2000)}";
        //  $onsuccess="function(t, j){if (j) Object.eval(j);   geflash(\"$flashid\");}"

        $onclick.="new Ajax.Updater($targetID, \"$url\", { method: \"$method\", parameters: $arg, evalScripts: true $moreOptions});";

        if (strlen($condition)) {
            $onclick="if ( $condition ) { $onclick } else { $else }";
        }
        
        $ret="";
        $ret.="<script type='text/javascript'>
// <![CDATA[
gb_asoc{$counter}=function()
{
    {$onclick}
}
// ]]>
</script>
";
        if (strlen($jump)) {
            $ret.="<a href='$jump' $class $id title=\"$title\" onclick=\"gb_asoc{$counter}(); return false;\" $onmouseout $onmouseover>$text</a>";
        } else {
            $ret.="<input type='submit' $class $id title=\"$title\" onclick=\"gb_asoc{$counter}(); return false;\" $onmouseout $onmouseover value='$text' />";
        }

        return $ret;
    }
//}

?>