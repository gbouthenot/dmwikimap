/*jshint maxerr:9999 evil:true browser:true prototypejs:true white:false*/

var levelUp = function () {
    "use strict";
    if (window.urllevelup.length) {
        window.location.replace(window.urllevelup);
    }
};

var levelDown = function () {
    if (window.urlleveldown.length) {
        window.location.replace(window.urlleveldown);
    }
};






var DmmapZone = (function () {
    "use strict";
    
   /**
    * handler: mouse is on a <dmzone>
    * private static method
    */
    var _mouseover = function (that, event, domevent) {
        if (null !== DmmapOverlay.getLockElement() ) {
            return;
        }
        // get the zone data
        var dmzone = domevent.getAttribute("dmzone");
        dmzone     = eval(dmzone);

        var tab = [];
        // fills the zones with 1 (transparent)
        dmzone.each(function (coords) {
            var x1  = coords[0];
            var y1  = coords[1];
            
            if (typeof(coords[0][0]) !== "undefined" && typeof(coords[0][1]) !== "undefined") {
                x1      = coords[0][0];
                y1      = coords[0][1];
            }

            // by default : coord2 = coord1 (draw a single cell)
            var x2  = x1;
            var y2  = y1;
            var x, y, n;
            if (2 === coords.length && typeof(coords[1][0]) !== "undefined" &&  typeof(coords[1][1]) !== "undefined") {
                // 2 coordinates : draw a box of cells
                x2  = coords[1][0];
                y2  = coords[1][1];
            }
            for (y = y1; y <= y2; y++) {
                for (x = x1; x <= x2; x++) {
                    n  = y * window.mapWidth + x;
                    tab.push(n);
                }
            }
        });

        DmmapOverlay.updateCells(tab, "dark", "");
    };



   /**
    * handler
    * private static method
    */
    var _mouseout = function (that, event, domevent) {
        if (null !== DmmapOverlay.getLockElement() ) {
            return;
        }
        that.hide();
    };



    /**
     * handler to handle the locking
     * private static method
     */
    var _click = function (that, event, domevent) {
        if (null !== DmmapOverlay.getLockElement()) {
            // lock set
            if (domevent === DmmapOverlay.getLockElement()) {
                // same element : unlock but don't hide
                DmmapOverlay.unlock(false);
            } else {
                // click on a different element
                DmmapOverlay.unlock(true);
                _mouseover(that, event, domevent);
                DmmapOverlay.lock("zone", domevent);
            }
        } else {
            // lock not set
            DmmapOverlay.lock("zone", domevent);
        }
    };



   /**
    * the constructor
    * (returned, hosts the priveleged methods)
    */
    var __construct = function () {
    };



   /**
    * install handlers
    * privileged static method
    */
    __construct.init = function () {
        // delegate div#leveloverview -> span.dmzone:
        var divs = $$("div#leveloverview, #sandbox");
        divs.invoke("on", "mouseover", "span.dmzone", _mouseover.curry(this));
        divs.invoke("on", "mouseout",  "span.dmzone", _mouseout.curry(this));
        divs.invoke("on", "click",     "span.dmzone", _click.curry(this));

        var sandbox = $$("#sandbox textarea")[0];
        if ("undefined" !== typeof sandbox ) {
            Event.on(sandbox, "keyup", function(a,e){$$("#sandbox .result")[0].innerHTML=e.value.replace("<dmzone ","<span class='dmzone' ").replace(" zone="," dmzone=").replace("</dmzone>","</span>");});
        }
    };



    /**
    * hide the zone. Called by _mouseout, _click and by DmmapOverlay
    * privileged static method
    */
    __construct.hide = function () {
        DmmapOverlay.hide();
    };



    return __construct;
})(); // DmmapZone






