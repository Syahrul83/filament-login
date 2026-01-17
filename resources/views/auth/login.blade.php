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
