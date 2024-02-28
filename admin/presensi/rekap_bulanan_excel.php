<?php
session_start();
ob_start();
if (!isset($_SESSION["login"])) {
  header("Location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION["role"] != 'admin') {
  header("Location: ../../auth/login.php?pesan=tolak_akses");
}

$judul = 'Rekap Presensi Bulanan';
include_once("../../config.php");
require('../../assets/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$filter_tahun_bulan = $_POST['filter_tahun'] . '-' . $_POST['filter_bulan'];
$result = mysqli_query($connection, "SELECT presensi.*, pegawai.nama, pegawai.lokasi_presensi, pegawai.nip FROM presensi JOIN pegawai ON presensi.id_pegawai= pegawai.id WHERE DATE_FORMAT(tanggal_masuk, '%Y-%m') = '$filter_tahun_bulan' ORDER BY tanggal_masuk DESC");


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'REKAP PRESENSI BULANAN');
$sheet->setCellValue('A2', 'BULAN');
$sheet->setCellValue('A3', 'TAHUN');
$sheet->setCellValue('C2', $_POST['filter_bulan']);
$sheet->setCellValue('C3', $_POST['filter_tahun']);
$sheet->setCellValue('A5', 'NO');
$sheet->setCellValue('B5', 'NAMA');
$sheet->setCellValue('C5', 'NIP');
$sheet->setCellValue('D5', 'TANGGAL MASUK');
$sheet->setCellValue('E5', 'JAM MASUK');
$sheet->setCellValue('F5', 'TANGGAL KELUAR');
$sheet->setCellValue('G5', 'JAM KELUAR');
$sheet->setCellValue('H5', 'TOTAL JAM KERJA');
$sheet->setCellValue('I5', 'TOTAL JAM TERLAMBAT');

$sheet->mergeCells('A1:F1');
$sheet->mergeCells('A2:B2');
$sheet->mergeCells('A3:B3');

$no = 1;
$row = 6;

while ($data = mysqli_fetch_array($result)) {

  // Menghitung total jam kerja
  $jam_tanggal_masuk = date('Y-m-d H:i:s', strtotime($data['tanggal_masuk'] . ' ' . $data['jam_masuk']));
  $jam_tanggal_keluar = date('Y-m-d H:i:s', strtotime($data['tanggal_keluar'] . ' ' . $data['jam_keluar']));

  $timestamp_masuk = strtotime($jam_tanggal_masuk);
  $timestamp_keluar = strtotime($jam_tanggal_keluar);

  $selisih = $timestamp_keluar - $timestamp_masuk;

  $total_jam_kerja = floor($selisih / 3600);
  $selisih -= $total_jam_kerja * 3600;
  $selisih_menit_kerja = floor($selisih / 60);

  // Menghitun total keterlambatan
  $lokasi_presensi = $data['lokasi_presensi'];
  $lokasi = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE nama_lokasi = '$lokasi_presensi'");

  while ($lokasi_result = mysqli_fetch_array($lokasi)) :
    $jam_masuk_kantor = date('H:i:s', strtotime($lokasi_result['jam_masuk']));
  endwhile;

  $jam_masuk = date('H:i:s', strtotime($data['jam_masuk']));
  $timestamp_jam_masuk_real = strtotime($jam_masuk);
  $timestamp_jam_masuk_kantor = strtotime($jam_masuk_kantor);

  $terlambat = $timestamp_jam_masuk_real - $timestamp_jam_masuk_kantor;
  $total_jam_terlambat = floor($terlambat / 3600);
  $terlambat -= $total_jam_terlambat * 3600;
  $selisih_menit_terlambat = floor($terlambat / 60);


  $sheet->setCellValue('A' . $row, $no);
  $sheet->setCellValue('B' . $row, $data['nama']);
  $sheet->setCellValue('C' . $row, $data['nip']);
  $sheet->setCellValue('D' . $row, $data['tanggal_masuk']);
  $sheet->setCellValue('E' . $row, $data['jam_masuk']);
  $sheet->setCellValue('F' . $row, $data['tanggal_keluar']);
  $sheet->setCellValue('G' . $row, $data['jam_keluar']);
  $sheet->setCellValue('H' . $row, $total_jam_kerja . ' Jam ' . $selisih_menit_kerja . ' Menit');
  $sheet->setCellValue('I' . $row, $total_jam_terlambat . ' Jam ' . $selisih_menit_terlambat . ' Menit');

  $no++;
  $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Rekap_Presensi_Bulanan.xlsx"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