var DmmapHint = (function () {
    "use strict";

   /**
    * handler: mouse is on a <dmhint>
    * private static method
    */
    var _mouseoverHint = function (that, event, domevent) {
        if (null !== DmmapOverlay.getLockElement() ) {
            return;
        }
        
        // get the hint text
        var text = domevent.select(".text")[0].innerHTML;
        
        // nodes
        var overlayhint = $("overlayhint");
        var levelhints  = $("levelhints");
        
        // position the overlay
        var rect = levelhints.getClientRects()[0];
        overlayhint.style.left     = (rect.left + (typeof window.pageXOffset==="undefined"?0:window.pageXOffset))       + "px";
        overlayhint.style.width    = (rect.right - rect.left - 10) + "px";  // ie6 doesn't know width
            rect = domevent.getClientRects();
        rect     = rect[ rect.length - 1 ]; // because a <span may span other multiple lines
        overlayhint.style.top      = (rect.bottom + (typeof window.pageYOffset==="undefined"?0:window.pageYOffset) + 1) + "px";
        overlayhint.innerHTML      = text;
        overlayhint.show();
        
        // get the zone data
        var dmzone = eval("[" + domevent.select(".cells")[0].innerHTML + "]");

        var tab = [];
        // fills the zones with 1 (transparent)
        dmzone.each(function (coords) {
            var x1  = coords[0];
            var y1  = coords[1];
            
            if (typeof(coords[0][0]) !== "undefined" && typeof(coords[0][1]) !== "undefined") {
                x1      = coords[0][0];
                y1      = coords[0][1];
            }

            // by default : coord2 = coord1 (draw a single cell)
            var x2  = x1;
            var y2  = y1;
            var x, y, n;
            if (2 === coords.length && typeof(coords[1][0]) !== "undefined" &&  typeof(coords[1][1]) !== "undefined") {
                // 2 coordinates : draw a box of cells
                x2  = coords[1][0];
                y2  = coords[1][1];
            }
            for (y = y1; y <= y2; y++) {
                for (x = x1; x <= x2; x++) {
                    n  = y * window.mapWidth + x;
                    tab.push(n);
                }
            }
        });

        if (tab.length) {
            DmmapOverlay.updateCells(tab, "", "high");
        }
    };



    /**
     * handler: mouse is on the overlay hint : hide it so the currently hint mouseout can bubble
     * if click: unlock and hide the hint
     * private static method
     */
    var _mouseoverClickOverlay = function (that, event, domevent) {
        if ("mouseover" === event.type) {
            if (null !== DmmapOverlay.getLockElement() ) {
                return;
            }
            domevent.hide();
        } else if ("click" === event.type) {
            DmmapOverlay.unlock(true);
            that.hide();
        }
    };



   /**
    * handler
    * private static method
    */
    var _mouseoutHint = function (that, event, domevent) {
        if (null !== DmmapOverlay.getLockElement() ) {
            return;
        }
        that.hide();
    };



    /**
     * handler to handle the locking
     * @todo: factorize with DmmapZone_click into DmmapOverlay
     * private static method
     */
    var _clickHint = function (that, event, domevent) {
        if (null !== DmmapOverlay.getLockElement()) {
            // lock set
            if (domevent === DmmapOverlay.getLockElement()) {
                // same element : unlock but don't hide
                DmmapOverlay.unlock(false);
            } else {
                // click on a different element
                DmmapOverlay.unlock(true);
                _mouseoverHint(that, event, domevent);
                DmmapOverlay.lock("hint", domevent);
            }
        } else {
            // lock not set
            DmmapOverlay.lock("hint", domevent);
        }
    };

        
        
   /**
    * the constructor
    * (returned, hosts the priveleged methods)
    */
    var __construct = function () {
    };



   /**
    * install handlers
    * privileged static method
    */
    __construct.init = function () {
        var div;
        // delegate div#levelhints and div#overlayhint
        div = $("levelhints");
        div.on("mouseover", "div.dmhint", _mouseoverHint.curry(this));
        div.on("mouseout",  "div.dmhint", _mouseoutHint.curry(this));
        div.on("click",     "div.dmhint", _clickHint.curry(this));

        div = $("overlayhint");
        div.on("mouseover", _mouseoverClickOverlay.curry(this));
        div.on("click",     _mouseoverClickOverlay.curry(this));
    };



    /**
     * hide the zone. Called by _mouseout, _click and by DmmapOverlay
     * privileged static method
     */
     __construct.hide = function () {
         var node = $("overlayhint");
         node.hide();
         DmmapOverlay.hide();
     };



     return __construct;
})(); // DmmapHint






