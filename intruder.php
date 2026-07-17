<!DOCTYPE html>
<html>
<head>
    <title>Intruder - Burp PHP Proxy</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0a0a0a; color: #00ff41; font-family: 'Share Tech Mono', monospace; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { border-bottom: 2px solid #ffaa00; padding-bottom: 15px; margin-bottom: 30px; }
        .header h1 { font-size: 2rem; text-shadow: 0 0 20px #ffaa00; color: #ffaa00; }
        .panel { background: rgba(255,170,0,0.03); border: 1px solid #ffaa0033; border-radius: 8px; padding: 20px; margin-bottom: 25px; }
        .panel h3 { color: #ffaa00; border-bottom: 1px solid #ffaa0033; padding-bottom: 8px; margin-bottom: 15px; }
        .btn { background: transparent; color: #ffaa00; border: 1px solid #ffaa00; padding: 10px 25px; font-family: 'Share Tech Mono', monospace; cursor: pointer; border-radius: 4px; transition: all 0.3s; text-decoration: none; display: inline-block; margin: 5px 5px 5px 0; }
        .btn:hover { background: #ffaa00; color: #0a0a0a; box-shadow: 0 0 30px #ffaa0066; }
        input, textarea, select { background: #0a0a0a; color: #ffaa00; border: 1px solid #ffaa0033; padding: 10px; font-family: 'Share Tech Mono', monospace; width: 100%; border-radius: 4px; margin: 5px 0; }
        .result-item { border-bottom: 1px solid #ffaa0011; padding: 8px; }
        .success { color: #00ff41; }
        .fail { color: #ff0040; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #ffaa00; border-radius: 3px; }
        a { color: #ffaa00; text-decoration: none; }
        .stats { color: #ffaa0088; font-size: 0.9rem; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💥 Intruder - OTP Brute Force</h1>
            <a href="index.php" class="btn">⬅ Back to Dashboard</a>
        </div>

        <div class="panel">
            <h3>⚙️ Attack Configuration</h3>
            <form id="intruderForm">
                <label>Request Body (use {OTP} as placeholder)</label>
                <textarea id="requestBody" rows="5" placeholder='{"phone":"017xxxxxxx","otp":"{OTP}"}'></textarea>
                
                <label>Target URL</label>
                <input id="targetUrl" placeholder="https://example.com/api/verify-otp" value="https://httpbin.org/post">
                
                <label>OTP Range</label>
                <div style="display:flex;gap:10px;">
                    <input id="fromRange" type="number" value="0" style="width:50%;">
                    <input id="toRange" type="number" value="9999" style="width:50%;">
                </div>
                
                <button type="submit" class="btn">🔥 Start Attack</button>
            </form>
        </div>

        <div class="panel" id="resultPanel" style="display:none;">
            <h3>📊 Results</h3>
            <div class="stats" id="stats"></div>
            <div id="results"></div>
        </div>
    </div>

    <script>
        document.getElementById('intruderForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const body = document.getElementById('requestBody').value;
            const url = document.getElementById('targetUrl').value;
            const from = parseInt(document.getElementById('fromRange').value) || 0;
            const to = parseInt(document.getElementById('toRange').value) || 9999;

            if (!body || !url) {
                alert('Please fill all fields');
                return;
            }

            const requestData = {
                method: 'POST',
                path: url,
                headers: { 'Content-Type': 'application/json' },
                body: body
            };

            const panel = document.getElementById('resultPanel');
            const resultsDiv = document.getElementById('results');
            const statsDiv = document.getElementById('stats');
            
            panel.style.display = 'block';
            resultsDiv.innerHTML = '⏳ Running attack... please wait...';
            statsDiv.textContent = `Testing ${to - from + 1} OTPs from ${from} to ${to}`;

            try {
                const resp = await fetch('/api.php?action=intruder', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ request: requestData, param: '{OTP}' })
                });
                const data = await resp.json();
                
                let html = '';
                let successCount = 0;
                data.results.forEach(r => {
                    const isSuccess = r.response.includes('success') || r.response.includes('verified');
                    if (isSuccess) successCount++;
                    html += `<div class="result-item ${isSuccess ? 'success' : 'fail'}">
                        OTP: ${r.otp} → ${isSuccess ? '✅ SUCCESS' : '❌ FAIL'} 
                        <span style="color:#00ff4188;font-size:0.8rem;">${r.response.substring(0, 100)}</span>
                    </div>`;
                });
                
                statsDiv.textContent = `✅ ${successCount} success | ❌ ${data.results.length - successCount} fail | Total: ${data.results.length}`;
                resultsDiv.innerHTML = html;
            } catch (err) {
                resultsDiv.innerHTML = '❌ Error: ' + err.message;
            }
        });
    </script>
</body>
</html>
