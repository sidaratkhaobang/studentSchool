<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create
        {--username= : ชื่อผู้ใช้สำหรับเข้าสู่ระบบ}
        {--email= : อีเมลของผู้ดูแลระบบ}
        {--password= : รหัสผ่าน (หากไม่ระบุจะถูกถามและซ่อนการพิมพ์)}';

    protected $description = 'สร้างบัญชีผู้ดูแลระบบ (admin) สำหรับเข้าจัดการระบบทั้งหมด';

    public function handle(): int
    {
        $username = $this->option('username') ?: $this->ask('Username');
        $email = $this->option('email') ?: $this->ask('Email');
        $password = $this->option('password') ?: $this->secret('Password');

        $validator = Validator::make(
            ['username' => $username, 'email' => $email, 'password' => $password],
            [
                'username' => ['required', 'string', 'max:50', 'unique:users,username'],
                'email' => ['required', 'email', 'max:100', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->info("สร้างผู้ดูแลระบบเรียบร้อยแล้ว: {$user->username} ({$user->email})");

        return self::SUCCESS;
    }
}
