var DmmapOverview = (function() {

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
    }


   /**
    * Event handler: remove the overlay
    * private static method
    */
    var _dmzoneOff = function(that, event, domevent) {
        domTable      = $("overlaymap");
        domTable.hide();
//        domTable.fade({duration:.4});
    }



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
    }



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
    }



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

