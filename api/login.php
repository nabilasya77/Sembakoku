<?php

if (isset($_COOKIE['login']) && $_COOKIE['login'] === 'true') {
    header('Location: /api/dashboard.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login SembakoKu</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">

<h1 class="text-center text-3xl font-bold mb-6">
Sembako<span class="text-orange-500">Ku</span>
</h1>

<?php if(isset($_GET['pesan'])): ?>
<div class="mb-4 p-3 bg-red-100 text-red-600 rounded-lg">
Username atau Password salah
</div>
<?php endif; ?>

<form action="/api/Proses/prosesLogin.php" method="POST">

<div class="mb-4">
<label>Username</label>
<input
type="text"
name="username"
required
class="w-full border p-3 rounded-lg">
</div>

<div class="mb-4">
<label>Password</label>
<input
type="password"
name="password"
required
class="w-full border p-3 rounded-lg">
</div>

<button
type="submit"
class="w-full bg-orange-500 text-white py-3 rounded-lg">

Login

</button>

</form>

</div>

</body>
</html>