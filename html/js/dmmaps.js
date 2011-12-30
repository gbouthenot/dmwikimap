"use strict";
var DmmapZones = (function() {

   /**
    * event handler : display the overlay
    * private static method
    */
    var _dmzoneOn = function(that, event, domevent) {
        // get the zone data
        var dmzone = domevent.getAttribute("dmzone");
        dmzone     = eval(dmzone);
        //console.log("dmzone=", dmzone);
        
        // get an array filled with 0 (darken everything)
        var tab = _getEmptyArray(window.mapWidth, window.mapHeight);
        //console.log("tab=", tab);

        // fills the zones with 1 (transparent)
        dmzone.each(function(coords){
            var x1  = coords[0][0];
            var y1  = coords[0][1];
            // by default : coord2 = coord1 (draw a single cell)
            var x2  = x1;
            var y2  = y1;
            var x, y, n;
            if (2 == coords.length) {
                // 2 coordinates : draw a box of cells
                var x2  = coords[1][0];
                var y2  = coords[1][1];
            }
            for (y=y1; y<=y2; y++) {
                for (x=x1; x<=x2; x++) {
                    var n  = y*window.mapHeight + x;
                    tab[n] = 1;
                }
            }
        });
        //console.log("tab=", tab);

        var domTable  = $$("#overlaymap table.map tbody")[0];
        var newTable  = _createDom(tab);
        domTable.replace(newTable);

        domTable      = $("overlaymap");
        domTable.show();
//        domTable.appear({duration:.4, to:.75});
    };


   /**
    * Event handler: remove the overlay
    * private static method
    */
    var _dmzoneOff = function(that, event, domevent) {
        var domTable = $("overlaymap");
        domTable.hide();
//        domTable.fade({duration:.4});
    };



   /**
    * render a new tbody with the array specified
    * @returns domnode <tbody>
    * private static method
    */
    var _createDom = function(tab) {
        var newTable  = document.createElement("tbody");
        var emptyRow  = document.createElement("tr");
        var emptyCell = document.createElement("td");
        var darkCell  = document.createElement("td");
        darkCell.setAttribute("style", "background:#000;");

        emptyRow.insert(emptyCell.clone());
        emptyRow.insert(emptyCell.clone());

        // first 2 rows
        newTable.insert(emptyRow.cloneNode(true));
        newTable.insert(emptyRow.cloneNode(true));

        var x,y;
        var cellvalue;
        var newCell;
        var newRow;
        for (y=0; y<window.mapHeight; y++) {
            newRow = emptyRow.cloneNode(true);
            for (x=0; x<window.mapWidth; x++) {
                cellvalue = tab[y*window.mapWidth + x];
                if (cellvalue) {
                    newCell = emptyCell.clone();
                } else {
                    newCell = darkCell.clone();
                }
                newRow.insert(newCell);
            }
            newTable.insert(newRow);
        }

        return newTable;
    };



   /**
    * returns a array filled with 0
    * @returns array
    * private static method
    */
    var _getEmptyArray = function(width, height) {
        var a = new Array();
        var x,y;
        for (y=0; y<height; y++) {
            for (x=0; x<width; x++) {
                a.push(0);
            }
        }
        return a;
    };



   /**
    * the constructor
    * (returned, hosts the priveleged methods)
    */
    var __construct = function() {
    };



   /**
    * initializer : position the overlaymap, install event handlers
    * privileged static method
    */
    __construct.init = function(){
        // get position of the overlay
        var tbody = $$("div.mainmap table.map tbody")[0];
        var left  = tbody.getClientRects()[0].left;
        var top   = tbody.getClientRects()[0].top;

        // position the overlay
        var overlaymap = $("overlaymap");
        overlaymap.setStyle( { top:top+"px", left:left+"px"} );

        // delegate div#leveloverview -> span.dmzone:
        var div = $$("div#leveloverview")[0];
        div.on("mouseover", "span.dmzone", _dmzoneOn.curry(this));
        div.on("mouseout", "span.dmzone", _dmzoneOff.curry(this));
    };



    return __construct;
})();







