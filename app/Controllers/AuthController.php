<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            $userModel = new UserModel();

            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $user = $userModel->where('username', $username)
                ->orWhere('email', $username)
                ->where('is_active', 1)
                ->first();

            if ($user && $userModel->verifyPassword($password, $user['password'])) {
                $session = session();
                $session->set([
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role'],
                    'is_logged_in' => true
                ]);

                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        return redirect()->to('/admin/dashboard');
                    case 'teacher':
                        return redirect()->to('/teacher/dashboard');
                    case 'student':
                        return redirect()->to('/student/dashboard');
                    default:
                        return redirect()->to('/');
                }
            } else {
                session()->setFlashdata('error', 'Username atau password salah!');
                return redirect()->back()->withInput();
            }
        }

        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout.');
    }

    public function register()
    {
        if ($this->request->getMethod() === 'POST') {
            $userModel = new UserModel();

            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'full_name' => $this->request->getPost('full_name'),
                'role' => 'student' // Default role
            ];

            if ($userModel->insert($data)) {
                session()->setFlashdata('success', 'Registrasi berhasil! Silakan login.');
                return redirect()->to('/login');
            } else {
                session()->setFlashdata('error', 'Registrasi gagal! ' . implode(', ', $userModel->errors()));
                return redirect()->back()->withInput();
            }
        }

        return view('auth/register');
    }
}
