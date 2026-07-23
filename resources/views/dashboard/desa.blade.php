@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold">Beranda IDM</h1>
        <p class="text-gray-500 mt-1">
            Status dan pemantauan data IDM terbaru.
        </p>
    </div>

    <button class="bg-blue-700 text-white px-5 py-3 rounded-lg hover:bg-blue-800">
        + Input Data Baru
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    <div class="bg-white p-6 rounded-xl shadow-sm border">
        <p class="text-gray-500 text-sm">Skor IDM</p>
        <h2 class="text-4xl font-bold text-blue-700 mt-2">0</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border">
        <p class="text-gray-500 text-sm">IKS</p>
        <h2 class="text-3xl font-bold mt-2">0</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border">
        <p class="text-gray-500 text-sm">IKL</p>
        <h2 class="text-3xl font-bold mt-2">0</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border">
        <p class="text-gray-500 text-sm">Data Terverifikasi</p>
        <h2 class="text-3xl font-bold mt-2">12 / 12</h2>
    </div>


</div>

<div class="grid lg:grid-cols-2 gap-6">

    <div class="bg-white p-6 rounded-xl border shadow-sm">

        <h2 class="text-xl font-bold mb-4">Upload File Data</h2>

        <div class="border-2 border-dashed rounded-xl p-10 text-center">
            <p class="text-gray-500 mb-4">Tarik file ke sini</p>

            <button class="border border-blue-700 text-blue-700 px-5 py-2 rounded-lg">
                Pilih File
            </button>
        </div>

             </div>

    <div class="bg-white p-6 rounded-xl border shadow-sm">

        <h2 class="text-xl font-bold mb-4">Riwayat Upload</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-3">Tanggal</th>
                    <th class="text-left">Nama File</th>
                    <th class="text-left">Status</th>
                </tr>
            </thead>

            <tbody>
                <tr class="border-b">
                    <td class="py-3">12 Okt 2024</td>
                    <td>IDM_2024.xlsx</td>
                    <td>
                         <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">
                            Disetujui
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

</div>

@endsection