var DmmapMap = (function() {
    // private static variables
    var _mode;      // "view|edit"




   /**
    * render a new cell
    * @returns domnode <td>
    * private static method
    */
    var _renderNewCell = function(celltype) {
        var cell = document.createElement("td");
        var background;

        background = window.tilePrefix + celltype + window.tilePostfix;
        cell.setAttribute("background", background);
        return cell;
    };



   /**
    * render the map
    * @returns domnode <tbody>
    * private static method
    */
    var _render = function() {
        var x, y;
        var newTable  = document.createElement("tbody");
        var td, tr;
        var firstrow, secondrow;

        //first row
        tr = document.createElement("tr");
        tr.insert(_renderNewCell(0));
        tr.insert(_renderNewCell(0));
        for (x=0; x<window.mapWidth; x++) {
            td = _renderNewCell(0);
            if (x%10 == 0) {
                td.setStyle({fontSize:"x-small"});
                td.innerHTML = x;
            }
            tr.insert(td);
        }
        tr.insert(_renderNewCell(0));
        tr.insert(_renderNewCell(0));
        newTable.insert(tr);
        firstrow = tr;

        //second row
        tr = document.createElement("tr");
        tr.insert(_renderNewCell(0));
        tr.insert(_renderNewCell(0));
        for (x=0; x<window.mapWidth; x++) {
            td = _renderNewCell(0);
            if (x%10) { td.innerHTML = x%10; }
            tr.insert(td);
        }
        tr.insert(_renderNewCell(0));
        tr.insert(_renderNewCell(0));
        newTable.insert(tr);
        secondrow = tr;


        var tileId = 0;
        var firstcell, secondcell;
        for (y=0; y<window.mapHeight; y++) {
            var celltype;

            tr = document.createElement("tr");

            //first cell
            td = _renderNewCell(0);
            if (y%10 == 0) {
                td.setStyle({fontSize:"x-small"});
                td.innerHTML = y;
            }
            tr.insert(td);
            firstcell = td;

            //second cell
            td = _renderNewCell(0);
            if (y%10) {td.innerHTML = y%10;}
            tr.insert(td);
            secondcell = td;

            for (x=0; x<window.mapWidth; x++) {
                celltype = window.tileIds[tileId];
                td = _renderNewCell(celltype);
                td.setAttribute("id", "cell-" + tileId);
                if (("view" == _mode) && ((3 == celltype) || (4 == celltype) || (5 == celltype)) ) {
                    td.setStyle({cursor:"pointer"});
                }
                tr.insert(td);
                tileId++;
            }

            tr.insert(firstcell.cloneNode(true));
            tr.insert(secondcell.cloneNode(true));

            newTable.insert(tr);
        }

        //last rows : same as first ones
        newTable.insert(firstrow.cloneNode(true));
        newTable.insert(secondrow.cloneNode(true));

        return newTable;
    };


   /**
    * the constructor
    * (returned, hosts the privileged methods)
    */
    var __construct = function() {
    };



   /**
    * @var mode string "view|edit"
    * privileged static method
    */
    __construct.init = function(mode) {
        _mode       = mode;
        this.render();
    }



   /**
    * render the map
    * privileged static method
    */
    __construct.render = function(mode) {
        var mainmap = $$(".mainmap table.map tbody")[0];
        var newmap  = _render();
        mainmap.replace(newmap);
    }


    return __construct;
})();







var DmmapTips = (function() {
    // private static variables
    var _currentId = null;

   /**
    * install a tip (setting the right background for the cell)
    * @returns nothing
    * private static method
    */
    var _install = function(div) {
        var id = div.getAttribute("id");            // id is "hover-box-nnn"
        if (id.substr(0, 10) != "hover-box-") {
            return;
        }

        var tileId = parseInt(id.substr(10));
        var celltype = window.tileIds[tileId];
        id = "cell-" + tileId                       // id is "cell-nnn" (the id of the td cell)

        var cell = $(id);
        var background;

        background = window.tilePrefix + (100+celltype) + window.tilePostfix;
        cell.setAttribute("background", background);
    };



   /**
    * the constructor
    * (returned, hosts the privileged methods)
    */
    var __construct = function() {
    };



   /**
    * privileged static method
    */
    __construct.init = function() {
        this.render();
    };



   /**
    * privileged static method
    */
    __construct.render = function() {
        var hoverboxes = $$("#hover-boxes div");
        hoverboxes.each(_install);
    };



   /**
    * event called by a click
    * privileged static method
    */
    __construct.click = function(tileId, event) {
        var celltype = window.tileIds[tileId];

        if ( (3 == celltype) || (5 == celltype) ) {
            window.levelDown();
        } else if (4 == celltype) {
            window.levelUp();
        }
    };



   /**
    * event
    * privileged static method
    */
    __construct.showTip = function(tileId, event) {
        var hoverbox = $("hover-box-" + tileId);
        if (null == hoverbox) { return; }

        var x = event.clientX+10;
        var y = event.clientY+10;
        hoverbox.setStyle({top:y + "px", left:x + "px", display:"block"});
        _currentId = tileId;
    };



   /**
    * event
    * privileged static method
    */
    __construct.hideTip = function() {
        if (null == _currentId) { return; }
        var hoverbox = $("hover-box-" + _currentId);
        hoverbox.setStyle({display:"none"});
    };
    return __construct;
})();






