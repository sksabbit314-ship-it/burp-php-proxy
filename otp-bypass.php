<!DOCTYPE html>
<html>
<head>
    <title>OTP Bypass - Burp PHP Proxy</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0a0a0a; color: #ff0040; font-family: 'Share Tech Mono', monospace; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { border-bottom: 2px solid #ff0040; padding-bottom: 15px; margin-bottom: 30px; }
        .header h1 { font-size: 2rem; text-shadow: 0 0 20px #ff0040; color: #ff0040; }
        .panel { background: rgba(255,0,64,0.03); border: 1px solid #ff004033; border-radius: 8px; padding: 20px; margin-bottom: 25px; }
        .panel h3 { color: #ff0040; border-bottom: 1px solid #ff004033; padding-bottom: 8px; margin-bottom: 15px; }
        .btn { background: transparent; color: #ff0040; border: 1px solid #ff0040; padding: 10px 25px; font-family: 'Share Tech Mono', monospace; cursor: pointer; border-radius: 4px; transition: all 0.3s; text-decoration: none; display: inline-block; margin: 5px 5px 5px 0; }
        .btn:hover { background: #ff0040; color: #0a0a0a; box-shadow: 0 0 30px #ff004066; }
        .btn-success { border-color: #00ff41; color: #00ff41; }
        .btn-success:hover { background: #00ff41; color: #0a0a0a; }
        textarea, input { background: #0a0a0a; color: #ff0040; border: 1px solid #ff004033; padding: 10px; font-family: 'Share Tech Mono', monospace; width: 100%; border-radius: 4px; margin: 5px 0; }
        .result-item { border-bottom: 1px solid #ff004011; padding: 10px; }
        .pass { color: #00ff41; }
        .fail { color: #ff0040; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #ff0040; border-radius: 3px; }
        a { color: #ff0040; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔓 OTP Bypass Wizard</h1>
            <a href="index.php" class="btn">⬅ Back to Dashboard</a>
        </div>

        <div class="panel">
            <h3>🎯 Target Request</h3>
            <form id="bypassForm">
                <label>Request Body (with OTP parameter)</label>
                <textarea id="requestBody" rows="5" placeholder='{"phone":"017xxxxxxx","otp":"1234"}'></textarea>
                
                <label>Target URL</label>
                <input id="targetUrl" placeholder="https://example.com/api/verify-otp" value="https://httpbin.org/post">
                
                <div style="display:flex;flex-wrap:wrap;gap:10px;margin:15px 0;">
                    <button type="submit" class="btn">🚀 Run All Attacks</button>
                </div>
            </form>
        </div>

        <div class="panel" id="resultPanel" style="display:none;">
            <h3>📊 Attack Results</h3>
            <div id="results"></div>
        </div>
    </div>

    <script>
        document.getElementById('bypassForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const body = document.getElementById('requestBody').value;
            const url = document.getElementById('targetUrl').value;

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
            
            panel.style.display = 'block';
            resultsDiv.innerHTML = '⏳ Running attacks...';

            try {
                const resp = await fetch('/api.php?action=otp-bypass', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ request: requestData })
                });
                const data = await resp.json();
                
                let html = '';
                const attacks = [
                    'parameter_removal', 'null_value', 'negative_value',
                    'race_condition', 'type_juggling', 'expiry_time'
                ];
                const labels = {
                    'parameter_removal': '📌 Parameter Removal',
                    'null_value': '🚫 Null Value',
                    'negative_value': '➖ Negative Value',
                    'race_condition': '🏎️ Race Condition',
                    'type_juggling': '🔄 Type Juggling',
                    'expiry_time': '⏰ Expiry Time Attack'
                };
                
                attacks.forEach(key => {
                    const result = data[key] || '⚠️ No result';
                    const isSuccess = result.includes('success') || result.includes('✓');
                    html += `<div class="result-item ${isSuccess ? 'pass' : 'fail'}">
                        <strong>${labels[key] || key}</strong><br>
                        → ${result}
                    </div>`;
                });
                
                resultsDiv.innerHTML = html;
            } catch (err) {
                resultsDiv.innerHTML = '❌ Error: ' + err.message;
            }
        });
    </script>
</body>
</html>
