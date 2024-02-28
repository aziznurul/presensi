<?php
session_start();



if (!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] != 'admin') {
  header("Location: ../../auth/login.php?pesan=tolak_akses");
}


$judul = "Data Lokasi Presensi";

include('../layout/header.php');
require_once('../../config.php');

$result = mysqli_query($connection, "SELECT * FROM lokasi_presensi ORDER BY id DESC");

?>



<!-- Page body -->
<div class="page-body">
  <div class="container-xl ">
    <a href="<?= base_url('admin/data_lokasi_presensi/tambah.php'); ?>" class="btn btn-primary"><span class="text"><i class="fa-solid fa-circle-plus"></i> Tambah Data</span></a>
    <table class="table table-bordered mt-3">
      <tr class="text-center">
        <th>No</th>
        <th>Nama Lokasi</th>
        <th>Alamat Lokasi</th>
        <th>Tipe Lokasi</th>
        <th>Latitude/Longitude</th>
        <th>Radius</th>
        <th>Aksi</th>
      </tr>

      <?php if (mysqli_num_rows($result) === 0) { ?>
        <tr>
          <td colspan="7">Data kosong, silahkan tambahkan dara baru
        </tr>
      <?php } else { ?>
        <?php $no = 1;
        while ($lokasi = mysqli_fetch_array($result)) : ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= $lokasi['nama_lokasi']; ?></td>
            <td><?= $lokasi['alamat_lokasi']; ?></td>
            <td><?= $lokasi['tipe_lokasi']; ?></td>
            <td><?= $lokasi['latitude'] . '/' . $lokasi['longitude'] ?></td>
            <td><?= $lokasi['radius']; ?></td>
            <td>
              <a href="<?= base_url('admin/data_lokasi_presensi/detail.php?id=' . $lokasi['id']); ?>" class="badge badge-pill bg-primary">Detail</a>
              <a href="<?= base_url('admin/data_lokasi_presensi/edit.php?id=' . $lokasi['id']); ?>" class="badge badge-pill bg-success">Edit</a>
              <a href="<?= base_url('admin/data_lokasi_presensi/hapus.php?id=' . $lokasi['id']); ?>" class="badge badge-pill bg-danger tombol-hapus">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php } ?>
    </table>

  </div>
</div>

<?php include('../layout/footer.php'); ?>