var levelUp = function() {
    if (urllevelup.length) {
        window.location.replace(urllevelup);
    }
};

var levelDown = function() {
    if (urlleveldown.length) {
        window.location.replace(urlleveldown);
    }
};






var DmmapHandlers = (function() {
    // private static variable
    var _mode;

   /**
    * event
    * private static method
    */
    var _mouseover = function(that, event, domevent) {
        var tileId   = _getCellId(domevent);
        if (null == tileId) { return; }

        var celltype = window.tileIds[tileId];
        if ("view" == _mode) {
            DmmapTips.showTip(tileId, event);
        }
    };



   /**
    * event
    * private static method
    */
    var _mouseout = function(that, event, domevent) {
        if ("view" == _mode) {
            DmmapTips.hideTip();
        }
    };



   /**
    * event
    * private static method
    */
    var _click = function(that, event, domevent) {
        var tileId   = _getCellId(domevent);
        if (null == tileId) { return; }

        var celltype = window.tileIds[tileId];
        if ("view" == _mode) {
            DmmapTips.click(tileId, event);
        } else if ("edit" == _mode) {
            DmmapEditor.clickMap(tileId, event);
        }
    };



   /**
    * Get the cell id from the domevent
    * @returns int or null
    */
    var _getCellId = function(domevent) {
        var id = domevent.getAttribute("id");            // id is "cell-nnn" or null
        if (null == id) { return null; }
        if (id.substr(0, 5) != "cell-") { return null; }

        var tileId   = parseInt(id.substr(5));
        return tileId;
    };


   /**
    * the constructor
    * (returned, hosts the privileged methods)
    */
    var __construct = function() {
    };



   /**
    * @var mode string "view|edit"
    * privileged static method
    */
    __construct.init = function(mode) {
        _mode = mode;

        var mainmap = $$("div.mainmap table.map")[0];
        mainmap.on("mouseover", "td", _mouseover.curry(this));
        mainmap.on("mouseout" , "td", _mouseout.curry(this));
        mainmap.on("click",     "td", _click.curry(this));
    };



    return __construct;
})();






