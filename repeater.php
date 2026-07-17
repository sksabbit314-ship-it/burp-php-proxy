<?php
$requests = json_decode(@file_get_contents('storage/requests.json'), true) ?? [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Repeater - Burp PHP Proxy</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0a0a0a; color: #00ff41; font-family: 'Share Tech Mono', monospace; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { border-bottom: 2px solid #00ff41; padding-bottom: 15px; margin-bottom: 30px; }
        .header h1 { font-size: 2rem; text-shadow: 0 0 20px #00ff41; }
        .panel { background: rgba(0,255,65,0.03); border: 1px solid #00ff4133; border-radius: 8px; padding: 20px; margin-bottom: 25px; }
        .panel h3 { color: #00ff41; border-bottom: 1px solid #00ff4133; padding-bottom: 8px; margin-bottom: 15px; }
        .btn { background: transparent; color: #00ff41; border: 1px solid #00ff41; padding: 10px 25px; font-family: 'Share Tech Mono', monospace; cursor: pointer; border-radius: 4px; transition: all 0.3s; text-decoration: none; display: inline-block; margin: 5px 5px 5px 0; }
        .btn:hover { background: #00ff41; color: #0a0a0a; box-shadow: 0 0 30px #00ff4166; }
        .btn-danger { border-color: #ff0040; color: #ff0040; }
        .btn-danger:hover { background: #ff0040; color: #0a0a0a; }
        textarea, input { background: #0a0a0a; color: #00ff41; border: 1px solid #00ff4133; padding: 10px; font-family: 'Share Tech Mono', monospace; width: 100%; border-radius: 4px; margin: 5px 0; }
        .request-list { max-height: 300px; overflow: auto; }
        .request-item { border-bottom: 1px solid #00ff4111; padding: 8px; cursor: pointer; }
        .request-item:hover { background: #00ff4111; }
        .method { color: #ffaa00; }
        .path { color: #00ccff; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #00ff41; border-radius: 3px; }
        a { color: #00ff41; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔁 Repeater</h1>
            <a href="index.php" class="btn">⬅ Back to Dashboard</a>
        </div>

        <div class="panel">
            <h3>📋 Captured Requests</h3>
            <div class="request-list">
                <?php if (empty($requests)): ?>
                    <p>No requests captured yet. Start Proxy and send traffic.</p>
                <?php else: ?>
                    <?php foreach (array_reverse($requests) as $index => $req): ?>
                        <div class="request-item" onclick="loadRequest(<?= $index ?>)">
                            <span class="method"><?= preg_match('/^([A-Z]+)/', $req['raw'], $m) ? $m[1] : '???' ?></span>
                            <span class="path"><?= preg_match('/^[A-Z]+\s+([^\s]+)/', $req['raw'], $m) ? $m[1] : '/' ?></span>
                            <span style="color:#00ff4188;font-size:0.8rem;">[<?= $req['time'] ?>]</span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel">
            <h3>✏️ Edit & Send</h3>
            <form id="repeaterForm">
                <input type="hidden" name="index" id="reqIndex">
                <textarea id="rawRequest" rows="10" placeholder="Select a request from above..."></textarea>
                <button type="submit" class="btn">🚀 Send</button>
            </form>
            <div id="responseBox" style="margin-top:15px;border-top:1px solid #00ff4133;padding-top:15px;display:none;">
                <h3>📥 Response</h3>
                <pre id="responseContent" style="background:#0a0a0a;padding:15px;border:1px solid #00ff4133;border-radius:4px;overflow:auto;max-height:400px;white-space:pre-wrap;word-break:break-all;"></pre>
            </div>
        </div>
    </div>

    <script>
        const requests = <?= json_encode($requests) ?>;

        function loadRequest(index) {
            const req = requests[index];
            if (!req) return;
            document.getElementById('reqIndex').value = index;
            document.getElementById('rawRequest').value = req.raw || '';
        }

        document.getElementById('repeaterForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const raw = document.getElementById('rawRequest').value;
            const index = document.getElementById('reqIndex').value;
            
            if (!raw) return;

            // Parse the raw request to JSON
            const lines = raw.split('\n');
            const requestLine = lines[0].split(' ');
            const method = requestLine[0] || 'GET';
            const path = requestLine[1] || '/';
            const headers = {};
            let body = '';
            let inBody = false;

            for (let line of lines.slice(1)) {
                if (inBody) { body += line + '\n'; continue; }
                if (line.trim() === '') { inBody = true; continue; }
                if (line.includes(': ')) {
                    const [key, val] = line.split(': ');
                    headers[key] = val;
                }
            }

            const requestData = { method, path, headers, body: body.trim() };

            try {
                const resp = await fetch('/api.php?action=repeater', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(requestData)
                });
                const data = await resp.json();
                const box = document.getElementById('responseBox');
                const content = document.getElementById('responseContent');
                box.style.display = 'block';
                content.textContent = data.response || '(empty response)';
            } catch (err) {
                alert('Error: ' + err.message);
            }
        });
    </script>
</body>
</html>
