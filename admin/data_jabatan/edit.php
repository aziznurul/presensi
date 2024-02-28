<?php
session_start();
ob_start();

if (!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] != 'admin') {
  header("Location: ../../auth/login.php?pesan=tolak_akses");
}


$judul = "Edit Data Jabatan";

include('../layout/header.php');
require_once('../../config.php');

if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $jabatan = htmlspecialchars($_POST['jabatan']);

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($jabatan)) {
      $pesan_kesalahan = "Nama Jabatan wajib diisi";
    }
    if (!empty($pesan_kesalahan)) {
      $_SESSION['validasi'] = $pesan_kesalahan;
    } else {
      $result = mysqli_query($connection, "UPDATE jabatan SET jabatan='$jabatan' WHERE id=$id");
      $_SESSION['berhasil'] = "Data Berhasil diupdate";
      header("Location: jabatan.php");
      exit();
    }
  }
}

//$id = $_GET['id'];
$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
$result = mysqli_query($connection, "SELECT * FROM jabatan WHERE id=$id");

while ($jabatan = mysqli_fetch_array($result)) {
  $nama_jabatan = $jabatan['jabatan'];
}

?>
<!-- Page body -->
<div class="page-body">
  <div class="container-xl ">
    <div class="card col-md-6">
      <div class="card-body">
        <form action="<?= base_url('admin/data_jabatan/edit.php'); ?>" method="POST">
          <div class="mb-3">
            <label for="jabatan">Nama Jabatan</label>
            <input type="text" name="jabatan" id="jabatan" class="form-control" value="<?= $nama_jabatan; ?>">
          </div>
          <input type="hidden" value="<?= $id ?>" name="id">
          <button type="submit" name="update" class="btn btn-primary ">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include('../layout/footer.php'); ?>