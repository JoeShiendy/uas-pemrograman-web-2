<?php
session_start();

// 1. KONEKSI DATABASE
$host = "localhost"; $user = "root"; $pass = ""; $db = "perpustakaan_db";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) { die("Koneksi Gagal: " . mysqli_connect_error()); }

// 2. LOGIKA SESSION LOGIN & LOGOUT (Sederhana & Pasti Jalan)
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if ($username == 'admin' && $password == 'admin123') {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
    }
    header("Location: index.php"); exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: index.php"); exit();
}

// JIKA BELUM LOGIN, TAMPILKAN FORM LOGIN
if (!isset($_SESSION['logged_in'])) { ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height:100vh;">
    <div class="card p-4 shadow-sm" style="width: 350px;">
        <h4 class="text-center mb-3">Login System</h4>
        <form method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="admin" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="admin123" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Masuk</button>
        </form>
    </div>
</body>
</html>
<?php exit(); }

// 3. LOGIKA CRUD (CREATE, UPDATE, DELETE)
if (isset($_POST['simpan'])) {
    $judul = $_POST['judul']; $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit']; $tahun = $_POST['tahun_terbit']; $kategori = $_POST['kategori'];
    
    if ($_POST['id'] == '') {
        mysqli_query($conn, "INSERT INTO buku VALUES (NULL, '$judul', '$penulis', '$penerbit', '$tahun', '$kategori')");
    } else {
        $id = $_POST['id'];
        mysqli_query($conn, "UPDATE buku SET judul='$judul', penulis='$penulis', penerbit='$penerbit', tahun_terbit='$tahun', kategori='$kategori' WHERE id='$id'");
    }
    header("Location: index.php"); exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM buku WHERE id='$id'");
    header("Location: index.php"); exit();
}

// 4. LOGIKA SEARCHING & PAGINATION
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

$where = $keyword != '' ? "WHERE judul LIKE '%$keyword%' OR penulis LIKE '%$keyword%'" : "";
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM buku $where");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

$data_buku = mysqli_query($conn, "SELECT * FROM buku $where LIMIT $start, $limit");

$edit_data = ['id'=>'', 'judul'=>'', 'penulis'=>'', 'penerbit'=>'', 'tahun_terbit'=>'', 'kategori'=>''];
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $res_edit = mysqli_query($conn, "SELECT * FROM buku WHERE id='$id_edit'");
    $edit_data = mysqli_fetch_assoc($res_edit);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Perpustakaan Buku Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand">Perpustakaan Buku Digital</a>
        <span class="text-white">Halo, <?= $_SESSION['username']; ?> | <a href="?action=logout" class="btn btn-sm btn-danger">Logout</a></span>
    </div>
</nav>

<div class="container">
    <div class="row">
        <!-- FORM INPUT (CREATE & UPDATE) -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white"><?= $edit_data['id'] ? 'Edit Buku' : 'Tambah Buku' ?></div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                        <div class="mb-2"><label>Judul</label><input type="text" name="judul" class="form-control" value="<?= $edit_data['judul'] ?>" required></div>
                        <div class="mb-2"><label>Penulis</label><input type="text" name="penulis" class="form-control" value="<?= $edit_data['penulis'] ?>" required></div>
                        <div class="mb-2"><label>Penerbit</label><input type="text" name="penerbit" class="form-control" value="<?= $edit_data['penerbit'] ?>" required></div>
                        <div class="mb-2"><label>Tahun Terbit</label><input type="number" name="tahun_terbit" class="form-control" value="<?= $edit_data['tahun_terbit'] ?>" required></div>
                        <div class="mb-3"><label>Kategori</label><input type="text" name="kategori" class="form-control" value="<?= $edit_data['kategori'] ?>" required></div>
                        <button type="submit" name="simpan" class="btn btn-success w-100">Simpan Data</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- TABEL DATA, SEARCHING, & PAGINATION -->
        <div class="col-md-8">
            <div class="card shadow-sm p-3">
                <form method="GET" class="d-flex mb-3">
                    <input type="text" name="keyword" class="form-control me-2" placeholder="Cari Judul / Penulis..." value="<?= $keyword ?>">
                    <button type="submit" class="btn btn-secondary">Cari</button>
                </form>

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th><th>Judul</th><th>Penulis</th><th>Penerbit</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = $start + 1; while($row = mysqli_fetch_assoc($data_buku)) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['judul'] ?></td>
                            <td><?= $row['penulis'] ?></td>
                            <td><?= $row['penerbit'] ?></td>
                            <td>
                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-sm btn-danger">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- PAGINATION -->
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php for($i=1; $i<=$total_pages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&keyword=<?= $keyword ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
</body>
</html>