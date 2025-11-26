<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoodleHub · Gallery</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { background:#0a0a0a; color:#e0e0e0; font-family:'Inter',sans-serif; padding:3rem 1rem; }
        .card { background:#111; border:1px solid #222; border-radius:12px; overflow:hidden; transition:0.3s; }
        .card:hover { transform:translateY(-4px); box-shadow:0 12px 40px rgba(0,0,0,0.7); }
    </style>
</head>
<body>
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-12">
            <h1 class="text-5xl font-bold">Gallery</h1>
            <a href="index.php" class="px-6 py-3 bg-gray-800 hover:bg-gray-700 rounded-lg transition">
                ← Draw More
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <?php
            $pdo = new PDO('mysql:host=localhost;dbname=doodlehub', 'root', '');
            foreach ($pdo->query("SELECT id,title,data FROM drawings ORDER BY id DESC") as $row) {
                $d = json_decode($row['data'], true);
                $w = $d['width'] ?? 800;
                $h = $d['height'] ?? 600;
            ?>
            <a href="view.php?id=<?= $row['id'] ?>" class="block">
                <div class="card">
                    <canvas width="<?= $w ?>" height="<?= $h ?>" class="w-full bg-black"
                            data-strokes='<?= json_encode($d['strokes'] ?? []) ?>'></canvas>
                    <div class="p-3 text-center text-sm truncate"><?= htmlspecialchars($row['title']) ?></div>
                </div>
            </a>
            <?php } ?>
        </div>

        <?php if (!$pdo->query("SELECT 1 FROM drawings")->fetch()): ?>
            <p class="text-center text-2xl mt-20 opacity-50">no drawings yet — go make some</p>
        <?php endif; ?>
    </div>

    <script>
        // render thumbnails with correct scaling
        document.querySelectorAll('canvas').forEach(c => {
            const ctx = c.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, c.width, c.height);
            const strokes = JSON.parse(c.dataset.strokes);
            const scale = Math.min(c.clientWidth / c.width, c.clientHeight / c.height);
            ctx.scale(scale, scale);
            strokes.forEach(s => {
                if (!s.length) return;
                ctx.lineCap = 'round';
                ctx.lineWidth = s[0].size;
                ctx.strokeStyle = s[0].color;
                ctx.beginPath();
                s.forEach((p,i) => i===0 ? ctx.moveTo(p.x,p.y) : ctx.lineTo(p.x,p.y));
                ctx.stroke();
            });
        });
    </script>
</body>
</html>