var DmmapEditor = (function() {
    // private static variable
    var _currentTool;   // cell to draw
    var _lastClickId;   // id of the last cell
    var _tileBackup;    // value of the cell before modification
    var _clickNumber;   // number of time the user has clicked the cell


   /**
    * private static method
    */
    var _setCell = function( cellId, tileId ) {
        var cell = $("cell-" + cellId);
        cell.setAttribute("background", tilePrefix + tileId + tilePostfix);

        tileIds[cellId] = tileId;
    };



   /**
    * private static method
    */
    var _initPalette = function() {
        var palette = $$("#palette tbody")[0];
        while (palette.hasChildNodes()) { palette.removeChild(palette.lastChild); }

        var tr = document.createElement("tr");
        var celltype;
        for (celltype=0; celltype<=window.tileCount; celltype++) {
            var td = document.createElement("td");
            var background = window.tilePrefix + (celltype) + window.tilePostfix;
            td.setAttribute("id", "palette-" + celltype);
            td.setAttribute("background", background);
            tr.insert(td);
        }
        palette.insert(tr);

        palette.on("click", "td", _clickPalette.curry(this));
    };



   /**
    * click handler
    * private static method
    */
    var _clickPalette = function(that, event, domevent) {
        // domevent: td
        var id = domevent.getAttribute("id");            // id is "palette-nnn" or null
        if (null == id) { return null; }
        if (id.substr(0, 8) != "palette-") { return null; }

        var tileId   = parseInt(id.substr(8));
        __construct.setTool(tileId);

        return tileId;
    };



   /**
    * the constructor
    * (returned, hosts the privileged methods)
    */
    var __construct = function() {
    };



   /**
    * event called by a click
    * privileged static method
    */
    __construct.clickMap = function(tileId, event) {
        var currentTile = window.tileIds[tileId];

        if (tileId != _lastClickId) {
            _lastClickId = tileId;
            _tileBackup  = currentTile;
            _clickNumber = 0;
        }

        // 0 : currentTool
        // 1 : "1"
        // 2 : "2"
        // 3 : backup

        var drawId;

        do {
            var cycle = _clickNumber % 4;
            if (0 == cycle) {
                drawId = _currentTool;
            } else if (1 == cycle) {
                drawId = 1;
            } else if (2 == cycle) {
                drawId = 2;
            } else {
                drawId = _tileBackup;
            }
            _clickNumber++;
        } while (drawId == currentTile);

        _setCell(tileId, drawId);
    };



   /**
    * @var mode string "view|edit"
    * privileged static method
    */
    __construct.init = function(mode) {
        _initPalette();
        this.setTool(3);
    };



   /**
    * @var tool int
    * privileged static method
    */
    __construct.setTool = function(tool) {
        var node, background;

        if (null != _currentTool) {
            // reset the palette cell
            node = $("palette-" + _currentTool);
            background = window.tilePrefix + (_currentTool) + window.tilePostfix;
            node.setAttribute("background", background);
        }

        _currentTool = tool;

        // set the palette cell
        node = $("palette-" + _currentTool);
        background = window.tilePrefix + (_currentTool+100) + window.tilePostfix;
        node.setAttribute("background", background);
    }



    return __construct;
})();





/**
 * static class managing versions. Should be called *before* DmmapMap !
 */
var DmmapVersions = (function() {
    // private static variables
    var _currentNum;

   /**
    * switch to the selected version. You should call a map redraw after
    * private static method
    * @var num index of the version in aVersions (not the mapid)
    */
    var _selectVersion = function( num ) {
        var aVersions  = window.aVersions;
        window.tileIds = eval(aVersions[num].cells);
        window.mapId   = aVersions[num].id;
    };

   /**
    * Update the url in the "switch to edit mode" link
    * private static method
    * @var num index of the version in aVersions (not the mapid)
    */
    var _updateEditLinkHref = function( num ) {
        var anchor     = $("switchtoeditmode");
        var url        = window.urlBase + "edit/";
        if (0 == num ) {
            // last version
            url       += window.dungeonname + "/" + window.level + "/";
        } else {
            url       += "mapid/" + window.mapId + "/";
        }
        anchor.setAttribute("href", url);
    };



   /**
    * private static method
    */
    var _change = function(that, event, domevent) {
        var num = domevent.value;
        if (num != _currentNum) {
            _currentNum = num;
            _selectVersion(num);
            _updateEditLinkHref(num);
            DmmapMap.render();
            DmmapTips.render();
        }
    };



   /**
    * the constructor
    * (returned, hosts the privileged methods)
    */
    var __construct = function() {
    };



   /**
    * privileged static method
    */
    __construct.init = function() {
        _updateEditLinkHref(0);

        if (window.aVersions.length > 0) {
            _selectVersion(0);

            var node = $("versions");
            while (node.hasChildNodes()) { node.removeChild(node.lastChild); }
            node.insert("Versions: ");

            var select = document.createElement("select");
            var num    = 0;
            window.aVersions.each(function(ver){
                var option = document.createElement("option");
                var text   = ver.user_name + ": " + ver.comment + " (" + ver.datemodif + ")";
                option.setAttribute("value", num);
                option.insert(text);
                select.insert(option);
                num++;
            });
            node.insert(select);

            // install event handler
            select.on("change", _change.curry(this));
            select.on("keyup", _change.curry(this));
        }
    };



    return __construct;
})();