var DmmapOverlay = (function () {
    "use strict";
    // private static variables
    var _coords    = null;      // coordinate of the first <td> cell
    var _isVisible = null;      // if an overlay is shown
    var _lockElement = null;    // the element that the overlay is locked to
    var _lockType    = null;    // "hint|zone|tip"
    
   /**
    * render a new tbody with cells
    * @returns domnode <tbody>
    * private static method
    */
    var _createCells = function (mapWidth, mapHeight) {
        var newTable  = document.createElement("tbody");
        var emptyRow  = document.createElement("tr");
        var emptyCell = document.createElement("td");

        // each rows begin with two blank cells
        emptyRow.appendChild(document.createElement("th"));
        emptyRow.appendChild(document.createElement("th"));

        // first 2 rows
        newTable.appendChild(emptyRow.cloneNode(true));
        newTable.appendChild(emptyRow.cloneNode(true));

        var x, y;
        var newCell;
        var newRow;
        var n = 0;
        for (y = 0; y < mapHeight; y++) {
            newRow = emptyRow.cloneNode(true);
            for (x = 0; x < mapWidth; x++) {
                newCell = emptyCell.cloneNode(true);
                newCell.setAttribute("id", "over-" + n);
                newRow.appendChild(newCell);
                n++;
            }
            newTable.appendChild(newRow);
        }

        return newTable;
    };



   /**
    * the constructor
    * (returned, hosts the priveleged methods)
    */
    var __construct = function () {
    };



   /**
    * initializer : position the overlaymap, install event handlers
    * privileged static method
    */
    __construct.init = function () {
        // get position of the overlay
        var tbody = $$("div.mainmap table.map tbody")[0];
        var left  = tbody.getClientRects()[0].left;
        var top   = tbody.getClientRects()[0].top;
        if ("undefined" !== typeof window.pageXOffset) {
            left += window.pageXOffset;
            top  += window.pageYOffset;
        }
        
        _coords = [ left + 32, top + 32 ];

        // position the overlay
        var overlaymap = $("overlaymap");
        overlaymap.style.top  = top + "px";
        overlaymap.style.left = left + "px";

        // insert new cells
        var domTable  = $$("#overlaymap table.map tbody")[0];
        var newTable  = _createCells(window.mapWidth, window.mapHeight);
        domTable.replace(newTable);
    };



   /**
    * highlight the individual cells given into the array
    * @param array aRes [ "z,x,y", "x,y" ]
    * privileged static method
    */
    __construct.highlightCells = function (aRes) {
        var tab = [];
        aRes.each(function (a) {
            tab.push(a[1] * window.mapWidth + a[0]);
        });

        this.updateCells(tab, "", "high");
    };



   /**
    * set all the cells class to defaultClass, except the ones in tab which are set to setClass
    * @var array tab index of the cells
    * @var string defaultClass
    * @var string setClass 
    * privileged static method
    */
    __construct.updateCells = function (tab, defaultClass, setClass) {
        // reset all cells class
        var allcells = $$("#overlaymap td");
        allcells.each(function (cell) {
            cell.className = defaultClass;
        });

        var n, val, td;
        var len = tab.length;
        for (n = 0; n < len; n++) {
            val = tab[n];
            td = $("over-" + val);
            td.className = setClass;
        }

        _isVisible = true;
    };



   /**
    * returns the coords of the first cell
    * privileged static method
    * @returns array [x,y]
    */
    __construct.getCoords = function () {
        return _coords;
    };



   /**
    * hide the overlay. Set all cells to transparent
    * privileged static method
    */
    __construct.hide = function () {
        if (false !== _isVisible) {
            DmmapOverlay.updateCells([], "", "");
            _isVisible = false;
        }
    };



    /**
     * privileged static method
     */
     __construct.lock = function (type, elem) {
         _lockElement = elem;
         _lockType    = type;
     };



    /**
     * privileged static method
     */
    __construct.unlock = function (fHide) {
        if (true === fHide) {
            if ("zone" === _lockType) {
                DmmapZone.hide();
            } else if ("hint" === _lockType) {
                DmmapHint.hide();
            } else if ("tip" === _lockType) {
                DmmapTips.hideTip();
            }
        }
        _lockElement = null;
    };



    /**
     * privileged static method
     */
    __construct.getLockElement = function () {
        return _lockElement;
    };



    return __construct;
})(); // DmmapOverlay







