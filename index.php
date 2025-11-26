<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoodleHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: #0a0a0a;
            color: #e0e0e0;
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh;
        }
        .card {
            background: #111111;
            border: 1px solid #222222;
            box-shadow: 0 8px 32px rgba(0,0,0,0.6);
        }
        canvas {
            background: #000000;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-5xl">
        <!-- simple top bar -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-5xl font-bold">DoodleHub</h1>
            <a href="gallery.php" class="px-6 py-3 bg-gray-800 hover:bg-gray-700 rounded-lg transition">
                View Gallery →
            </a>
        </div>

        <div class="card rounded-2xl overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <!-- tools sidebar -->
                <aside class="p-8 space-y-8 bg-[#0d0d0d]">
                    <div>
                        <label class="block text-sm font-medium mb-2">Colour</label>
                        <input type="color" id="colorPicker" value="#ffffff" class="w-full h-12 rounded w-full cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Brush Size</label>
                        <input type="range" id="brushSize" min="1" max="40" value="6" class="w-full">
                        <span id="sizeLabel" class="text-sm block mt-2">6px</span>
                    </div>
                    <button id="clearBtn" class="w-full py-4 bg-red-800 hover:bg-red-700 rounded-lg font-medium transition">
                        Clear Canvas
                    </button>
                    <button id="saveBtn" class="w-full py-4 bg-green-800 hover:bg-green-700 rounded-lg font-medium transition">
                        Save Drawing
                    </button>
                </aside>

                <!-- main canvas -->
                <main class="flex-1 p-10">
                    <canvas id="canvas" class="w-full h-96 lg:h-full rounded-xl border-4 border-gray-900 bg-white"></canvas>
                </main>
            </div>
        </div>

        <p class="text-center text-gray-500 mt-8 text-sm">
            built with php • canvas • mysql
        </p>
    </div>

    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const colorPicker = document.getElementById('colorPicker');
        const brushSize = document.getElementById('brushSize');
        const sizeLabel = document.getElementById('sizeLabel');

        let strokes = [];           // full drawing data
        let currentStroke = [];     // current line being drawn
        let drawing = false;
        let color = '#ffffff';
        let size = 6;

        // keep canvas sharp on resize
        function resize() {
            const data = ctx.getImageData(0, 0, canvas.width, canvas.height);
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            ctx.putImageData(data, 0, 0);
        }
        window.addEventListener('resize', resize);
        resize();

        // update tools
        colorPicker.addEventListener('input', () => color = colorPicker.value);
        brushSize.addEventListener('input', () => {
            size = +brushSize.value;
            sizeLabel.textContent = size + 'px';
        });

        // mouse / touch handlers
        const start = e => { e.preventDefault(); drawing = true;
            const r = canvas.getBoundingClientRect();
            const x = (e.clientX || e.touches[0].clientX) - r.left;
            const y = (e.clientY || e.touches[0].clientY) - r.top;
            currentStroke = [{x, y, color, size}];
        };
        const move = e => { if (!drawing) return; e.preventDefault();
            const r = canvas.getBoundingClientRect();
            const x = (e.clientX || e.touches[0].clientX) - r.left;
            const y = (e.clientY || e.touches[0].clientY) - r.top;

            ctx.lineWidth = size;
            ctx.lineCap = 'round';
            ctx.strokeStyle = color;

            ctx.beginPath();
            ctx.moveTo(currentStroke.at(-1).x, currentStroke.at(-1).y);
            ctx.lineTo(x, y);
            ctx.stroke();

            currentStroke.push({x, y, color, size});
        };
        const end = () => { if (drawing) strokes.push(currentStroke); drawing = false; };

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', move);
        canvas.addEventListener('mouseup', end);
        canvas.addEventListener('mouseout', end);
        canvas.addEventListener('touchstart', start);
        canvas.addEventListener('touchmove', move);
        canvas.addEventListener('touchend', end);

        // clear
        document.getElementById('clearBtn').onclick = () => {
            ctx.clearRect(0,0,canvas.width,canvas.height);
            strokes = [];
        };

        // save to mysql
        document.getElementById('saveBtn').onclick = () => {
            if (!strokes.length) return alert("draw something first");

            const title = prompt("title (or leave blank)", "") || "Untitled";

            fetch('save.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    title,
                    strokes,
                    width: canvas.width,
                    height: canvas.height
                })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) alert(`saved — #${res.id}`);
                else alert("save failed");
            });
        };
    </script>
</body>
</html>