<?php
namespace App\Models\Bpjskes\Vclaim\V2;

class Params
{
    const BULAN=[1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    const JENIS_LAYANAN=[1=>'Rawat Inap',2=>'Rawat Jalan'];
    const TUJUAN_KUNJUNGAN=[0=>'Normal',1=>'Prosedur',2=>'Konsul Dokter'];
    const FLAG_PROSEDUR=[0=>'Prosedur Tidak Berkelanjutan',1=>'Prosedur dan Terapi Berkelanjutan'];
    const PEMBIAYAAN=[1=>'Pribadi',2=>'Pemberi Kerja',3=>'Asuransi Kesehatan Tambahan'];
    const PENUNJANG=[1=>'Radioterapi',2=>'Kemoterapi',3=>'Rehabilitasi Medik',4=>'Rehabilitasi Psikososial',5=>'Transfusi Darah',6=>'Pelayanan Gigi',7=>'Laboratorium',8=>'USG',9=>'Farmasi',10=>'Lain-Lain',11=>'MRI',12=>'HEMODIALISA'];
    const ASESMEN_PELAYANAN=[1=>'Poli spesialis tidak tersedia pada hari sebelumnya',2=>'Jam Poli telah berakhir pada hari sebelumnya',3=>'Dokter Spesialis yang dimaksud tidak praktek pada hari sebelumnya',4=>'Atas Instruksi RS',5=>'Tujuan Kontrol'];
    const KELAS=[
        ['id'=>'1!#!KELAS 1','nama'=>'KELAS 1'],
        ['id'=>'2!#!KELAS 2','nama'=>'KELAS 2'],
        ['id'=>'3!#!KELAS 3','nama'=>'KELAS 3']
    ];
    const KELAS_NAIK=[
        ['id'=>'1!#!VVIP','nama'=>'VVIP'],
        ['id'=>'2!#!VIP','nama'=>'VIP'],
        ['id'=>'3!#!KELAS 1','nama'=>'KELAS 1'],
        ['id'=>'4!#!KELAS 2','nama'=>'KELAS 2'],
        ['id'=>'5!#!KELAS 3','nama'=>'KELAS 3'],
        ['id'=>'6!#!ICCU','nama'=>'ICCU'],
        ['id'=>'7!#!ICU','nama'=>'ICU'],
    ];
    //LAKA LANTAS
    const STATUS_LAKA_LANTAS=[0=>'Bukan Kecelakaan lalu lintas [BKLL]',1=>'KLL dan bukan kecelakaan Kerja [BKK]',2=>'KLL dan KK',3=>'KK'];
    const STATUS_PULANG_RAWATINAP=[1=>'Atas Persetujuan Dokter',3=>'Atas Permintaan Sendiri',4=>'Meninggal',5=>'Lain-lain'];
}