var DmmapMap = (function () {
    "use strict";
    // private static variables
    var _mode = null;       // "view|edit"




   /**
    * render a new cell
    * @returns domnode <td>
    * private static method
    */
    var _renderNewCell = function (celltype) {
        var cell = document.createElement("td");
        cell.className = "tile-" + celltype;
        return cell;
    };



   /**
    * render the map
    * @returns domnode <tbody>
    * private static method
    */
    var _render = function () {
        var x, y;
        var newTable  = document.createElement("tbody");
        var td, tr;
        var firstrow, secondrow;

        //first row
        tr = document.createElement("tr");
        tr.appendChild(_renderNewCell(0));
        tr.appendChild(_renderNewCell(0));
        for (x = 0; x < window.mapWidth; x++) {
            td = _renderNewCell(0);
            if (x % 10 === 0) {
                td.style.fontSize = "x-small";
                td.innerHTML = x;
            }
            tr.appendChild(td);
        }
        tr.appendChild(_renderNewCell(0));
        tr.appendChild(_renderNewCell(0));
        newTable.appendChild(tr);
        firstrow = tr;

        //second row
        tr = document.createElement("tr");
        tr.appendChild(_renderNewCell(0));
        tr.appendChild(_renderNewCell(0));
        for (x = 0; x < window.mapWidth; x++) {
            td = _renderNewCell(0);
            if (x % 10) { td.innerHTML = x % 10; }
            tr.appendChild(td);
        }
        tr.appendChild(_renderNewCell(0));
        tr.appendChild(_renderNewCell(0));
        newTable.appendChild(tr);
        secondrow = tr;


        var tileId = 0;
        var firstcell, secondcell;
        for (y = 0; y < window.mapHeight; y++) {
            var celltype;

            tr = document.createElement("tr");

            //first cell
            td = _renderNewCell(0);
            if (y % 10 === 0) {
                td.style.fontSize = "x-small";
                td.innerHTML = y;
            }
            tr.appendChild(td);
            firstcell = td;

            //second cell
            td = _renderNewCell(0);
            if (y % 10) { td.innerHTML = y % 10; }
            tr.appendChild(td);
            secondcell = td;

            for (x = 0; x < window.mapWidth; x++) {
                celltype = window.tileIds[tileId];
                td = _renderNewCell(celltype);
                td.setAttribute("id", "cell-" + tileId);
                tr.appendChild(td);
                tileId++;
            }

            tr.appendChild(firstcell.cloneNode(true));
            tr.appendChild(secondcell.cloneNode(true));

            newTable.appendChild(tr);
        }

        //last rows : same as first ones
        newTable.appendChild(firstrow.cloneNode(true));
        newTable.appendChild(secondrow.cloneNode(true));

        return newTable;
    };


   /**
    * the constructor
    * (returned, hosts the privileged methods)
    */
    var __construct = function () {
    };



   /**
    * @var mode string "view|edit"
    * privileged static method
    */
    __construct.init = function (mode) {
        _mode       = mode;
        this.render();
    };



   /**
    * render the map
    * privileged static method
    */
    __construct.render = function (mode) {
        var mainmap = $$(".mainmap table.map tbody")[0];
        var newmap  = _render();
        mainmap.replace(newmap);
    };



    return __construct;
})(); // DmmapMap







