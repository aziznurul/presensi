<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] != 'admin') {
  header("Location: ../../auth/login.php?pesan=tolak_akses");
}


$judul = "Data Ketidakhadiran";

include('../layout/header.php');
require_once('../../config.php');

$result = mysqli_query($connection, "SELECT * FROM ketidakhadiran ORDER BY id DESC");
?>

<!-- Page body -->
<div class="page-body">
  <div class="container-xl ">
    <table class="table table-bordered mt-2">
      <tr class="text-center">
        <th>No.</th>
        <th>Tanggal</th>
        <th>Keterangan</th>
        <th>Deskripsi</th>
        <th>File</th>
        <th>Status Pengajuan</th>
      </tr>

      <?php if (mysqli_num_rows($result) === 0) { ?>
        <tr>
          <td colspan="7">Data Ketidakhadiran Masih Kosong</td>
        </tr>
      <?php } else { ?>
        <?php $no = 1;
        while ($data = mysqli_fetch_array($result)) : ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= date('d F Y', strtotime($data['tanggal'])) ?></td>
            <td><?= $data['keterangan'] ?></td>
            <td><?= $data['deskripsi'] ?></td>
            <td class="text-center">
              <a target="_blank" href="<?= base_url('assets/file_ketidakhadiran/' . $data['file']) ?>" class="badge badge-pill bg-primary">Download</a>
            </td>
            <td class="text-center">
              <?php if ($data['status_pengajuan'] == 'PENDING') : ?>
                <a class="badge badge-pill bg-warning" href="<?= base_url('admin/ketidakhadiran/detail.php?id=' . $data['id']) ?>">PENDING</a>
              <?php elseif ($data['status_pengajuan'] == 'REJECTED') : ?>
                <a class="badge badge-pill bg-danger" href="<?= base_url('admin/ketidakhadiran/detail.php?id=' . $data['id']) ?>">REJECTED</a>
              <?php else : ?>
                <a class="badge badge-pill bg-success" href="<?= base_url('admin/ketidakhadiran/detail.php?id=' . $data['id']) ?>">APPOVED</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php } ?>

    </table>
  </div>
</div>

<?php include('../layout/footer.php'); ?>