<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-sans bg-white text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white text-gray-800 px-6 py-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="text-2xl font-bold">POSitif</a>
            <ul class="flex space-x-6 text-lg">
                <li><a href="{{ route('login') }}" class="text-gray-800">Masuk</a></li>
                <li><a href="{{ route('register') }}" class="bg-teal-600 text-white px-6 py-2 rounded-full">Daftar</a></li>
            </ul>
        </div>
    </nav>

    <!-- Register Section -->
    <section class="max-w-lg mx-auto mt-16 p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-semibold mb-6 text-center text-gray-800">Daftar di POSitif</h2>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-4 flex space-x-4">
    <div class="w-full">
        <label for="name" class="block text-gray-800 text-lg">Nama</label>
        <input type="text" id="name" name="name" class="w-full mt-2 px-4 py-2 text-gray-800 border border-gray-300 rounded-lg" required>
    </div>
    <div class="w-full">
        <label for="email" class="block text-gray-800 text-lg">Email</label>
        <input type="email" id="email" name="email" class="w-full mt-2 px-4 py-2 text-gray-800 border border-gray-300 rounded-lg" required>
    </div>
</div>

            <div class="mb-4 flex space-x-4">
    <div class="w-full">
        <label for="password" class="block text-gray-800 text-lg">Password</label>
        <input type="password" id="password" name="password" class="w-full mt-2 px-4 py-2 text-gray-800 border border-gray-300 rounded-lg" required>
    </div>
    <div class="w-full">
        <label for="password_confirmation" class="block text-gray-800 text-lg">Konfirmasi Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="w-full mt-2 px-4 py-2 text-gray-800 border border-gray-300 rounded-lg" required>
    </div>
</div>


            <div class="mb-4">
                <label for="role" class="block text-gray-800 text-lg">Role</label>
                <select id="role" name="role" class="w-full mt-2 px-4 py-2 text-gray-800 border border-gray-300 rounded-lg" required>
                    <option value="admin" class="text-gray-800">Admin</option>
                    <option value="kasir" class="text-gray-800">Kasir</option>
                    <option value="pemilik" class="text-gray-800">Pemilik</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-teal-600 text-white py-3 rounded-lg">Daftar</button>
        </form>
    </section>

    <!-- Footer -->
    <footer class="bg-white text-gray-800 py-6 text-center mt-16 border-t">
        <p>&copy; 2025 POSitif. Semua hak dilindungi.</p>
    </footer>

</body>

</html>