var DmmapTips = (function () {
    "use strict";
    // private static variables
    var _currentId = null;



   /**
    * parse the tip for coordinates (z,x,y or x,y)
    * @param string text
    * @returns array ["0,18,11", "0,19,7"]
    * private static method
    */
    var _parseText = function (text) {
        var rxp = new RegExp("(\\d{1,2}\\s*,)?(\\d{1,2}\\s*,\\s*\\d{1,2})", "g");
        var match;
        var res = [];

        while ( (match = rxp.exec(text)) ) {
            res.push(match[0]);
        }

        return res;
    };



   /**
    * filter an coords to get only the coords contained in the current level
    * @param array  ["z,x,y", "x,y"]
    * @returns array [ [18,11], [19,7] ]
    * private static method
    */
    var _filterParsed = function (aIn) {
        var aOut = [];

        aIn.each(function (sCoords) {
            var z, x, y;
            var aCoord = sCoords.split(",");
            if (3 === aCoord.length) {
                z = parseInt(aCoord[0], 10); x = aCoord[1]; y = aCoord[2];
            } else {
                z = window.level; x = aCoord[0]; y = aCoord[1];
            }
            x = parseInt(x, 10); y = parseInt(y, 10);
            if (z !== window.level || x >= window.mapWidth || y >= window.mapHeight) {
                return;
            }
            aOut.push( [x, y] );
        });

        return aOut;
    };



   /**
    * install a tip (setting the right background for the cell)
    * @returns nothing
    * private static method
    */
    var _updateCells = function (div) {
        var id = div.getAttribute("id");            // id is "hover-box-nnn"
        if (id.substr(0, 10) !== "hover-box-") {
            return;
        }

        var tileId = parseInt(id.substr(10), 10);
        var celltype = window.tileIds[tileId];
        id = "cell-" + tileId;                      // id is "cell-nnn" (the id of the td cell)

        var cell = $(id);
        cell.className = "tile-" + (celltype + 100);
    };



   /**
    * the constructor
    * (returned, hosts the privileged methods)
    */
    var __construct = function () {
    };



   /**
    * privileged static method
    */
    __construct.init = function () {
        this.render();
    };



   /**
    * privileged static method
    */
    __construct.render = function () {
        var hoverboxes = $$("#hover-boxes div");
        hoverboxes.each(_updateCells);
    };



   /**
    * event called by a click
    * privileged static method
    */
    __construct.click = function (tileId, event) {
        var celltype = window.tileIds[tileId];

        if ( (3 === celltype) || (5 === celltype) ) {
            window.levelDown();
        } else if (4 === celltype) {
            window.levelUp();
        }
    };



   /**
    * event
    * privileged static method
    * @return boolean isTip
    */
    __construct.showTip = function (tileId, event, domevent) {
        var hoverbox = $("hover-box-" + tileId);
        if (null === hoverbox) { return false; }

        // get coords of the first cell of the overlay
        var overCoord = DmmapOverlay.getCoords();
        var xpos = overCoord[0];      // where to display the tip
        var ypos = overCoord[1];

        // compute the cell x,y
        var xtile = tileId % window.mapWidth;
        var ytile = Math.floor(tileId / window.mapWidth);

        var text = hoverbox.innerHTML;

        var aRes = _parseText(text);
        aRes     = _filterParsed(aRes);

        DmmapOverlay.highlightCells(aRes);

        if (aRes.length) {
            // compute a good xtile, ytile to display the tip
            var ymax = ytile;
            aRes.each(function (a) {
                ymax = Math.max( ymax, a[1] );
            });

            var xmax = 0;
            if (ytile === ymax) {
                xmax = xtile;
            }
            aRes.each(function (a) {
                if (a[1] === ymax) {
                    xmax = Math.max( xmax, a[0] );
                }
            });

            xtile = xmax;
            ytile = ymax;
        }

        hoverbox.style.top     = (ypos + (ytile * 16) + 16) + "px";
        hoverbox.style.left    = (xpos + (xtile * 16) + 16) + "px";
        hoverbox.style.display = "block";
        _currentId = tileId;
        
        return true;
    };



   /**
    * event
    * privileged static method
    */
    __construct.hideTip = function () {
        if (null === _currentId) { return; }
        var hoverbox = $("hover-box-" + _currentId);
        hoverbox.style.display = "none";
        DmmapOverlay.hide();
    };



    return __construct;
})(); // DmmapTips






