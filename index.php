<?php
 /*
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 3 of the License, or
  * (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

  require_once __DIR__ . '/spider.php';
  if($_POST['domain']){
    $siteStructure = crawlSite($_POST['domain']);
    if(empty($siteStructure)){
      print "<h1>No results</h1>";
    }else{
      print "<h1>Site structure for " . $_POST['domain'];
    }
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Visual Site Explorer</title>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
    <script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
    <script src="http://cytoscape.github.io/cytoscape.js/api/cytoscape.js-latest/cytoscape.min.js"></script>

    <style>
      body {
        font-family: helvetica;
        font-size: 14px;
      }

      #cy {
        width: 100%;
        height: 100%;
        position: absolute;
        left: 0;
        top: 100;
        z-index: 999;
      }
    </style>

    <script>
      var siteStructure = <?php echo json_encode($siteStructure) ?>;
      
      $(function(){
        var cy = window.cy = cytoscape({
          container: document.getElementById('cy'),
          boxSelectionEnabled: false,
          autounselectify: true,

          layout: {
            name: 'circle'
          },

          style: [
            {
              selector: 'node',
              style: {
                'height': 13,
                'width': 13,
                'background-color': '#5C1664'
              }
            },

            {
              selector: 'edge',
              style: {
                'curve-style': 'haystack',
                'haystack-radius': 0,
                'width': 3,
                'opacity': 0.5,
                'line-color': '#15AAEE'
              }
            }
          ],
      });

      var items = 0;
      //Cant call length as it's an object
      for (const outprop in siteStructure) {
        items ++;
      }
      //Centre of graph
      var x0 = window.screen.availWidth/2;
      var y0 = window.screen.availWidth/4;

      var i = 0; //Count;
      var r = 250; //Radius of graph
      //Add our verticies
      for (const outprop in siteStructure) {
          var x = x0 + r * Math.cos(2 * Math.PI * ++i / items);
          var y = y0 + r * Math.sin(2 * Math.PI * ++i / items); 
          console.log(x);
          console.log(y);
          console.log(items);
          cy.add(
            {"data":
              {"id":outprop,
              "altered":0,
              "rank":148,
              "cited":53,
              "uniprotdesc":outprop,
              "isseed":false,
              "isvalid":true,
              "importance":3
              },
              "position":
                {"x":x,
                "y":y
                },
              "group":"nodes",
              "removed":false,
              "selected":false,
              "selectable":true,
              "locked":false,
              "grabbable":true,
              "classes":""
               });
      }
      // And now the edges      
      for (const outprop in siteStructure) {
        for (const inprop in siteStructure[outprop]) {
          cy.add(
            {"data":
              {
              "source":outprop,
              "cited":0,
              "target":inprop,
              "isdirected":true,
              "type":"controls-expression-of",
              },
              "position":{},
              "group":"edges",
              "removed":false,
              "selected":false,
              "selectable":true,
              "locked":false,
              "grabbable":true,
              "classes":""
            });
          }
        }
      });
    </script>
  </head>
  <body>
    <div id = "input-field">
      <form action="index.php" method="post">
        <input type="text" name="domain" value="Enter domain" ><br>
        <input type="submit" value="Submit">
      </form>
    </div>
    <div id="cy"></div>
  </body>
</html>

