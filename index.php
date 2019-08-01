<?php
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=canvas', 'root', '');
    $hacer = $pdo->query("SELECT * FROM coordenadas");
    $lineas = [];
    foreach ($hacer as $h){
        $lineas[] = $h;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HTML5 Canvas</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<canvas id="mesa-dibujo" width="800" height="400"></canvas>
<form name="grosor">
    <input type="range" min="1" max="80"  name="valorgrosor" onchange="dibujaTrazado(value)">
</form>
<div id="colores">
    <div class="color negro" onclick="cambiarColor('#000')"></div>
    <div class="color azul" onclick="cambiarColor('#00F')"></div>
    <div class="color rojo" onclick="cambiarColor('#F00')"></div>
    <div class="color verde" onclick="cambiarColor('#0F0')"></div>
    <div class="color naranja" onclick="cambiarColor('#ff7900')"></div>
</div>

<script>
    const ws = new WebSocket("ws://127.0.0.1:4000");
    let canvas = document.getElementById("mesa-dibujo");
    canvas.width = window.innerWidth;
    let contexto = canvas.getContext("2d");
    let presionado = false;
    let coordenadasAgrupadas = [];
    let fi = <?= json_encode($lineas) ?>

    ws.onopen = function () {
        console.log("Conexión abierta");
        for (let i = 0; i < fi.length; i++) {
            contexto.beginPath();
            contexto.strokeStyle = fi[i]['color'];
            contexto.lineWidth = fi[i]['grosor'];
            contexto.moveTo(fi[i]['coord_x']-1,fi[i]['coord_y']-1);
            contexto.lineTo(fi[i]['coord_x']-8,fi[i]['coord_y']-8);
            contexto.stroke();
        }
    };

    ws.onmessage = function (data) {
        let info = JSON.parse(data.data);
        contexto.beginPath();
        for (let i = 0; i < info.length; i++) {
            contexto.strokeStyle = info[i].color;
            contexto.lineTo(info[i].x, info[i].y);
            contexto.stroke();
        }
    };

    canvas.addEventListener('mousedown', function (e) {
        presionado = true;
        contexto.beginPath();
        console.log(e.data);

    });
    canvas.addEventListener('mouseup', function (e) {
        presionado = false;
        ws.send(JSON.stringify(coordenadasAgrupadas));
        coordenadasAgrupadas = [];
    });
    canvas.addEventListener('mousemove', function (e) {
        if (presionado) {
            contexto.lineTo(e.x, e.y);
            contexto.stroke();
            let datos = {
                x: e.x,
                y: e.y,
                color: contexto.strokeStyle,
                grosor: contexto.lineWidth
            };
            coordenadasAgrupadas.push(datos);
        }
    });

    function cambiarColor(color) {
        contexto.strokeStyle = color;
    }


    function dibujaTrazado(value){
        contexto.lineWidth = value;
    }
</script>
</body>
</html>
