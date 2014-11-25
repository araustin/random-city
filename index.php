<!DOCTYPE html>
<html>
<head>
<title>City</title>
</head>
<body style="overflow: hidden; margin: 0; padding: 0;">
<div id="controlbar">
    <button id="redraw" style="position: relative; z-index: 100;">Redraw</button>
    <button id="makeItRain" style="position: relative; z-index: 100;">Rain</button>
</div>
<?php 
$depth = 8;
$width = 800;
$height = 600;
for ($i = 0; $i < $depth; $i++) {
    echo '<canvas id="draw' . $i . '" style="position: absolute; bottom: 0; left: 0; z-index: ' . $i . '"></canvas>';
}
?>
<script type="text/javascript">
(function() {
    var depth = <?php echo $depth; ?>;
    var maxBuildings = 100;
    var buildingsPer = maxBuildings / depth;
    var context = [];
    var width = window.innerWidth;
    var height = <?php echo $height; ?>;
    var canvas = [];
    for (var i = 0; i < depth; i++) {
        canvas[i] = document.getElementById('draw' + i);
        canvas[i].style.width = width + 'px';
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
            buildingWidth = randRange(2, 14);
            buildingHeight = randRange(9, 100);
            buildingLeft = randRange(1, 100 - buildingWidth);
            buildingTop = 100 - buildingHeight;
            fill = 'rgb(' + randRange(65,95) + ',' + randRange(55,95) + ',' + randRange(95,145) + ')';
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
        var left, top, sizeX, sizeY, fillR, fillG, fillB;
        fillG = randRange(40, 55);
        fillR = randRange(40, 55);
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
    document.body.addEventListener("mousemove", function(event) {
        var half = width / 2;
        var magnitude = 4;
        var moveBy = ((half - event.x) /  half) * magnitude;
        for (var i = 0; i < depth; i++) {
            canvas[i].style['transform'] = 'translate(' + (moveBy * (i + 1)) + 'px)';
        }
    });

    document.getElementById('redraw').onclick = function() {
        drawCity();
    }
})()
</script>
<canvas id="rain" style="z-index: 99; position: relative;"></canvas>
<script>
(function () {
    var canvas = document.getElementById('rain');
    var ctx = canvas.getContext('2d');
    var drops = [];
    var start = null;
    var steps = 1000;
    var globalDelay = 200;
    var numberOfDrops = 999;
    var xOffset = 20;
    var y = 0;
    var strokeColor;
    var alpha;

    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight + 50;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    var genDrop = function() {
        var randStartX = Math.floor(Math.random() * canvas.width);
        var randOffset = Math.floor(Math.random() * xOffset * (Math.random() * 2 - 1));
        var drop = {
            x: randStartX,
            offset: randOffset,
            delay:  Math.floor(Math.random() * globalDelay),
            start: 0,
        }
        return drop;
    }
    // create the drops
    for (var i = 0; i < numberOfDrops; i++) {
        drops.push(genDrop());
    }
    var makeItRain = function(timestamp) {
        ctx.clearRect(0,0,canvas.width, canvas.height);
        var progress = 0;
        var doneDrops = [];
        for (var d = drops.length - 1; d >= 0; d--) {
            if (drops[d].delay-- >= 0) {
                continue;
            }
            if (!drops[d].start) {
                drops[d].start = timestamp;
            }

            progress = timestamp - drops[d].start
            if (progress >= steps) {
                progress = steps;
            }

            y = (canvas.height / steps) * progress;
//            ctx.clearRect(drops[d].x, 0, drops[d].x + (drops[d].offset * (progress / steps)), y);
            ctx.beginPath();
            ctx.moveTo(drops[d].x, 0);
            ctx.lineTo(drops[d].x + (drops[d].offset * (progress / steps)), y);

            if (progress == steps) {
                var totalProgress = timestamp - drops[d].start;
                alpha = 1 - (totalProgress - steps) / steps;
            } else {
                strokeColor = 'rgb(0,0,0)';
                alpha = 1;
            }

            if (alpha < 0) {
                doneDrops.push(d);
                continue;
            }
            ctx.globalAlpha = alpha;

            ctx.strokeStyle = strokeColor;
            ctx.stroke();

        }

        for (var i in doneDrops) {
            var d = doneDrops[i];
            drops.splice(d, 1, genDrop());
        }

        if (drops.length && raining) {
            window.requestAnimationFrame(makeItRain);
        }
    }


    window.addEventListener("resize", function(event) {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight + 50;
    });

    document.getElementById('makeItRain').onclick = function() {
        if (typeof raining == 'undefined' || !raining) {
            raining = window.requestAnimationFrame(makeItRain);
            canvas.style.display = 'block';
        } else {
            raining = false;
            canvas.style.display = 'none';
        }
    }

})()

</script>


</body>
</html>