var DmmapEditor = (function () {
    // private static variable
    var _currentTool = null;    // cell to draw
    var _lastClickId = null;    // id of the last cell
    var _tileBackup  = null;    // value of the cell before modification
    var _clickNumber = null;    // number of time the user has clicked the cell
    var _toolsDesc   = ["empty cell", "wall cell", "open cell", "staircase down", "staircase up", "pit", "buttonless vertical door", "buttonless horizontal door", "openable vertical door", "openable horizontal door", "imaginary wall", "removable wall", "visble/invisble teleporter" ];

   /**
    * private static method
    */
    var _setCell = function (cellId, tileId ) {
        var cell = $("cell-" + cellId);
        cell.className = "tile-" + tileId;

        tileIds[cellId] = tileId;
    };



   /**
    * private static method
    */
    var _initPalette = function () {
        var palette = $$("#palette tbody")[0];
        while (palette.hasChildNodes()) { palette.removeChild(palette.lastChild); }

        var tr = document.createElement("tr");
        var celltype;
        for (celltype = 0; celltype <= window.tileCount; celltype++) {
            var td = document.createElement("td");
            td.className = "tile-" + celltype;
            td.setAttribute("id", "palette-" + celltype);
            tr.appendChild(td);
        }
        palette.appendChild(tr);

        palette.on("click", "td", _clickPalette.curry(this));
    };



   /**
    * click handler
    * private static method
    */
    var _clickPalette = function (that, event, domevent) {
        // domevent: td
        var id = domevent.getAttribute("id");            // id is "palette-nnn" or null
        if (null === id) { return null; }
        if (id.substr(0, 8) !== "palette-") { return null; }

        var tileId   = parseInt(id.substr(8), 10);
        __construct.setTool(tileId);

        return tileId;
    };



   /**
    * the constructor
    * (returned, hosts the privileged methods)
    */
    var __construct = function () {
    };



   /**
    * event called by a click
    * @param int drawMode 0:click 1:mouseover
    * privileged static method
    */
    __construct.clickMap = function (tileId, event, drawMode) {
        if (null === _currentTool) {
            return;
        }
        var currentTile = window.tileIds[tileId];

        if (tileId !== _lastClickId) {
            _lastClickId = tileId;
            _tileBackup  = currentTile;
            _clickNumber = 0;
        }

        // 0 : currentTool
        // 1 : "1"
        // 2 : "2"
        // 3 : backup

        var drawId;

        if (drawMode === 0) {
            // click
            do {
                var cycle = _clickNumber % 4;
                if (0 === cycle) {
                    drawId = _currentTool;
                } else if (1 === cycle) {
                    drawId = 1;
                } else if (2 === cycle) {
                    drawId = 2;
                } else {
                    drawId = _tileBackup;
                }
                _clickNumber++;
            } while (drawId === currentTile);
        } else {
            // mouseover
            drawId = _currentTool;
        }

        _setCell(tileId, drawId);
    };



   /**
    * @var mode string "view|edit"
    * privileged static method
    */
    __construct.init = function (mode) {
        _initPalette();
    };



   /**
    * @var tool int
    * privileged static method
    */
    __construct.setTool = function (tool) {
        var node;

        if (null !== _currentTool) {
            // reset the palette cell
            node = $("palette-" + _currentTool);
            node.className = "tile-" + _currentTool;
        }

        _currentTool = tool;

        // set the palette cell
        node = $("palette-" + _currentTool);
        node.className = "tile-" + (_currentTool + 100);

        node = $$("div.tooldesc span")[0];
        node.innerHTML = this.getToolDesc(tool);
    };



   /**
    * @var tool int 0-12 or 100-112
    * @returns string tool description
    * privileged static method
    */
    __construct.getToolDesc = function (tool) {
        return _toolsDesc[tool % 100];
    };
    return __construct;
})(); // DmmapEditor





/**
 * static class managing versions. Should be called *before* DmmapMap !
 */
