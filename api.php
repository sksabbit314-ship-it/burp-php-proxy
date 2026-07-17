<?php
header('Content-Type: application/json');

require_once __DIR__ . '/core/ProxyServer.php';
require_once __DIR__ . '/core/HttpParser.php';
require_once __DIR__ . '/core/RepeaterEngine.php';
require_once __DIR__ . '/core/IntruderEngine.php';
require_once __DIR__ . '/core/OTPBypassWizard.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'start':
        $pid = pcntl_fork();
        if ($pid == 0) {
            $proxy = new ProxyServer();
            $proxy->start();
            exit(0);
        } else {
            file_put_contents('/tmp/proxy.pid', $pid);
            echo json_encode(['status' => 'started', 'pid' => $pid]);
        }
        break;

    case 'stop':
        if (file_exists('/tmp/proxy.pid')) {
            $pid = file_get_contents('/tmp/proxy.pid');
            exec("kill -9 $pid");
            unlink('/tmp/proxy.pid');
            echo json_encode(['status' => 'stopped']);
        } else {
            echo json_encode(['status' => 'not running']);
        }
        break;

    case 'log':
        echo file_get_contents('storage/requests.json') ?: '[]';
        break;

    case 'clear':
        file_put_contents('storage/requests.json', '[]');
        echo json_encode(['status' => 'cleared']);
        break;

    case 'repeater':
        $data = json_decode(file_get_contents('php://input'), true);
        $response = RepeaterEngine::send($data);
        echo json_encode(['response' => $response]);
        break;

    case 'intruder':
        $data = json_decode(file_get_contents('php://input'), true);
        $results = IntruderEngine::bruteForce($data['request'], $data['param']);
        echo json_encode(['results' => $results]);
        break;

    case 'otp-bypass':
        $data = json_decode(file_get_contents('php://input'), true);
        $results = OTPBypassWizard::runAll($data['request']);
        echo json_encode($results);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>
