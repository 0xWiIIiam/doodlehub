<?php
$id = (int)($_GET['id'] ?? 0);
$pdo = new PDO('mysql:host=localhost;dbname=doodlehub', 'root', '');
$drawing = $pdo->query("SELECT title,data FROM drawings WHERE id=$id")->fetch();

if (!$drawing) die("not found");

$data = json_decode($drawing['data'], true);
$strokes = $data['strokes'] ?? [];
$w = $data['width'] ?? 800;
$h = $data['height'] ?? 600;
$title = htmlspecialchars($drawing['title']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?> · DoodleHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght=500;700&display=swap" rel="stylesheet">
    <style>
        body { background:#0a0a0a; color:#e0e0e0; font-family:'Inter',sans-serif; min-h-screen flex items-center justify-center p-8; }
        canvas { background:#000; border:4px solid #222; border-radius:12px; }
    </style>
</head>
<body>
    <div class="text-center">
        <h1 class="text-4xl font-bold mb-8"><?= $title ?></h1>
        <canvas id="c" width="<?= $w ?>" height="<?= $h ?>" class="max-w-full max-h-screen bg-white border-4 border-gray-800 rounded-xl"></canvas>
        <div class="mt-10 space-x-6">
            <a href="gallery.php" class="px-6 py-3 bg-gray-800 hover:bg-gray-700 rounded-lg transition">← Gallery</a>
            <a href="index.php" class="px-6 py-3 bg-green-800 hover:bg-green-700 rounded-lg transition">Draw More</a>
        </div>
    </div>

    <script>
        const c = document.getElementById('c');
        const ctx = c.getContext('2d');
        const strokes = <?= json_encode($strokes) ?>;

        const scale = Math.min(c.clientWidth / <?= $w ?>, c.clientHeight / <?= $h ?>);
        ctx.scale(scale, scale);
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, c.width, c.height);

        strokes.forEach(s => {
            if (!s.length) return;
            ctx.lineCap = 'round';
            ctx.lineWidth = s[0].size;
            ctx.strokeStyle = s[0].color;
            ctx.beginPath();
            s.forEach((p,i) => i===0 ? ctx.moveTo(p.x,p.y) : ctx.lineTo(p.x,p.y));
            ctx.stroke();
        });
    </script>
</body>
</html>