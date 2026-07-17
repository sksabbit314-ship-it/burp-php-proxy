<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🖥️ Burp PHP Proxy - Hacker Edition</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #0a0a0a;
            color: #00ff41;
            font-family: 'Share Tech Mono', monospace;
            min-height: 100vh;
            padding: 20px;
            background-image: 
                linear-gradient(rgba(0, 255, 65, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 65, 0.03) 1px, transparent 1px);
            background-size: 30px 30px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            border-bottom: 2px solid #00ff41;
            padding-bottom: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .header h1 {
            font-size: 2.2rem;
            text-shadow: 0 0 20px #00ff41, 0 0 40px #00ff4133;
            letter-spacing: 3px;
        }
        
        .header .status {
            color: #00ff41;
            font-size: 0.9rem;
            padding: 8px 18px;
            border: 1px solid #00ff41;
            border-radius: 4px;
            background: rgba(0, 255, 65, 0.05);
            animation: blink 1.5s infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        .panel {
            background: rgba(0, 255, 65, 0.03);
            border: 1px solid #00ff4133;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            backdrop-filter: blur(5px);
        }
        
        .panel h3 {
            color: #00ff41;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
            border-bottom: 1px solid #00ff4133;
            padding-bottom: 8px;
        }
        
        .btn {
            background: transparent;
            color: #00ff41;
            border: 1px solid #00ff41;
            padding: 10px 25px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.9rem;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.3s ease;
            margin: 5px 5px 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: #00ff41;
            color: #0a0a0a;
            box-shadow: 0 0 30px #00ff4166;
        }
        
        .btn-danger {
            border-color: #ff0040;
            color: #ff0040;
        }
        
        .btn-danger:hover {
            background: #ff0040;
            color: #0a0a0a;
            box-shadow: 0 0 30px #ff004066;
        }
        
        .btn-warning {
            border-color: #ffaa00;
            color: #ffaa00;
        }
        
        .btn-warning:hover {
            background: #ffaa00;
            color: #0a0a0a;
            box-shadow: 0 0 30px #ffaa0066;
        }
        
        #log {
            background: #0a0a0a;
            border: 1px solid #00ff4133;
            border-radius: 4px;
            height: 400px;
            overflow: auto;
            padding: 15px;
            font-size: 0.85rem;
            line-height: 1.6;
            color: #00ff41;
            font-family: 'Share Tech Mono', monospace;
        }
        
        #log .entry {
            border-bottom: 1px solid #00ff4111;
            padding: 4px 0;
        }
        
        #log .time {
            color: #00ff4188;
            margin-right: 15px;
        }
        
        #log .method {
            color: #ffaa00;
            margin-right: 10px;
        }
        
        #log .path {
            color: #00ccff;
        }
        
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0a0a0a;
        }
        ::-webkit-scrollbar-thumb {
            background: #00ff41;
            border-radius: 3px;
        }
        
        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #00ff4166;
            font-size: 0.8rem;
            border-top: 1px solid #00ff4111;
            padding-top: 20px;
        }
        
        @media (max-width: 768px) {
            .header h1 { font-size: 1.5rem; }
            .btn { padding: 8px 16px; font-size: 0.8rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🖥️ Burp PHP Proxy</h1>
            <div class="status">● SYSTEM ACTIVE</div>
        </div>

        <div class="panel">
            <h3>▶ CONTROL INTERFACE</h3>
            <div>
                <button class="btn" onclick="startProxy()">▶ START PROXY</button>
                <button class="btn btn-danger" onclick="stopProxy()">⏹ STOP PROXY</button>
                <button class="btn btn-warning" onclick="clearLog()">🗑 CLEAR LOG</button>
            </div>
        </div>

        <div class="panel">
            <h3>📡 LIVE TRAFFIC LOG</h3>
            <div id="log">⏳ INITIALIZING...</div>
        </div>

        <div class="panel">
            <h3>⚡ ATTACK MODULES</h3>
            <div class="nav-links">
                <a href="repeater.php" class="btn">🔁 REPEATER</a>
                <a href="intruder.php" class="btn btn-warning">💥 INTRUDER</a>
                <a href="otp-bypass.php" class="btn btn-danger">🔓 OTP BYPASS</a>
            </div>
        </div>

        <div class="footer">
            <span>⚡ burp-php-proxy v2.0 | encrypted channel | root access only</span>
        </div>
    </div>

    <script>
        function startProxy() {
            fetch('/api.php?action=start')
                .then(r => r.text())
                .then(data => {
                    alert('✅ Proxy Started\n' + data);
                    updateStatus('ACTIVE');
                })
                .catch(() => alert('❌ Failed to start proxy'));
        }

        function stopProxy() {
            fetch('/api.php?action=stop')
                .then(r => r.text())
                .then(data => {
                    alert('⏹ Proxy Stopped\n' + data);
                    updateStatus('STOPPED');
                })
                .catch(() => alert('❌ Failed to stop proxy'));
        }

        function clearLog() {
            fetch('/api.php?action=clear')
                .then(r => r.text())
                .then(() => {
                    document.getElementById('log').innerHTML = '🗑 LOG CLEARED';
                })
                .catch(() => alert('❌ Failed to clear log'));
        }

        function updateStatus(status) {
            const el = document.querySelector('.status');
            if (el) el.textContent = '● ' + status;
        }

        setInterval(() => {
            fetch('/api.php?action=log')
                .then(r => r.text())
                .then(data => {
                    const log = document.getElementById('log');
                    if (data && data !== '[]') {
                        try {
                            const entries = JSON.parse(data);
                            if (entries.length === 0) {
                                log.innerHTML = '📡 NO TRAFFIC DETECTED';
                                return;
                            }
                            let html = '';
                            entries.slice(-50).reverse().forEach(e => {
                                const raw = e.raw || '';
                                const method = raw.match(/^([A-Z]+)/) ? raw.match(/^([A-Z]+)/)[1] : '???';
                                const path = raw.match(/^[A-Z]+\s+([^\s]+)/) ? raw.match(/^[A-Z]+\s+([^\s]+)/)[1] : '/';
                                html += `<div class="entry">
                                    <span class="time">[${e.time}]</span>
                                    <span class="method">${method}</span>
                                    <span class="path">${path}</span>
                                </div>`;
                            });
                            log.innerHTML = html;
                        } catch (e) {
                            log.innerHTML = '⚠️ ERROR PARSING LOG';
                        }
                    } else {
                        log.innerHTML = '📡 NO TRAFFIC DETECTED';
                    }
                })
                .catch(() => {
                    // silent fail
                });
        }, 2000);
    </script>
</body>
</html>
