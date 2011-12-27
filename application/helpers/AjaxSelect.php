<?php


//class Zend_View_Helper_AjaxSelect
//{
    
    /**
     * Renvoie un <input type='submit' dont le résultat sera affiché dans un emplacement ajax
     *
     * @param string $url                Url à appeler pour effectuer la requête.
     * @param string $targetID[optional] Element Id cible ex: "'target'"
     * @param array  $text[optional]     valeurs à afficher array("2007"=>"en 2007-2008")
     * @param string $arg[optional]      Les arguments à envoyer à l'url (urlencodé) exemples:
     *                                    "{itm_code:\$F('newcomp'), commentaire: encodeURIComponent(\$F('commentaire'))}"
     *                                    "itm_code=\"+\$F(\"newcomp\")+\"" ."&". "commentaire=\"+encodeURIComponent(\$F(\"commentaire\"))+\""
     *                                    "delete=\"+getChecked()+\""
     * @param array  $aOptions[optional]
     *                "method"     => "POST (par défaut)|GET"
     *                "class"      => "class du bouton (par défaut submit)"
     *                "id"         => "id du bouton (aucun par défaut)"
     *                "condition"  => "getNbChecked() && confirm(\"Confirmez la suppression de \"+getNbChecked()+\" formateurs\")"
     *                "else"       => "alert(\"erreur\");"
     *                "moreOptions"=> "onComplete: function(){geflash('flashid');}"
     *                "selected"   => "2007"
     * 
     *      * @return string lien généré
     * 
     *  En php, en cas d'erreur il faut faire
     *      header("HTTP/1.0 500 Erreur xxxx");
     */
    function ajaxSelect($url, $targetID="''", $aOptions=array(), $arg="{}", array $opt=array())
    {
        //$jump='#', $targetID, $method, $url, $arg, $text, $title="", $class=null, $flashid=null, $condition=null
        
        $counter=rand(1,999999);
        $method="POST";
        $class="class='select'";
        $id="";
        $condition="";
        $else="";
        $moreOptions="";
        $onclick="";
        $selected="";
        
        if (!empty($opt["method"]))                $method     =$opt["method"];
        if (!empty($opt["class"]))                 $class      ="class='{$opt["class"]}'";
        if (!empty($opt["id"]))                    $id         ="id='{$opt["id"]}'";
        if (!empty($opt["moreOptions"]))           $moreOptions=", ".$opt["moreOptions"];
        if (!empty($opt["condition"]))     {       $condition  =$opt["condition"];
        if (!empty($opt["else"]))                  $else       =$opt["else"];                        }
        if (!empty($opt["selected"]))              $selected   =$opt["selected"];

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

        $ret.="<select $class $id onchange=\"gb_asoc{$counter}(); return false;\">";
        foreach ($aOptions as $val=>$text) {
            if ($val==$selected) {
                $fselected="selected='selected'";
            } else {
                $fselected="";
            }
            $valhtml=htmlentities($val);
            $texthtml=htmlentities($text);
            $ret.="<option value='$valhtml' $fselected>$texthtml</option>";
        }
        $ret.="</select>";
        
        return $ret;
    }
//}
