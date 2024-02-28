<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] != 'admin') {
  header("Location: ../../auth/login.php?pesan=tolak_akses");
}


$judul = "Detail Ketidakhadiran";

include('../layout/header.php');
require_once('../../config.php');

if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $status_pengajuan = $_POST['status_pengajuan'];

  $result = mysqli_query($connection, "UPDATE ketidakhadiran SET status_pengajuan = '$status_pengajuan' WHERE id=$id");

  $_SESSION['berhasil'] = "Status Pengajuan berhasil diupdate";
  header("Location: ketidakhadiran.php");
  exit();
}


$id = $_GET['id'];
$result = mysqli_query($connection, "SELECT * FROM ketidakhadiran WHERE id=$id");

$result = mysqli_query($connection, "SELECT * FROM ketidakhadiran WHERE id=$id");

while ($data = mysqli_fetch_array($result)) {
  $keterangan = $data['keterangan'];
  $tanggal = $data['tanggal'];
  $status_pengajuan = $data['status_pengajuan'];
}

?>

<!-- Page body -->
<div class="page-body">
  <div class="container-xl ">
    <div class="card col-md-6">
      <div class="card-body">
        <form action="" method="POST">
          <div class="mb-3">
            <label for="tanggal">Tanggal</label>
            <input type="date" class="form-control" name="tanggal" value="<?= $tanggal; ?>" readonly>
          </div>
          <div class="mb-3">
            <label for="keterangan">Keterangan</label>
            <input type="text" class="form-control" name="keterangan" value="<?= $keterangan; ?>" readonly>
          </div>
          <div class="mb-3">
            <label for="status_pengajuan">Status Pengajuan</label>
            <select name="status_pengajuan" id="status_pengajuan" class="form-control">
              <option value="">--PILIH STATUS--</option>
              <option <?php if ($status_pengajuan == 'PENDING') {
                        echo 'selected';
                      } ?> value="PENDING">PENDING</option>
              <option <?php if ($status_pengajuan == 'REJECTED') {
                        echo 'selected';
                      } ?> value="REJECTED">REJECTED</option>
              <option <?php if ($status_pengajuan == 'APPROVED') {
                        echo 'selected';
                      } ?> value="APPROVED">APPROVED</option>
            </select>
          </div>

          <input type="hidden" name="id" value="<?= $id ?>">
          <button type="submit" name="update" class="btn btn-primary">UPDATE</button>
        </form>

      </div>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>