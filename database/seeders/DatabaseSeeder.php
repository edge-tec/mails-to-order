<?php

namespace Database\Seeders;

use App\Services\Database;
use App\Services\EncryptionService;

class DatabaseSeeder {

    public static function run(): void {
        self::seedSettings();
        self::seedPackages();
        self::seedPaymentMethods();
        self::seedEmailTemplates();
        self::seedAdminUser();
        self::seedSampleServers();
    }

    private static function seedSettings(): void {
        $settings = [
            'site_name' => 'Server Provisioning Portal',
            'site_url' => config('app.url', 'http://localhost:8000'),
            'company_name' => 'Edge-Tec Server Solutions',
            'company_logo' => '/assets/images/logo.png',
            'currency' => 'USD',
            'notify_user_registration' => '1',
            'notify_order_submitted' => '1',
            'notify_order_approved' => '1',
            'notify_order_rejected' => '1',
            'notify_server_activated' => '1',
            'notify_server_suspended' => '1',
            'notify_admin_new_order' => '1',
            'notify_admin_new_payment' => '1',
        ];

        foreach ($settings as $key => $val) {
            Database::execute(
                "INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE setting_value = ?",
                [$key, $val, $val]
            );
        }
    }

    private static function seedPackages(): void {
        $packages = [
            [
                'name' => 'Daily 500 POP',
                'type' => 'standard',
                'description' => 'Ideal for small-scale email delivery and automated server tasks. 500 POP limit per day, up to 7,000 POP per month.',
                'daily_pop_limit' => 500,
                'monthly_pop_limit' => 7000,
                'price' => 29.99,
                'currency' => 'USD',
                'status' => 'active',
                'sort_order' => 1
            ],
            [
                'name' => 'Daily 1,000 POP',
                'type' => 'standard',
                'description' => 'Designed for growing platforms needing higher daily output. 1,000 POP limit per day, up to 10,000 POP per month.',
                'daily_pop_limit' => 1000,
                'monthly_pop_limit' => 10000,
                'price' => 49.99,
                'currency' => 'USD',
                'status' => 'active',
                'sort_order' => 2
            ],
            [
                'name' => 'Custom Package',
                'type' => 'custom',
                'description' => 'Tailored enterprise infrastructure. Configure custom daily & monthly POP capacity, dedicated IP subnet, and preferred geographical region.',
                'daily_pop_limit' => 0,
                'monthly_pop_limit' => 0,
                'price' => 0.00,
                'currency' => 'USD',
                'status' => 'active',
                'sort_order' => 3
            ]
        ];

        foreach ($packages as $pkg) {
            $existing = Database::fetch("SELECT id FROM packages WHERE name = ?", [$pkg['name']]);
            if (!$existing) {
                Database::insert(
                    "INSERT INTO packages (name, type, description, daily_pop_limit, monthly_pop_limit, price, currency, status, sort_order, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
                    [
                        $pkg['name'], $pkg['type'], $pkg['description'],
                        $pkg['daily_pop_limit'], $pkg['monthly_pop_limit'],
                        $pkg['price'], $pkg['currency'], $pkg['status'], $pkg['sort_order']
                    ]
                );
            }
        }
    }

    private static function seedPaymentMethods(): void {
        $methods = [
            [
                'code' => 'bkash',
                'name' => 'bKash Personal',
                'type' => 'mobile_wallet',
                'personal_number' => '01700000000',
                'currency' => 'BDT',
                'network' => null,
                'wallet_address' => null,
                'instructions' => "1. Go to your bKash Mobile App or dial *247#\n2. Select Send Money\n3. Enter Personal Number: 01700000000\n4. Enter the order total amount\n5. Enter your order reference\n6. Copy and paste the Transaction ID below.",
                'status' => 'active'
            ],
            [
                'code' => 'nagad',
                'name' => 'Nagad Personal',
                'type' => 'mobile_wallet',
                'personal_number' => '01800000000',
                'currency' => 'BDT',
                'network' => null,
                'wallet_address' => null,
                'instructions' => "1. Open your Nagad Mobile App\n2. Choose Send Money\n3. Enter Nagad Personal Number: 01800000000\n4. Submit payment and enter your TrxID in the form.",
                'status' => 'active'
            ],
            [
                'code' => 'crypto_usdt_trc20',
                'name' => 'Cryptocurrency (USDT TRC20)',
                'type' => 'crypto',
                'personal_number' => null,
                'currency' => 'USDT',
                'network' => 'TRC20',
                'wallet_address' => 'T9yD14Nj9j7xXv8mN2kP4qL6sW3eR5tY7u',
                'instructions' => "Send exact payment in USDT via TRC20 network to address: T9yD14Nj9j7xXv8mN2kP4qL6sW3eR5tY7u. Submit the TxHash below.",
                'status' => 'active'
            ],
            [
                'code' => 'crypto_btc',
                'name' => 'Cryptocurrency (Bitcoin BTC)',
                'type' => 'crypto',
                'personal_number' => null,
                'currency' => 'BTC',
                'network' => 'Bitcoin',
                'wallet_address' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
                'instructions' => "Send BTC payment to wallet: bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh. Enter TxHash once broadcasted.",
                'status' => 'active'
            ]
        ];

        foreach ($methods as $m) {
            $existing = Database::fetch("SELECT id FROM payment_methods WHERE code = ?", [$m['code']]);
            if (!$existing) {
                Database::insert(
                    "INSERT INTO payment_methods (code, name, type, personal_number, currency, network, wallet_address, instructions, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
                    [
                        $m['code'], $m['name'], $m['type'], $m['personal_number'],
                        $m['currency'], $m['network'], $m['wallet_address'],
                        $m['instructions'], $m['status']
                    ]
                );
            }
        }
    }

