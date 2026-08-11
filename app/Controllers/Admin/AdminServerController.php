<?php

namespace App\Controllers\Admin;

use App\Models\Server;
use App\Models\User;
use App\Models\AdminLog;
use App\Middleware\AdminMiddleware;
use App\Services\Database;
use App\Services\EncryptionService;

class AdminServerController {

    public function index() {
        AdminMiddleware::handle();
        $servers = Server::getAll();
        
        foreach ($servers as &$srv) {
            $srv['decrypted_password'] = EncryptionService::decrypt($srv['encrypted_password']);
        }

        view('admin.servers.index', [
            'title' => 'Server Inventory Management',
            'servers' => $servers
        ]);
    }

    public function create() {
        AdminMiddleware::handle();
        view('admin.servers.create_edit', [
            'title' => 'Add New Server to Pool',
            'server' => null
        ]);
    }

    public function store() {
        AdminMiddleware::handle();
        verify_csrf();

        $hostIp = trim($_POST['host_ip'] ?? '');
        $sshPort = (int)($_POST['ssh_port'] ?? 22);
        $username = trim($_POST['username'] ?? 'root');
        $password = $_POST['password'] ?? '';
        $location = trim($_POST['location'] ?? 'USA');
        $provider = trim($_POST['provider'] ?? 'Internal Cloud');
        $type = trim($_POST['server_type'] ?? 'VPS');
        $status = trim($_POST['status'] ?? 'Available');
        $notes = trim($_POST['notes'] ?? '');

        if (empty($hostIp) || empty($password)) {
            flash('error', 'Host IP and Password are required.');
            redirect('/admin/servers/create');
        }

        $serverId = Server::create([
            'host_ip' => $hostIp,
            'ssh_port' => $sshPort,
            'username' => $username,
            'password' => $password,
            'location' => $location,
            'provider' => $provider,
            'server_type' => $type,
            'status' => $status,
            'notes' => $notes
        ]);

        AdminLog::log('Server Created', 'Server', $serverId, "Added server {$hostIp}");

        flash('success', "Server {$hostIp} added to inventory.");
        redirect('/admin/servers');
    }

    public function edit(int $id) {
        AdminMiddleware::handle();
        $server = Server::findById($id);

        if (!$server) {
            flash('error', 'Server not found.');
            redirect('/admin/servers');
        }

        $server['decrypted_password'] = EncryptionService::decrypt($server['encrypted_password']);

        view('admin.servers.create_edit', [
            'title' => "Edit Server — {$server['host_ip']}",
            'server' => $server
        ]);
    }

    public function update(int $id) {
        AdminMiddleware::handle();
        verify_csrf();

        $server = Server::findById($id);
        if (!$server) {
            flash('error', 'Server not found.');
            redirect('/admin/servers');
        }

        $hostIp = trim($_POST['host_ip'] ?? '');
        $sshPort = (int)($_POST['ssh_port'] ?? 22);
        $username = trim($_POST['username'] ?? 'root');
        $password = $_POST['password'] ?? ''; // Optional if unchanged
        $location = trim($_POST['location'] ?? 'USA');
        $provider = trim($_POST['provider'] ?? 'Internal Cloud');
        $type = trim($_POST['server_type'] ?? 'VPS');
        $status = trim($_POST['status'] ?? 'Available');
        $notes = trim($_POST['notes'] ?? '');

        Server::update($id, [
            'host_ip' => $hostIp,
            'ssh_port' => $sshPort,
            'username' => $username,
            'password' => $password,
            'location' => $location,
            'provider' => $provider,
            'server_type' => $type,
            'status' => $status,
            'notes' => $notes
        ]);

        AdminLog::log('Server Updated', 'Server', $id, "Updated server {$hostIp}");

        flash('success', "Server {$hostIp} updated successfully.");
        redirect('/admin/servers');
    }

    public function toggleStatus(int $id, string $status) {
        AdminMiddleware::handle();
        verify_csrf();

        Database::execute("UPDATE servers SET status = ? WHERE id = ?", [$status, $id]);
        Database::execute("UPDATE server_assignments SET status = ? WHERE server_id = ?", [$status, $id]);

        AdminLog::log('Server Status Change', 'Server', $id, "Changed status to {$status}");

        flash('success', "Server status updated to {$status}.");
        redirect('/admin/servers');
    }
}
