function remove_accents(my_string)
{
	var new_string = "";
	var pattern_accent =         new Array("é", "è", "ê", "ë", "ç", "à", "â", "ä", "ì", "î", "ï", "ù", "ò", "ô", "ó", "ö");
	var pattern_replace_accent = new Array("e", "e", "e", "e", "c", "a", "a", "a", "i", "i", "i", "u", "o", "o", "o", "o");
	if (my_string && my_string!= "") {
		my_string=my_string.toLowerCase();
		new_string = preg_replace (pattern_accent, pattern_replace_accent, my_string);
	}
	return new_string;
}
function preg_replace(array_pattern, array_pattern_replace, my_string)
{
	var new_string = String (my_string);
	for (i=0; i<array_pattern.length; i++) {
		var reg_exp= RegExp(array_pattern[i], "gi");
		var val_to_replace = array_pattern_replace[i];
		new_string = new_string.replace (reg_exp, val_to_replace);
	}
	return new_string
}





function getradio(reid) {
    var res=$$('input[type=radio][name='+$(reid).name+']').find(function(el) { return el.checked });
    if (typeof(res) == "undefined") {
        return null;
    } else {
        return res.value;
    }
}

function geflash(flashid)
{
    if ($(flashid))
    {
        if ($(flashid).style.display=="none")
        {
            Effect.SlideDown(flashid, {queue: "end"});
        }
        else
        {
            new Effect.Highlight(flashid, {queue: "end"});
        }
    }
}




// gbtooltip
// tous les elements ayant pour classe quelquechose qui commence par "gbtt", et un élément title
// se voit traiter comme un tooltip

var mytt=
{
    enable:function()
    {
        // ajoute au document un élément <span id='gbttobj' style='position:absolute;'></span>
        h=document.createElement("span");
        h.id="gbttobj";
        h.setAttribute("id","gbttobj");
        h.style.position="absolute";
        document.getElementsByTagName("body")[0].appendChild(h);

        // pour tous les éléments de class ".gbtt", appelle prepare(element)
        $$('.gbtt').each(function(elem){mytt.prepare(elem)});
    },


    prepare:function(elem)
    {
        // récupère le tooltip html
        elem.tooltip=elem.childNodes[1];
    
        elem.onmouseover=this.show;
        elem.onmouseout=this.hide;
        elem.onmousemove=this.move;
    },

    show:function(e)
    {
        document.getElementById("gbttobj").appendChild(this.tooltip);
        mytt.move(e);
    },

    hide:function (e)
    {
        var d=document.getElementById("gbttobj");
        if(d.childNodes.length>0) d.removeChild(d.firstChild);
    },

    move:function(e)
    {
        var posx=0,posy=0;
        if (e==null)
            e=window.event;
        if (e.pageX || e.pageY) {
            posx=e.pageX; posy=e.pageY;
        } else {
            if (e.clientX || e.clientY) {
                if(document.documentElement.scrollTop){
                    posx=e.clientX+document.documentElement.scrollLeft;
                    posy=e.clientY+document.documentElement.scrollTop;
                } else {
                    posx=e.clientX+document.body.scrollLeft;
                    posy=e.clientY+document.body.scrollTop;
                }
            }
        }
        document.getElementById("gbttobj").style.top=(posy+5)+"px";
        document.getElementById("gbttobj").style.left=(posx-25)+"px";
    }
}
Event.observe(window,"load",function(){mytt.enable()});

//FastInit.addOnLoad(function(){gbtt.enable()});