var DmmapVersions = (function () {
    "use strict";
    // private static variables
    var _currentNum = null;



   /**
    * switch to the selected version. You should call a map redraw after
    * private static method
    * @var num index of the version in aVersions (not the mapid)
    */
    var _selectVersion = function (num ) {
        var aVersions  = window.aVersions;
        window.tileIds = eval(aVersions[num].cells);
        window.mapId   = aVersions[num].id;
    };

   /**
    * Update the url in the "switch to edit mode" link
    * private static method
    * @var num index of the version in aVersions (not the mapid)
    */
    var _updateEditLinkHref = function (num ) {
        var anchor     = $("switchtoeditmode");
        var url        = window.urlBase + "edit/";
        if (0 === num ) {
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
    var _change = function (that, event, domevent) {
        var num = domevent.value;
        if (num !== _currentNum) {
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
    var __construct = function () {
    };



   /**
    * privileged static method
    */
    __construct.init = function () {
        _updateEditLinkHref(0);

        if (window.aVersions.length > 0) {
            _selectVersion(0);

            var node = $("versions");
            while (node.hasChildNodes()) { node.removeChild(node.lastChild); }
            node.insert("Versions: ");

            var select = document.createElement("select");
            var num    = 0;
            window.aVersions.each(function (ver) {
                var option = document.createElement("option");
                var text   = ver.user_name + ": " + ver.comment + " (" + ver.datemodif + ")";
                option.setAttribute("value", num);
                option.innerHTML = text;
                select.appendChild(option);
                num++;
            });
            node.appendChild(select);

            // install event handler
            Event.on(select, "change", _change.curry(this));
            Event.on(select, "keyup", _change.curry(this));
//            select.on("change", _change.curry(this)); // does not work in ie6
        }
    };



    return __construct;
})(); // DmmapVersions






/**
 * static class
*/
var DmmapSkin = (function () {
    "use strict";
   /**
    * the constructor
    * (returned, hosts the privileged methods)
    */
    var __construct = function () {
    };



    __construct.selectChange = function (that, event, domevent) {
        var i, n;
        var ssheets = document.styleSheets;         // all styleSheets. Find the right one
        var ssheet;

        // find the last one whose href contain "dmmaps"
        n = ssheets.length;
        for (i = n - 1; i >= 0 ;i--) {
            var thisheet = ssheets[i];
            if ( (null !== thisheet.href) && (thisheet.href.indexOf("dmmaps") !== -1) ) {
                ssheet = thisheet;
                break;
            }
        }
        if ( (null === ssheet) || ("undefined" === typeof(ssheet.cssRules))) {
            return;
        }

        var rule;
        n = ssheet.cssRules.length;
        for (i = 0; i < n; i++) {
            var r = ssheet.cssRules.item(i);
            if (typeof(r.selectorText) === "undefined") { continue; }
            if (r.selectorText.indexOf("THETILESET") !== -1) {
                rule = r;
                break;
            }
            
        }
        
        if (null === rule) {
            // not found
            return;
        }

        var newImage = domevent.value;
        rule.style.backgroundImage = "url(" + newImage + ")";
    };



    return __construct;
})(); // DmmapSkin






var DmmapHandlers = (function () {
    "use strict";
    // private static variable
    var _mode     = null;
    var _drawMode = 0;
    var _cursor   = null;       // since document.body.style.cursor changing in painfully slow as hell under IE6, make minimal changes



   /**
    * Get the cell id from the domevent
    * @returns int or null
    * private static method
    */
    var _getCellId = function (domevent) {
        var id = domevent.getAttribute("id");            // id is "cell-nnn" or null
        if (null === id) { return null; }
        if (id.substr(0, 5) !== "over-") { return null; }

        var tileId   = parseInt(id.substr(5), 10);
        return tileId;
    };



   /**
    * event
    * private static method
    */
    var _mouseover = function (that, event, domevent) {
        var tileId   = _getCellId(domevent);
        if (null === tileId) { return; }

        var celltype = window.tileIds[tileId];
        if ("view" === _mode) {
            if (null === DmmapOverlay.getLockElement() ) {
                if ( (3 === celltype) || (4 === celltype) || (5 === celltype) ) {
                    if (_cursor !== "pointer") {
                        document.body.style.cursor = "pointer";
                        _cursor = "pointer";
                    }
                }
                DmmapTips.showTip(tileId, event, domevent);
            }
        } else if ("edit" === _mode) {
            if (_drawMode) {
                if (KeyWatcher.shift) {
                    DmmapEditor.clickMap(tileId, event, 1);
                } else {
                    // end draw
                    _drawMode = 0;
                }
            }
        }

        var cellinfo = $("cellinfo");
        var y = Math.floor(tileId / 32);
        var x = tileId % 32;
        cellinfo.innerHTML = "Current cell: " + x + ", " + y;
    };



   /**
    * event
    * private static method
    */
    var _mouseout = function (that, event, domevent) {
        if ("view" === _mode) {
            if (null !== DmmapOverlay.getLockElement() ) {
                return;
            }
            if (_cursor !== null) {
                document.body.style.cursor = null;
                _cursor = null;
            }
            DmmapTips.hideTip();
            DmmapOverlay.hide();
        }
        var cellinfo = $("cellinfo");
        cellinfo.innerHTML = "";
    };



   /**
    * event. Can end lock mode, but can't start it
    * private static method
    */
    var _click = function (that, event, domevent) {
        var tileId   = _getCellId(domevent);
        if (null === tileId) { return; }

        var celltype = window.tileIds[tileId];
        if ("view" === _mode) {
            if (null !== DmmapOverlay.getLockElement()) {
                // lock set: unlock an optionnaly relock.
                // do not proceed to normal click action (to prevent location change)
                if (domevent === DmmapOverlay.getLockElement()) {
                    // same element : unlock but don't hide
                    DmmapOverlay.unlock(false);
                } else {
                    // click on a different element: unlock, and hide, show the tip
                    DmmapOverlay.unlock(true);
                    var isTip = DmmapTips.showTip(tileId, event, domevent);
                    if (isTip) {
                        DmmapOverlay.lock("tip", domevent);
                    }
                }
                return;
            }
            // lock not set
            DmmapTips.click(tileId, event);
            var isTip = DmmapTips.showTip(tileId, event, domevent);
            if (isTip) {
                DmmapOverlay.lock("tip", domevent);
            }
        } else if ("edit" === _mode) {
            if (KeyWatcher.shift) {
                _drawMode = 1;
            }
            DmmapEditor.clickMap(tileId, event, _drawMode);
        }
    };



   /**
    * the constructor
    * (returned, hosts the privileged methods)
    */
    var __construct = function () {
    };



   /**
    * @var mode string "view|edit"
    * privileged static method
    */
    __construct.init = function (mode) {
        _mode = mode;

        var mainmap = $$("#overlaymap table.map")[0];
        mainmap.on("mouseover", "td", _mouseover.curry(this));
        mainmap.on("mouseout" , "td", _mouseout.curry(this));
        mainmap.on("click",     "td", _click.curry(this));

        var node = $("tilesetselect");
        Event.on(node, "change", DmmapSkin.selectChange.curry(this));

        KeyWatcher.install();
    };



    return __construct;
})(); // DmmapHandlers






var KeyWatcher = (function () {
    "use strict";
    // private static variable
    var _handlerKeyUp   = null;
    var _handlerKeyDown = null;



   /**
    * the constructor
    * (returned, hosts the privileged methods)
    */
    var __construct = function () {
    };



   /**
    * @var mode string "view|edit"
    * privileged static method
    */
    __construct.install = function () {
        if (null !== _handlerKeyUp) { return ; } // already installed

        _handlerKeyDown = Event.on(document, "keydown", function (event) {
            if ( !this.shift   && (event.which || event.keyCode) === 16) { this.shift   = 1; }
            if ( !this.control && (event.which || event.keyCode) === 17) { this.control = 1; }
            if ( !this.alt     && (event.which || event.keyCode) === 18) { this.alt     = 1; }
        }.bind(this));
        _handlerKeyUp = Event.on(document, "keyup", function (event) {
            if (  this.shift   && (event.which || event.keyCode) === 16) { this.shift   = 0; }
            if (  this.control && (event.which || event.keyCode) === 17) { this.control = 0; }
            if (  this.alt     && (event.which || event.keyCode) === 18) { this.alt     = 0; }
        }.bind(this));
    };



   /**
    * @var mode string "view|edit"
    * privileged static method
    */
    __construct.remove = function () {
        if (null === _handlerKeyUp) { return ; } // not installed

        _handlerKeyUp.stop();
        _handlerKeyDown.stop();
        _handlerKeyUp   = null;
        _handlerKeyDown = null;
    };



    /* public variables */
    __construct.shift   = 0;
    __construct.control = 0;
    __construct.alt     = 0;



    return __construct;
})(); // KeyWatcher






var Dmmap = (function () {
    "use strict";



   /**
    * the constructor
    * (returned, hosts the privileged methods)
    */
    var __construct = function () {
    };



   /**
    * @var mode string "view|edit"
    * privileged static method
    */
    __construct.bootstrap = function () {
        var mode = "view";
        if ( -1 !== window.location.href.indexOf("/edit/") ) {
            mode = "edit";
        }

        if ("view" === mode) {
            DmmapVersions.init();
            DmmapMap.init("view");
            DmmapOverlay.init();
            DmmapZone.init();
            DmmapHint.init();
            DmmapTips.init();
            DmmapHandlers.init("view");
        } else if ("edit" === mode) {
            DmmapMap.init("edit");
            DmmapOverlay.init();
            DmmapEditor.init();
            DmmapHandlers.init("edit");
        }
    };



    return __construct;
})(); // Dmmap






