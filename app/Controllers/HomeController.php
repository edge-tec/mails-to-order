<?php

namespace App\Controllers;

use App\Models\Package;

class HomeController {
    public function index() {
        $packages = Package::getAllActive();
        view('home.index', [
            'title' => 'Server Ordering & Provisioning Portal',
            'packages' => $packages
        ]);
    }

    public function packages() {
        $packages = Package::getAllActive();
        view('home.packages', [
            'title' => 'Server Packages & Pricing',
            'packages' => $packages
        ]);
    }

    public function contact() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            flash('success', 'Thank you for your message! Our team will get back to you shortly.');
            redirect('/contact');
        }
        view('home.contact', ['title' => 'Contact Support']);
    }

    public function terms() {
        view('home.terms', ['title' => 'Terms of Service & Privacy Policy']);
    }
}
