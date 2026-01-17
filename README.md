## KONSEP DASAR (WAJIB PAHAM)

Filament **tidak memaksa** pakai auth bawaannya.
Yang penting bagi Filament hanyalah:

✅ Ada **user yang sudah login (Laravel auth)**
✅ Middleware `auth` + `canAccessPanel()` terpenuhi

Jadi kita:

* **Login pakai Laravel biasa**
* **Filament cuma dipakai sebagai dashboard**

---

## ARSITEKTUR FULL MANUAL

```
/login        -> LOGIN LARAVEL (BUAT SENDIRI)
POST /login   -> AUTH::attempt()
/admin        -> FILAMENT PANEL (CEK auth Laravel)
```

---

## 1️⃣ NONAKTIFKAN AUTH FILAMENT

Di:

```php
app/Providers/Filament/AdminPanelProvider.php
```

❌ HAPUS:

```php
->login()
```

✔️ Jadinya:

```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->path('admin')
        ->authGuard('web');
}
```

📌 Artinya:

* Filament **tidak punya halaman login**
* Dia hanya cek `Auth::check()`

---

## 2️⃣ BUAT LOGIN ROUTE LARAVEL MANUAL

### routes/web.php

```php
use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
```

---

## 3️⃣ BUAT AuthController

```bash
php artisan make:controller AuthController
```

### app/Http/Controllers/AuthController.php

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/admin');
        }

        return back()->withErrors([
            'email' => 'Login gagal',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
```

---

## 4️⃣ BUAT VIEW LOGIN SENDIRI (BEBAS TOTAL)

### resources/views/auth/login.blade.php

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<form method="POST" action="/login"
      class="bg-white p-6 rounded shadow w-96">
    @csrf

    <h2 class="text-xl font-bold mb-4 text-center">
        Login Sistem
    </h2>

    <input name="email" type="email"
           class="w-full border p-2 mb-3"
           placeholder="Email">

    <input name="password" type="password"
           class="w-full border p-2 mb-4"
           placeholder="Password">

    <button class="w-full bg-blue-600 text-white py-2 rounded">
        Login
    </button>

    @error('email')
        <p class="text-red-500 mt-2">{{ $message }}</p>
    @enderror
</form>

</body>
</html>
```

👉 **Bisa kamu ganti jadi**:

* Login NIP
* Login username
* Login no HP
* OTP / CAPTCHA
* SSO

---

## 5️⃣ HUBUNGKAN KE FILAMENT (WAJIB!)

Di model `User.php`:

```php
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // atau role-based
    }
}
```

### Contoh role-based:

```php
return $this->role === 'admin';
```

---

## 6️⃣ PROTEKSI OTOMATIS FILAMENT

Jika user **belum login**, Filament otomatis:
➡️ redirect ke route `login`

Pastikan route ini ada:

```php
Route::get('/login')->name('login');
```

---

## 7️⃣ LOGOUT DARI FILAMENT

Buat tombol logout sendiri:

```blade
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button>Logout</button>
</form>
```

---

## KELEBIHAN FULL MANUAL

✅ UI login **bebas total**
✅ Bisa integrasi:

* SSO
* LDAP
* OAuth
* OTP SMS
* CAPTCHA
* NIP / Pegawai

---

## KEKURANGAN (PENTING)

❌ Tidak dapat:

* Rate limit login bawaan Filament
* UI auth siap pakai
* Update Filament auth otomatis

⚠️ Kamu **wajib**:

* Amankan brute force
* Validasi session
* CSRF
* Role check

---

## REKOMENDASI SAYA (JUJUR)

| Kebutuhan       | Saran                         |
| --------------- | ----------------------------- |
| Admin internal  | Auth Filament (override view) |
| Login ASN / NIP | **FULL MANUAL (INI)**         |
| SSO / SIMPEG    | **FULL MANUAL**               |
| Aplikasi publik | Laravel auth                  |

---


