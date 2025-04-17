<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans bg-white text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white text-gray-800 px-6 py-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="text-2xl font-bold">POSITIF</a>
            <ul class="flex space-x-6 text-lg">
                <li><a href="{{route('login')}}" class="text-gray-800">Masuk</a></li>
                <li><a href="{{route('register')}}" class="bg-teal-600 text-white px-6 py-2 rounded-full">Daftar</a></li>
            </ul>
        </div>
    </nav>

    <!-- Login Section -->
    <section class="max-w-lg mx-auto mt-16 p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-semibold mb-6 text-center text-gray-800">Masuk ke POSITIF</h2>

        <!-- Display Error Messages -->
        @if ($errors->any())
            <div class="bg-red-500 text-white p-4 mb-4 rounded-md">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{route('login')}}" method="POST">
            @csrf
            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-lg text-gray-800">Email</label>
                <input type="email" id="email" name="email" class="w-full mt-2 px-4 py-2 text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" required>
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-lg text-gray-800">Password</label>
                <input type="password" id="password" name="password" class="w-full mt-2 px-4 py-2 text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" required>
            </div>

            <button type="submit" class="w-full bg-teal-600 text-white py-3 rounded-lg text-lg font-semibold hover:bg-teal-700">Masuk</button>

            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">Belum punya akun? <a href="{{route('register')}}" class="text-teal-600">Daftar sekarang</a></p>
            </div>
        </form>
    </section>

    <!-- Footer -->
    <footer class="bg-white text-gray-800 py-6 text-center mt-16 border-t">
        <p>&copy;2025 POSITIF.  All rights reserved.</p>
    </footer>

</body>
</html>