    private static function seedEmailTemplates(): void {
        $templates = [
            [
                'template_key' => 'order_approved',
                'title' => 'Order Approved & Server Credentials',
                'subject' => 'Your Server Order Has Been Approved - {{order_number}}',
                'body_html' => '<h2>Hello {{customer_name}},</h2><p>Your server order <strong>{{order_number}}</strong> is active!</p>'
            ],
            [
                'template_key' => 'user_registration',
                'title' => 'User Registration Welcome',
                'subject' => 'Welcome to Server Provisioning Portal',
                'body_html' => '<h2>Welcome {{customer_name}}!</h2><p>Thank you for joining our platform.</p>'
            ],
            [
                'template_key' => 'order_submitted',
                'title' => 'Order Confirmation',
                'subject' => 'Order Received - {{order_number}}',
                'body_html' => '<h2>Order Received</h2><p>We are reviewing your payment for {{order_number}}.</p>'
            ]
        ];

        foreach ($templates as $t) {
            Database::execute(
                "INSERT INTO email_templates (template_key, title, subject, body_html, updated_at)
                VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE title = VALUES(title), subject = VALUES(subject), body_html = VALUES(body_html)",
                [$t['template_key'], $t['title'], $t['subject'], $t['body_html']]
            );
        }
    }

    private static function seedAdminUser(): void {
        $email = 'admin@example.com';
        $existing = Database::fetch("SELECT id FROM users WHERE email = ?", [$email]);
        
        if (!$existing) {
            $userId = Database::insert(
                "INSERT INTO users (name, email, phone, address, password_hash, role, status, email_verified_at, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())",
                [
                    'System Administrator',
                    $email,
                    '+1 800-555-0199',
                    '100 Tech Blvd, Silicon Valley, CA',
                    password_hash('Admin@123456', PASSWORD_BCRYPT),
                    'super_admin',
                    'active'
                ]
            );

            Database::insert(
                "INSERT INTO admins (user_id, department, permissions_json, created_at) VALUES (?, ?, ?, NOW())",
                [$userId, 'Super Administration', json_encode(['all' => true])]
            );
        }
    }

    private static function seedSampleServers(): void {
        $sampleServers = [
            [
                'host_ip' => '192.168.10.101',
                'ssh_port' => 22,
                'username' => 'root',
                'raw_password' => 'SrvP@ss_500_A',
                'server_type' => 'Dedicated VPS',
                'location' => 'USA - Ashburn',
                'provider' => 'HighSpeed Cloud',
                'status' => 'Available',
                'notes' => 'Pre-configured POP daily 500 node'
            ],
            [
                'host_ip' => '192.168.10.102',
                'ssh_port' => 2222,
                'username' => 'admin_pop',
                'raw_password' => 'SrvP@ss_1000_B',
                'server_type' => 'KVM VPS',
                'location' => 'Germany - Frankfurt',
                'provider' => 'Hetzn Enterprise',
                'status' => 'Available',
                'notes' => 'Pre-configured POP daily 1000 node'
            ],
            [
                'host_ip' => '192.168.10.103',
                'ssh_port' => 22,
                'username' => 'serveruser',
                'raw_password' => 'CustomSrv_Secure99!',
                'server_type' => 'Bare Metal',
                'location' => 'Singapore - SG1',
                'provider' => 'Equinix SG',
                'status' => 'Available',
                'notes' => 'Custom high-load bare metal node'
            ]
        ];

        foreach ($sampleServers as $srv) {
            $existing = Database::fetch("SELECT id FROM servers WHERE host_ip = ?", [$srv['host_ip']]);
            if (!$existing) {
                $encPassword = EncryptionService::encrypt($srv['raw_password']);
                $serverId = Database::insert(
                    "INSERT INTO servers (host_ip, ssh_port, username, encrypted_password, server_type, location, provider, status, notes, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
                    [
                        $srv['host_ip'], $srv['ssh_port'], $srv['username'], $encPassword,
                        $srv['server_type'], $srv['location'], $srv['provider'],
                        $srv['status'], $srv['notes']
                    ]
                );

                Database::insert(
                    "INSERT INTO server_credentials (server_id, host_ip, username, encrypted_password, ssh_port, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
                    [$serverId, $srv['host_ip'], $srv['username'], $encPassword, $srv['ssh_port']]
                );
            }
        }
    }
}
