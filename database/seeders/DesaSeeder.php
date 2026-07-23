<?php
// database/seeders/DesaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DesaSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('desas')->truncate();
        Schema::enableForeignKeyConstraints();

        $desa = [
            // Kecamatan Gabuswetan (ID: 1)
            ['nama_desa' => 'Kroyasuka', 'kode_desa' => '321217001', 'kecamatan_id' => 1],
            ['nama_desa' => 'Sumberjaya', 'kode_desa' => '321217002', 'kecamatan_id' => 1],
            ['nama_desa' => 'Kedungdawa', 'kode_desa' => '321217003', 'kecamatan_id' => 1],
            ['nama_desa' => 'Babakanjaya', 'kode_desa' => '321217004', 'kecamatan_id' => 1],
            ['nama_desa' => 'Gabus Kulon', 'kode_desa' => '321217005', 'kecamatan_id' => 1],
            ['nama_desa' => 'Sekarmulya', 'kode_desa' => '321217006', 'kecamatan_id' => 1],
            ['nama_desa' => 'Kedokan Gabus', 'kode_desa' => '321217007', 'kecamatan_id' => 1],
            ['nama_desa' => 'Rancamulya', 'kode_desa' => '321217008', 'kecamatan_id' => 1],
            ['nama_desa' => 'Rancahan', 'kode_desa' => '321217009', 'kecamatan_id' => 1],
            ['nama_desa' => 'Gabuswetan', 'kode_desa' => '321217010', 'kecamatan_id' => 1],
            ['nama_desa' => 'Drunten Wetan', 'kode_desa' => '321217011', 'kecamatan_id' => 1],
            ['nama_desa' => 'Drunten Kulon', 'kode_desa' => '321217012', 'kecamatan_id' => 1],

            // Kecamatan Cikedung (ID: 2)
            ['nama_desa' => 'Loyang', 'kode_desa' => '321213001', 'kecamatan_id' => 2],
            ['nama_desa' => 'Amis', 'kode_desa' => '321213002', 'kecamatan_id' => 2],
            ['nama_desa' => 'Jatisura', 'kode_desa' => '321213003', 'kecamatan_id' => 2],
            ['nama_desa' => 'Jambak', 'kode_desa' => '321213004', 'kecamatan_id' => 2],
            ['nama_desa' => 'Cikedung', 'kode_desa' => '321213005', 'kecamatan_id' => 2],
            ['nama_desa' => 'Cikedung Lor', 'kode_desa' => '321213006', 'kecamatan_id' => 2],
            ['nama_desa' => 'Mundakjaya', 'kode_desa' => '321213007', 'kecamatan_id' => 2],

            // Kecamatan Lelea (ID: 3)
            ['nama_desa' => 'Tunggulpayung', 'kode_desa' => '321205001', 'kecamatan_id' => 3],
            ['nama_desa' => 'Tugu', 'kode_desa' => '321205002', 'kecamatan_id' => 3],
            ['nama_desa' => 'Nunuk', 'kode_desa' => '321205003', 'kecamatan_id' => 3],
            ['nama_desa' => 'Tempel', 'kode_desa' => '321205004', 'kecamatan_id' => 3],
            ['nama_desa' => 'Panguban', 'kode_desa' => '321205005', 'kecamatan_id' => 3],
            ['nama_desa' => 'Telagasari', 'kode_desa' => '321205006', 'kecamatan_id' => 3],
            ['nama_desa' => 'Langgeng Sari', 'kode_desa' => '321205007', 'kecamatan_id' => 3],
            ['nama_desa' => 'Tamansari', 'kode_desa' => '321205008', 'kecamatan_id' => 3],
            ['nama_desa' => 'Lelea', 'kode_desa' => '321205009', 'kecamatan_id' => 3],
            ['nama_desa' => 'Cempeh', 'kode_desa' => '321205010', 'kecamatan_id' => 3],
            ['nama_desa' => 'Tempel Kulon', 'kode_desa' => '321205011', 'kecamatan_id' => 3],

            // Kecamatan Bangodua (ID: 4)
            ['nama_desa' => 'Mulyasari', 'kode_desa' => '321206001', 'kecamatan_id' => 4],
            ['nama_desa' => 'Bangodua', 'kode_desa' => '321206002', 'kecamatan_id' => 4],
            ['nama_desa' => 'Bedjyut', 'kode_desa' => '321206003', 'kecamatan_id' => 4],
            ['nama_desa' => 'Rancasari', 'kode_desa' => '321206004', 'kecamatan_id' => 4],
            ['nama_desa' => 'Wanasari', 'kode_desa' => '321206005', 'kecamatan_id' => 4],
            ['nama_desa' => 'Karanggetas', 'kode_desa' => '321206006', 'kecamatan_id' => 4],
            ['nama_desa' => 'Tegal Girang', 'kode_desa' => '321206007', 'kecamatan_id' => 4],
            ['nama_desa' => 'Malangsari', 'kode_desa' => '321206008', 'kecamatan_id' => 4],

            // Kecamatan Widasari (ID: 5)
            ['nama_desa' => 'Bangkaloa Ilir', 'kode_desa' => '321207001', 'kecamatan_id' => 5],
            ['nama_desa' => 'Widasari', 'kode_desa' => '321207002', 'kecamatan_id' => 5],
            ['nama_desa' => 'Kalensari', 'kode_desa' => '321207003', 'kecamatan_id' => 5],
            ['nama_desa' => 'Bunder', 'kode_desa' => '321207004', 'kecamatan_id' => 5],
            ['nama_desa' => 'Ujungaris', 'kode_desa' => '321207005', 'kecamatan_id' => 5],
            ['nama_desa' => 'Kongsijaya', 'kode_desa' => '321207006', 'kecamatan_id' => 5],
            ['nama_desa' => 'Ujungijaya', 'kode_desa' => '321207007', 'kecamatan_id' => 5],
            ['nama_desa' => 'Ujungpondok Jaya', 'kode_desa' => '321207008', 'kecamatan_id' => 5],
            ['nama_desa' => 'Leuwiagede', 'kode_desa' => '321207009', 'kecamatan_id' => 5],
            ['nama_desa' => 'Kasmaran', 'kode_desa' => '321207010', 'kecamatan_id' => 5],

            // Kecamatan Kertasemaya (ID: 6)
            ['nama_desa' => 'Tulungagung', 'kode_desa' => '321204001', 'kecamatan_id' => 6],
            ['nama_desa' => 'Jengkok', 'kode_desa' => '321204002', 'kecamatan_id' => 6],
            ['nama_desa' => 'Tegal Wirangrong', 'kode_desa' => '321204003', 'kecamatan_id' => 6],
            ['nama_desa' => 'Manguntara', 'kode_desa' => '321204004', 'kecamatan_id' => 6],
            ['nama_desa' => 'Jambe', 'kode_desa' => '321204005', 'kecamatan_id' => 6],
            ['nama_desa' => 'Lemahayu', 'kode_desa' => '321204006', 'kecamatan_id' => 6],
            ['nama_desa' => 'Tenajar Kidul', 'kode_desa' => '321204007', 'kecamatan_id' => 6],
            ['nama_desa' => 'Kertasemaya', 'kode_desa' => '321204008', 'kecamatan_id' => 6],
            ['nama_desa' => 'Kliwed', 'kode_desa' => '321204009', 'kecamatan_id' => 6],
            ['nama_desa' => 'Tenajar', 'kode_desa' => '321204010', 'kecamatan_id' => 6],
            ['nama_desa' => 'Larangan Jambe', 'kode_desa' => '321204011', 'kecamatan_id' => 6],
            ['nama_desa' => 'Tenajar Lor', 'kode_desa' => '321204012', 'kecamatan_id' => 6],
            ['nama_desa' => 'Sukawera', 'kode_desa' => '321204013', 'kecamatan_id' => 6],

            // Kecamatan Krangkeng (ID: 7)
            ['nama_desa' => 'Purwajaya', 'kode_desa' => '321208001', 'kecamatan_id' => 7],
            ['nama_desa' => 'Kapringan', 'kode_desa' => '321208002', 'kecamatan_id' => 7],
            ['nama_desa' => 'Singakerta', 'kode_desa' => '321208003', 'kecamatan_id' => 7],
            ['nama_desa' => 'Dukuhjati', 'kode_desa' => '321208004', 'kecamatan_id' => 7],
            ['nama_desa' => 'Tegalmulya', 'kode_desa' => '321208005', 'kecamatan_id' => 7],
            ['nama_desa' => 'Kedungwungu', 'kode_desa' => '321208006', 'kecamatan_id' => 7],
            ['nama_desa' => 'Srengseng', 'kode_desa' => '321208007', 'kecamatan_id' => 7],
            ['nama_desa' => 'Luwunggesik', 'kode_desa' => '321208008', 'kecamatan_id' => 7],
            ['nama_desa' => 'Kalianyar', 'kode_desa' => '321208009', 'kecamatan_id' => 7],
            ['nama_desa' => 'Krangkeng', 'kode_desa' => '321208010', 'kecamatan_id' => 7],
            ['nama_desa' => 'Tanjakan', 'kode_desa' => '321208011', 'kecamatan_id' => 7],

            // Kecamatan Karangampel (ID: 8)
            ['nama_desa' => 'Tanjungpura', 'kode_desa' => '321218001', 'kecamatan_id' => 8],
            ['nama_desa' => 'Tanjungsari', 'kode_desa' => '321218002', 'kecamatan_id' => 8],
            ['nama_desa' => 'Pringgacala', 'kode_desa' => '321218003', 'kecamatan_id' => 8],
            ['nama_desa' => 'Benda', 'kode_desa' => '321218004', 'kecamatan_id' => 8],
            ['nama_desa' => 'Sendang', 'kode_desa' => '321218005', 'kecamatan_id' => 8],
            ['nama_desa' => 'Karangampel Kidul', 'kode_desa' => '321218006', 'kecamatan_id' => 8],
            ['nama_desa' => 'Karangampel', 'kode_desa' => '321218007', 'kecamatan_id' => 8],
            ['nama_desa' => 'Dukuh Jeruk', 'kode_desa' => '321218008', 'kecamatan_id' => 8],
            ['nama_desa' => 'Dukuh Tengah', 'kode_desa' => '321218009', 'kecamatan_id' => 8],
            ['nama_desa' => 'Mundu', 'kode_desa' => '321218010', 'kecamatan_id' => 8],
            ['nama_desa' => 'Kaplongan Lor', 'kode_desa' => '321218011', 'kecamatan_id' => 8],

            // Kecamatan Juntinyuat (ID: 9)
            ['nama_desa' => 'Segeran Kidul', 'kode_desa' => '321230001', 'kecamatan_id' => 9],
            ['nama_desa' => 'Segeran Lor', 'kode_desa' => '321230002', 'kecamatan_id' => 9],
            ['nama_desa' => 'Juntiweden', 'kode_desa' => '321230003', 'kecamatan_id' => 9],
            ['nama_desa' => 'Dadap', 'kode_desa' => '321230004', 'kecamatan_id' => 9],
            ['nama_desa' => 'Juntinyuat', 'kode_desa' => '321230005', 'kecamatan_id' => 9],
            ['nama_desa' => 'Juntikedokan', 'kode_desa' => '321230006', 'kecamatan_id' => 9],
            ['nama_desa' => 'Pondoh', 'kode_desa' => '321230007', 'kecamatan_id' => 9],
            ['nama_desa' => 'Sambimaya', 'kode_desa' => '321230008', 'kecamatan_id' => 9],
            ['nama_desa' => 'Tinumpuk', 'kode_desa' => '321230009', 'kecamatan_id' => 9],
            ['nama_desa' => 'Juntikebon', 'kode_desa' => '321230010', 'kecamatan_id' => 9],
            ['nama_desa' => 'Lombang', 'kode_desa' => '321230011', 'kecamatan_id' => 9],
            ['nama_desa' => 'Limbangan', 'kode_desa' => '321230012', 'kecamatan_id' => 9],

            // Kecamatan Sliyeg (ID: 10)
            ['nama_desa' => 'Sleman', 'kode_desa' => '321211001', 'kecamatan_id' => 10],
            ['nama_desa' => 'Tambi', 'kode_desa' => '321211002', 'kecamatan_id' => 10],
            ['nama_desa' => 'Sudikampiran', 'kode_desa' => '321211003', 'kecamatan_id' => 10],
            ['nama_desa' => 'Tambi Lor', 'kode_desa' => '321211004', 'kecamatan_id' => 10],
            ['nama_desa' => 'Sleman Lor', 'kode_desa' => '321211005', 'kecamatan_id' => 10],
            ['nama_desa' => 'Majasari', 'kode_desa' => '321211006', 'kecamatan_id' => 10],
            ['nama_desa' => 'Majasih', 'kode_desa' => '321211007', 'kecamatan_id' => 10],
            ['nama_desa' => 'Sliyeg', 'kode_desa' => '321211008', 'kecamatan_id' => 10],
            ['nama_desa' => 'Gadingan', 'kode_desa' => '321211009', 'kecamatan_id' => 10],
            ['nama_desa' => 'Mekargading', 'kode_desa' => '321211010', 'kecamatan_id' => 10],
            ['nama_desa' => 'Sliyeg Lor', 'kode_desa' => '321211011', 'kecamatan_id' => 10],
            ['nama_desa' => 'Tugu Kidul', 'kode_desa' => '321211012', 'kecamatan_id' => 10],
            ['nama_desa' => 'Tugu', 'kode_desa' => '321211013', 'kecamatan_id' => 10],
            ['nama_desa' => 'Longok', 'kode_desa' => '321211014', 'kecamatan_id' => 10],

            // Kecamatan Jatibarang (ID: 11)
            ['nama_desa' => 'Sukalila', 'kode_desa' => '321202001', 'kecamatan_id' => 11],
            ['nama_desa' => 'Pilangsari', 'kode_desa' => '321202002', 'kecamatan_id' => 11],
            ['nama_desa' => 'Jatibarang Baru', 'kode_desa' => '321202003', 'kecamatan_id' => 11],
            ['nama_desa' => 'Bulak', 'kode_desa' => '321202004', 'kecamatan_id' => 11],
            ['nama_desa' => 'Bulak Lor', 'kode_desa' => '321202005', 'kecamatan_id' => 11],
            ['nama_desa' => 'Jatibarang', 'kode_desa' => '321202006', 'kecamatan_id' => 11],
            ['nama_desa' => 'Kebulen', 'kode_desa' => '321202007', 'kecamatan_id' => 11],
            ['nama_desa' => 'Pawidean', 'kode_desa' => '321202008', 'kecamatan_id' => 11],
            ['nama_desa' => 'Jatisawit', 'kode_desa' => '321202009', 'kecamatan_id' => 11],
            ['nama_desa' => 'Jatisawit Lor', 'kode_desa' => '321202010', 'kecamatan_id' => 11],
            ['nama_desa' => 'Krasak', 'kode_desa' => '321202011', 'kecamatan_id' => 11],
            ['nama_desa' => 'Kalimati', 'kode_desa' => '321202012', 'kecamatan_id' => 11],
            ['nama_desa' => 'Malang Semirang', 'kode_desa' => '321202013', 'kecamatan_id' => 11],
            ['nama_desa' => 'Lobener', 'kode_desa' => '321202014', 'kecamatan_id' => 11],
            ['nama_desa' => 'Lobener Lor', 'kode_desa' => '321202015', 'kecamatan_id' => 11],

            // Kecamatan Balongan (ID: 12)
            ['nama_desa' => 'Tegalsembadra', 'kode_desa' => '321203001', 'kecamatan_id' => 12],
            ['nama_desa' => 'Sukareja', 'kode_desa' => '321203002', 'kecamatan_id' => 12],
            ['nama_desa' => 'Sukarurip', 'kode_desa' => '321203003', 'kecamatan_id' => 12],
            ['nama_desa' => 'Rawadalem', 'kode_desa' => '321203004', 'kecamatan_id' => 12],
            ['nama_desa' => 'Gelarmendala', 'kode_desa' => '321203005', 'kecamatan_id' => 12],
            ['nama_desa' => 'Tegalurung', 'kode_desa' => '321203006', 'kecamatan_id' => 12],
            ['nama_desa' => 'Balongan', 'kode_desa' => '321203007', 'kecamatan_id' => 12],
            ['nama_desa' => 'Majakerta', 'kode_desa' => '321203008', 'kecamatan_id' => 12],
            ['nama_desa' => 'Sudimampir', 'kode_desa' => '321203009', 'kecamatan_id' => 12],
            ['nama_desa' => 'Sudimampir Lor', 'kode_desa' => '321203010', 'kecamatan_id' => 12],

            // Kecamatan Indramayu (ID: 13)
            ['nama_desa' => 'Telukagung', 'kode_desa' => '321201001', 'kecamatan_id' => 13],
            ['nama_desa' => 'Plumbon', 'kode_desa' => '321201002', 'kecamatan_id' => 13],
            ['nama_desa' => 'Dukuh', 'kode_desa' => '321201003', 'kecamatan_id' => 13],
            ['nama_desa' => 'Pekandangan Jaya', 'kode_desa' => '321201004', 'kecamatan_id' => 13],
            ['nama_desa' => 'Singaraja', 'kode_desa' => '321201005', 'kecamatan_id' => 13],
            ['nama_desa' => 'Singajaya', 'kode_desa' => '321201006', 'kecamatan_id' => 13],
            ['nama_desa' => 'Pekandangan', 'kode_desa' => '321201007', 'kecamatan_id' => 13],
            ['nama_desa' => 'Karangsong', 'kode_desa' => '321201008', 'kecamatan_id' => 13],
            ['nama_desa' => 'Pabeanudik', 'kode_desa' => '321201009', 'kecamatan_id' => 13],
            ['nama_desa' => 'Tambak', 'kode_desa' => '321201010', 'kecamatan_id' => 13],

            // Kecamatan Sindang (ID: 14)
            ['nama_desa' => 'Panyindangan Kulon', 'kode_desa' => '321229001', 'kecamatan_id' => 14],
            ['nama_desa' => 'Rambatan Wetan', 'kode_desa' => '321229002', 'kecamatan_id' => 14],
            ['nama_desa' => 'Panyindangan Wetan', 'kode_desa' => '321229003', 'kecamatan_id' => 14],
            ['nama_desa' => 'Kenanga', 'kode_desa' => '321229004', 'kecamatan_id' => 14],
            ['nama_desa' => 'Terusan', 'kode_desa' => '321229005', 'kecamatan_id' => 14],
            ['nama_desa' => 'Dermayu', 'kode_desa' => '321229006', 'kecamatan_id' => 14],
            ['nama_desa' => 'Sindang', 'kode_desa' => '321229007', 'kecamatan_id' => 14],
            ['nama_desa' => 'Pengajang', 'kode_desa' => '321229008', 'kecamatan_id' => 14],
            ['nama_desa' => 'Babadan', 'kode_desa' => '321229009', 'kecamatan_id' => 14],
            ['nama_desa' => 'Wanantara', 'kode_desa' => '321229010', 'kecamatan_id' => 14],

            // Kecamatan Cantigi (ID: 15)
            ['nama_desa' => 'Cangkring', 'kode_desa' => '321224001', 'kecamatan_id' => 15],
            ['nama_desa' => 'Cantigi Kulon', 'kode_desa' => '321224002', 'kecamatan_id' => 15],
            ['nama_desa' => 'Cantigi Wetan', 'kode_desa' => '321224003', 'kecamatan_id' => 15],
            ['nama_desa' => 'Panyingkiran Kidul', 'kode_desa' => '321224004', 'kecamatan_id' => 15],
            ['nama_desa' => 'Panyingkiran Lor', 'kode_desa' => '321224005', 'kecamatan_id' => 15],
            ['nama_desa' => 'Lamarantarung', 'kode_desa' => '321224006', 'kecamatan_id' => 15],
            ['nama_desa' => 'Cemara', 'kode_desa' => '321224007', 'kecamatan_id' => 15],

            // Kecamatan Lohbener (ID: 16)
            ['nama_desa' => 'Kiajaran Wetan', 'kode_desa' => '321210001', 'kecamatan_id' => 16],
            ['nama_desa' => 'Kiajaran Kulon', 'kode_desa' => '321210002', 'kecamatan_id' => 16],
            ['nama_desa' => 'Lanjan', 'kode_desa' => '321210003', 'kecamatan_id' => 16],
            ['nama_desa' => 'Langut', 'kode_desa' => '321210004', 'kecamatan_id' => 16],
            ['nama_desa' => 'Larangan', 'kode_desa' => '321210005', 'kecamatan_id' => 16],
            ['nama_desa' => 'Waru', 'kode_desa' => '321210006', 'kecamatan_id' => 16],
            ['nama_desa' => 'Legok', 'kode_desa' => '321210007', 'kecamatan_id' => 16],
            ['nama_desa' => 'Bojongsawi', 'kode_desa' => '321210008', 'kecamatan_id' => 16],
            ['nama_desa' => 'Lohbener', 'kode_desa' => '321210009', 'kecamatan_id' => 16],
            ['nama_desa' => 'Pamayahan', 'kode_desa' => '321210010', 'kecamatan_id' => 16],
            ['nama_desa' => 'Sindangkerta', 'kode_desa' => '321210011', 'kecamatan_id' => 16],
            ['nama_desa' => 'Rambatan Kulon', 'kode_desa' => '321210012', 'kecamatan_id' => 16],

            // Kecamatan Arahan (ID: 17)
            ['nama_desa' => 'Arahan Kidul', 'kode_desa' => '321219001', 'kecamatan_id' => 17],
            ['nama_desa' => 'Arahan Lor', 'kode_desa' => '321219002', 'kecamatan_id' => 17],
            ['nama_desa' => 'Linggajati', 'kode_desa' => '321219003', 'kecamatan_id' => 17],
            ['nama_desa' => 'Cidempet', 'kode_desa' => '321219004', 'kecamatan_id' => 17],
            ['nama_desa' => 'Sukadadi', 'kode_desa' => '321219005', 'kecamatan_id' => 17],
            ['nama_desa' => 'Pranggong', 'kode_desa' => '321219006', 'kecamatan_id' => 17],
            ['nama_desa' => 'Sukasari', 'kode_desa' => '321219007', 'kecamatan_id' => 17],
            ['nama_desa' => 'Tawangsari', 'kode_desa' => '321219008', 'kecamatan_id' => 17],

            // Kecamatan Losarang (ID: 18)
            ['nama_desa' => 'Rajuneng', 'kode_desa' => '321226001', 'kecamatan_id' => 18],
            ['nama_desa' => 'Krimun', 'kode_desa' => '321226002', 'kecamatan_id' => 18],
            ['nama_desa' => 'Puntang', 'kode_desa' => '321226003', 'kecamatan_id' => 18],
            ['nama_desa' => 'Pegagan', 'kode_desa' => '321226004', 'kecamatan_id' => 18],
            ['nama_desa' => 'Rajayang', 'kode_desa' => '321226005', 'kecamatan_id' => 18],
            ['nama_desa' => 'Jangga', 'kode_desa' => '321226006', 'kecamatan_id' => 18],
            ['nama_desa' => 'Jumbleng', 'kode_desa' => '321226007', 'kecamatan_id' => 18],
            ['nama_desa' => 'Pangkalan', 'kode_desa' => '321226008', 'kecamatan_id' => 18],
            ['nama_desa' => 'Losarang', 'kode_desa' => '321226009', 'kecamatan_id' => 18],
            ['nama_desa' => 'Muntur', 'kode_desa' => '321226010', 'kecamatan_id' => 18],
            ['nama_desa' => 'Santing', 'kode_desa' => '321226011', 'kecamatan_id' => 18],
            ['nama_desa' => 'Cemara Kulon', 'kode_desa' => '321226012', 'kecamatan_id' => 18],

            // Kecamatan Kandanghaur (ID: 19)
            ['nama_desa' => 'Curug', 'kode_desa' => '321212001', 'kecamatan_id' => 19],
            ['nama_desa' => 'Pranti', 'kode_desa' => '321212002', 'kecamatan_id' => 19],
            ['nama_desa' => 'Wirakanan', 'kode_desa' => '321212003', 'kecamatan_id' => 19],
            ['nama_desa' => 'Karangmulya', 'kode_desa' => '321212004', 'kecamatan_id' => 19],
            ['nama_desa' => 'Karanganyar', 'kode_desa' => '321212005', 'kecamatan_id' => 19],
            ['nama_desa' => 'Wirapanjunan', 'kode_desa' => '321212006', 'kecamatan_id' => 19],
            ['nama_desa' => 'Pareangirang', 'kode_desa' => '321212007', 'kecamatan_id' => 19],
            ['nama_desa' => 'Bulak', 'kode_desa' => '321212008', 'kecamatan_id' => 19],
            ['nama_desa' => 'Ilir', 'kode_desa' => '321212009', 'kecamatan_id' => 19],
            ['nama_desa' => 'Soge', 'kode_desa' => '321212010', 'kecamatan_id' => 19],
            ['nama_desa' => 'Wetan', 'kode_desa' => '321212011', 'kecamatan_id' => 19],
            ['nama_desa' => 'Eretan Kulon', 'kode_desa' => '321212012', 'kecamatan_id' => 19],
            ['nama_desa' => 'Kertawinangun', 'kode_desa' => '321212013', 'kecamatan_id' => 19],

            // Kecamatan Bongas (ID: 20)
            ['nama_desa' => 'Cipedang', 'kode_desa' => '321227001', 'kecamatan_id' => 20],
            ['nama_desa' => 'Sidamulya', 'kode_desa' => '321227002', 'kecamatan_id' => 20],
            ['nama_desa' => 'Margamulya', 'kode_desa' => '321227003', 'kecamatan_id' => 20],
            ['nama_desa' => 'Kertajaya', 'kode_desa' => '321227004', 'kecamatan_id' => 20],
            ['nama_desa' => 'Bongas', 'kode_desa' => '321227005', 'kecamatan_id' => 20],
            ['nama_desa' => 'Cipaat', 'kode_desa' => '321227006', 'kecamatan_id' => 20],
            ['nama_desa' => 'Kertamulya', 'kode_desa' => '321227007', 'kecamatan_id' => 20],
            ['nama_desa' => 'Plawangan', 'kode_desa' => '321227008', 'kecamatan_id' => 20],

            // Kecamatan Anjatan (ID: 21)
            ['nama_desa' => 'Anjatan', 'kode_desa' => '321222001', 'kecamatan_id' => 21],
            ['nama_desa' => 'Mangunjaya', 'kode_desa' => '321222002', 'kecamatan_id' => 21],
            ['nama_desa' => 'Bugis', 'kode_desa' => '321222003', 'kecamatan_id' => 21],
            ['nama_desa' => 'Bugis Tua', 'kode_desa' => '321222004', 'kecamatan_id' => 21],
            ['nama_desa' => 'Salamdarma', 'kode_desa' => '321222005', 'kecamatan_id' => 21],
            ['nama_desa' => 'Kedungwungu', 'kode_desa' => '321222006', 'kecamatan_id' => 21],
            ['nama_desa' => 'Wanguk', 'kode_desa' => '321222007', 'kecamatan_id' => 21],
            ['nama_desa' => 'Lempuyang', 'kode_desa' => '321222008', 'kecamatan_id' => 21],
            ['nama_desa' => 'Kopyah', 'kode_desa' => '321222009', 'kecamatan_id' => 21],
            ['nama_desa' => 'Anjatan Baru', 'kode_desa' => '321222010', 'kecamatan_id' => 21],
            ['nama_desa' => 'Cilandak', 'kode_desa' => '321222011', 'kecamatan_id' => 21],
            ['nama_desa' => 'Cilandak Lor', 'kode_desa' => '321222012', 'kecamatan_id' => 21],
            ['nama_desa' => 'Anjatan Utara', 'kode_desa' => '321222013', 'kecamatan_id' => 21],

            // Kecamatan Sukra (ID: 22)
            ['nama_desa' => 'Sukra', 'kode_desa' => '321223001', 'kecamatan_id' => 22],
            ['nama_desa' => 'Ujunggebang', 'kode_desa' => '321223002', 'kecamatan_id' => 22],
            ['nama_desa' => 'Tegaltaman', 'kode_desa' => '321223003', 'kecamatan_id' => 22],
            ['nama_desa' => 'Sukra Wetan', 'kode_desa' => '321223004', 'kecamatan_id' => 22],
            ['nama_desa' => 'Sumuradem', 'kode_desa' => '321223005', 'kecamatan_id' => 22],
            ['nama_desa' => 'Sumuradem Timur', 'kode_desa' => '321223006', 'kecamatan_id' => 22],
            ['nama_desa' => 'Karanglayung', 'kode_desa' => '321223007', 'kecamatan_id' => 22],

            // Kecamatan Gantar (ID: 23)
            ['nama_desa' => 'Bantarwaru', 'kode_desa' => '321221001', 'kecamatan_id' => 23],
            ['nama_desa' => 'Sanca', 'kode_desa' => '321221002', 'kecamatan_id' => 23],
            ['nama_desa' => 'Mekariyaya', 'kode_desa' => '321221003', 'kecamatan_id' => 23],
            ['nama_desa' => 'Gantar', 'kode_desa' => '321221004', 'kecamatan_id' => 23],
            ['nama_desa' => 'Situraja', 'kode_desa' => '321221005', 'kecamatan_id' => 23],
            ['nama_desa' => 'Balareja', 'kode_desa' => '321221006', 'kecamatan_id' => 23],
            ['nama_desa' => 'Mekarwaru', 'kode_desa' => '321221007', 'kecamatan_id' => 23],

            // Kecamatan Terisi (ID: 24)
            ['nama_desa' => 'Cikawung', 'kode_desa' => '321214001', 'kecamatan_id' => 24],
            ['nama_desa' => 'Jatimulya', 'kode_desa' => '321214002', 'kecamatan_id' => 24],
            ['nama_desa' => 'Jatimunggul', 'kode_desa' => '321214003', 'kecamatan_id' => 24],
            ['nama_desa' => 'Plosokerep', 'kode_desa' => '321214004', 'kecamatan_id' => 24],
            ['nama_desa' => 'Rajasinga', 'kode_desa' => '321214005', 'kecamatan_id' => 24],
            ['nama_desa' => 'Karangasem', 'kode_desa' => '321214006', 'kecamatan_id' => 24],
            ['nama_desa' => 'Cibereng', 'kode_desa' => '321214007', 'kecamatan_id' => 24],
            ['nama_desa' => 'Kendayakan', 'kode_desa' => '321214008', 'kecamatan_id' => 24],
            ['nama_desa' => 'Manggungan', 'kode_desa' => '321214009', 'kecamatan_id' => 24],

            // Kecamatan Sukagumiwang (ID: 25)
            ['nama_desa' => 'Cibeber', 'kode_desa' => '321215001', 'kecamatan_id' => 25],
            ['nama_desa' => 'Bondan', 'kode_desa' => '321215002', 'kecamatan_id' => 25],
            ['nama_desa' => 'Gunungsari', 'kode_desa' => '321215003', 'kecamatan_id' => 25],
            ['nama_desa' => 'Sukagumiwang', 'kode_desa' => '321215004', 'kecamatan_id' => 25],
            ['nama_desa' => 'Tersana', 'kode_desa' => '321215005', 'kecamatan_id' => 25],
            ['nama_desa' => 'Cadang Pinggan', 'kode_desa' => '321215006', 'kecamatan_id' => 25],
            ['nama_desa' => 'Gedangan', 'kode_desa' => '321215007', 'kecamatan_id' => 25],

            // Kecamatan Kedokan Bunder (ID: 26)
            ['nama_desa' => 'Cangkring', 'kode_desa' => '321231001', 'kecamatan_id' => 26],
            ['nama_desa' => 'Jayawinangun', 'kode_desa' => '321231002', 'kecamatan_id' => 26],
            ['nama_desa' => 'Kedokan Agung', 'kode_desa' => '321231003', 'kecamatan_id' => 26],
            ['nama_desa' => 'Kedokanbunder', 'kode_desa' => '321231004', 'kecamatan_id' => 26],
            ['nama_desa' => 'Kedokanbunder Wetan', 'kode_desa' => '321231005', 'kecamatan_id' => 26],
            ['nama_desa' => 'Kaplongan', 'kode_desa' => '321231006', 'kecamatan_id' => 26],
            ['nama_desa' => 'Jayalaksana', 'kode_desa' => '321231007', 'kecamatan_id' => 26],

            // Kecamatan Pasekan (ID: 27)
            ['nama_desa' => 'Brondong', 'kode_desa' => '321225001', 'kecamatan_id' => 27],
            ['nama_desa' => 'Pabeanilir', 'kode_desa' => '321225002', 'kecamatan_id' => 27],
            ['nama_desa' => 'Pagirikan', 'kode_desa' => '321225003', 'kecamatan_id' => 27],
            ['nama_desa' => 'Pasekan', 'kode_desa' => '321225004', 'kecamatan_id' => 27],
            ['nama_desa' => 'Karanganyar', 'kode_desa' => '321225005', 'kecamatan_id' => 27],
            ['nama_desa' => 'Totoran', 'kode_desa' => '321225006', 'kecamatan_id' => 27],

            // Kecamatan Tukdana (ID: 28)
            ['nama_desa' => 'Bodas', 'kode_desa' => '321209001', 'kecamatan_id' => 28],
            ['nama_desa' => 'Gadel', 'kode_desa' => '321209002', 'kecamatan_id' => 28],
            ['nama_desa' => 'Rancajawat', 'kode_desa' => '321209003', 'kecamatan_id' => 28],
            ['nama_desa' => 'Kerticala', 'kode_desa' => '321209004', 'kecamatan_id' => 28],
            ['nama_desa' => 'Cangko', 'kode_desa' => '321209005', 'kecamatan_id' => 28],
            ['nama_desa' => 'Karangkerta', 'kode_desa' => '321209006', 'kecamatan_id' => 28],
            ['nama_desa' => 'Sukamulya', 'kode_desa' => '321209007', 'kecamatan_id' => 28],
            ['nama_desa' => 'Mekarsari', 'kode_desa' => '321209008', 'kecamatan_id' => 28],
            ['nama_desa' => 'Lajer', 'kode_desa' => '321209009', 'kecamatan_id' => 28],
            ['nama_desa' => 'Tukdana', 'kode_desa' => '321209010', 'kecamatan_id' => 28],
            ['nama_desa' => 'Sukadana', 'kode_desa' => '321209011', 'kecamatan_id' => 28],
            ['nama_desa' => 'Pagedangan', 'kode_desa' => '321209012', 'kecamatan_id' => 28],
            ['nama_desa' => 'Sukaperna', 'kode_desa' => '321209013', 'kecamatan_id' => 28],

            // Kecamatan Patrol (ID: 29)
            ['nama_desa' => 'Limpas', 'kode_desa' => '321228001', 'kecamatan_id' => 29],
            ['nama_desa' => 'Mekarsari', 'kode_desa' => '321228002', 'kecamatan_id' => 29],
            ['nama_desa' => 'Patrol Baru', 'kode_desa' => '321228003', 'kecamatan_id' => 29],
            ['nama_desa' => 'Patrol Lor', 'kode_desa' => '321228004', 'kecamatan_id' => 29],
            ['nama_desa' => 'Patrol', 'kode_desa' => '321228005', 'kecamatan_id' => 29],
            ['nama_desa' => 'Bugel', 'kode_desa' => '321228006', 'kecamatan_id' => 29],
            ['nama_desa' => 'Arjasari', 'kode_desa' => '321228007', 'kecamatan_id' => 29],
            ['nama_desa' => 'Sukahali', 'kode_desa' => '321228008', 'kecamatan_id' => 29],

            // Kecamatan Haurgeulis (ID: 30)
            ['nama_desa' => 'Cipancuh', 'kode_desa' => '321216001', 'kecamatan_id' => 30],
            ['nama_desa' => 'Haurgeulis', 'kode_desa' => '321216002', 'kecamatan_id' => 30],
            ['nama_desa' => 'Haurkolot', 'kode_desa' => '321216003', 'kecamatan_id' => 30],
            ['nama_desa' => 'Karangtumaritis', 'kode_desa' => '321216004', 'kecamatan_id' => 30],
            ['nama_desa' => 'Kertanegara', 'kode_desa' => '321216005', 'kecamatan_id' => 30],
            ['nama_desa' => 'Mekarjati', 'kode_desa' => '321216006', 'kecamatan_id' => 30],
            ['nama_desa' => 'Sidadadi', 'kode_desa' => '321216007', 'kecamatan_id' => 30],
            ['nama_desa' => 'Sukajati', 'kode_desa' => '321216008', 'kecamatan_id' => 30],
            ['nama_desa' => 'Sumbermulya', 'kode_desa' => '321216009', 'kecamatan_id' => 30],
            ['nama_desa' => 'Wanakaya', 'kode_desa' => '321216010', 'kecamatan_id' => 30],

            // Kecamatan Kroya (ID: 31)
            ['nama_desa' => 'Jayamulya', 'kode_desa' => '321220001', 'kecamatan_id' => 31],
            ['nama_desa' => 'Kroya', 'kode_desa' => '321220002', 'kecamatan_id' => 31],
            ['nama_desa' => 'Sukamelang', 'kode_desa' => '321220003', 'kecamatan_id' => 31],
            ['nama_desa' => 'Sukaslamet', 'kode_desa' => '321220004', 'kecamatan_id' => 31],
            ['nama_desa' => 'Sumbon', 'kode_desa' => '321220005', 'kecamatan_id' => 31],
            ['nama_desa' => 'Temiyang', 'kode_desa' => '321220006', 'kecamatan_id' => 31],
            ['nama_desa' => 'Temiyangsari', 'kode_desa' => '321220007', 'kecamatan_id' => 31],
            ['nama_desa' => 'Tanjungkerta', 'kode_desa' => '321220008', 'kecamatan_id' => 31],
            ['nama_desa' => 'Tanjungsari', 'kode_desa' => '321220009', 'kecamatan_id' => 31],
        ];

        $hasIsActive = Schema::hasColumn('desas', 'is_active');

        foreach ($desa as $d) {
            $data = [
                'nama_desa' => $d['nama_desa'],
                'kode_desa' => $d['kode_desa'],
                'kecamatan_id' => $d['kecamatan_id'],
                'kecamatan' => $this->getKecamatanName($d['kecamatan_id']),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($hasIsActive) {
                $data['is_active'] = true;
            }

            DB::table('desas')->insert($data);
        }
    }

    private function getKecamatanName($id)
    {
        $kecamatan = [
            1 => 'Gabuswetan',
            2 => 'Cikedung',
            3 => 'Lelea',
            4 => 'Bangodua',
            5 => 'Widasari',
            6 => 'Kertasemaya',
            7 => 'Krangkeng',
            8 => 'Karangampel',
            9 => 'Juntinyuat',
            10 => 'Sliyeg',
            11 => 'Jatibarang',
            12 => 'Balongan',
            13 => 'Indramayu',
            14 => 'Sindang',
            15 => 'Cantigi',
            16 => 'Lohbener',
            17 => 'Arahan',
            18 => 'Losarang',
            19 => 'Kandanghaur',
            20 => 'Bongas',
            21 => 'Anjatan',
            22 => 'Sukra',
            23 => 'Gantar',
            24 => 'Terisi',
            25 => 'Sukagumiwang',
            26 => 'Kedokan Bunder',
            27 => 'Pasekan',
            28 => 'Tukdana',
            29 => 'Patrol',
            30 => 'Haurgeulis',
            31 => 'Kroya',
        ];
        return $kecamatan[$id] ?? 'Unknown';
    }
}
