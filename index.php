<!DOCTYPE html>
<html>
<head>
<title>City</title>
</head>
<body>
<div id="controlbar"><button id="redraw">Redraw</button></div>
<?php 
$depth = 8;
$width = 800;
$height = 600;
for ($i = 0; $i < $depth; $i++) {
    echo '<canvas id="draw' . $i . '" style="width: ' .  $width . 'px; height: ' . $height . 'px; position: absolute; top: 50px; left: 0; z-index: ' . $i . '"></canvas>';
}
?>
<script type="text/javascript">
    var depth = <?php echo $depth; ?>;
    var maxBuildings = 100;
    var buildingsPer = maxBuildings / depth;
    var context = [];
    var width = <?php echo $width; ?>;
    var height = <?php echo $height; ?>;
    var canvas = [];
    for (var i = 0; i < depth; i++) {
        canvas[i] = document.getElementById('draw' + i);
        canvas[i].width = width;
        canvas[i].height = height;
        context[i] = canvas[i].getContext('2d');
        context[i].clearRect(0, 0, width, height);
    }
    var wcent = Math.floor(width/100);
    var hcent = Math.floor(height/100);

    function drawCity() {
        for (var i = 0; i < depth; i++) {
            canvas[i] = document.getElementById('draw' + i);
            canvas[i].width = width;
            canvas[i].height = height;
            context[i] = canvas[i].getContext('2d');
            context[i].clearRect(0, 0, width, height);
        }

        context[0].fillStyle = '#fff';
        context[0].fillRect(0, 0, X(100), Y(100));
        var buildingLeft, buildingTop, buildingWidth, buildingHeight, fill;
        for (var i = 0; i < 40; i++) {
            buildingWidth = randRange(2, 15);
            buildingHeight = randRange(2, 90);
            buildingLeft = randRange(5, 95 - buildingWidth);
            buildingTop = 95 - buildingHeight;
            fill = 'rgba(' + randRange(25,255) + ',' + randRange(25,255) + ',' + randRange(25,255) + ',255)';
            drawBuilding(buildingLeft, buildingTop, buildingWidth, buildingHeight, fill,  Math.floor(i / buildingsPer));
        }
    }

    function randRange(low, high) {
        return Math.floor(Math.random() * (high - low)) + low;
    }
    function drawBuilding(buildingLeft, buildingTop, buildingWidth, buildingHeight, fillColor, zIndex) {
        context[zIndex].shadowColor   = '#111';
        context[zIndex].shadowOffsetX = 0;
        context[zIndex].shadowOffsetY = 0;
        context[zIndex].shadowBlur    = 8;
        context[zIndex].fillStyle = fillColor;
        context[zIndex].fillRect(X(buildingLeft), Y(buildingTop), X(buildingWidth), Y(buildingHeight));
        drawWindows(buildingLeft, buildingTop, buildingWidth, buildingHeight, zIndex);
    }

    function drawWindows(buildingLeft, buildingTop, buildingWidth, buildingHeight, zIndex) {
        context[zIndex].shadowColor   = '#000';
        context[zIndex].shadowOffsetX = 0;
        context[zIndex].shadowOffsetY = 0;
        context[zIndex].shadowBlur    = 0;

        var space = 0.8;
        var numWindowsHorizontal = Math.floor(Math.random() * buildingWidth) + 1; 
        var numWindowsVertical = Math.floor(Math.random() * buildingHeight) + 1; 
        var windowSizeX = buildingWidth / numWindowsHorizontal - (space + space / numWindowsHorizontal);
        var windowSizeY = buildingHeight / numWindowsVertical - (space + space / numWindowsVertical);
        var left, top, sizeX, sizeY, fillR,fillG,fillB;
                fillG = randRange(240, 255);
                fillR = randRange(240, 255);
        for (var x = 0; x < numWindowsHorizontal; x++) {

            for (var y = 0; y < numWindowsVertical; y++) {
                
                fillB = randRange(14, 40 + x);
                left = X(space * x) + X(x * windowSizeX) + X(buildingLeft) + X(space);
                top = Y(space * y) + Y(y * windowSizeY) + Y(buildingTop) + Y(space);
                sizeX = X(windowSizeX);
                sizeY = Y(windowSizeY);
                fill = 'rgba(' + 
                    fillR + ',' + 
                    fillG + ',' + 
                    fillB + ',' + 
                    '255)';
                context[zIndex].fillStyle = fill;
                context[zIndex].fillRect(left, top, sizeX, sizeY);
            }
        }
    }

    function X(percent) {
        return percent * wcent;
    }
    function Y(percent) {
        return percent * hcent;
    }
    drawCity();
    document.getElementById('redraw').onclick = function() {
        drawCity();
    }
    document.body.addEventListener("mousemove", function(event) {
        var half = width / 2;
        var magnitude = 4;
        var moveBy = ((half - event.x) /  half) * magnitude;
        for (var i = 0; i < depth; i++) {
            canvas[i].style['transform'] = 'translate(' + (moveBy * (i + 1)) + 'px)';
        }
    });
</script>

</body>
</html>
