
<!DOCTYPE html>

<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&amp;family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "on-primary-container": "#c4d2ff",
                    "on-background": "#191c1e",
                    "on-primary-fixed": "#001848",
                    "error": "#ba1a1a",
                    "tertiary-fixed": "#ffdbcf",
                    "on-secondary": "#ffffff",
                    "surface-tint": "#0c56d0",
                    "tertiary-fixed-dim": "#ffb59b",
                    "secondary-fixed": "#adecff",
                    "secondary-fixed-dim": "#5dd6f3",
                    "on-tertiary-fixed-variant": "#812800",
                    "background": "#f8f9fb",
                    "on-primary": "#ffffff",
                    "secondary-container": "#6ae1ff",
                    "on-error": "#ffffff",
                    "tertiary": "#7b2600",
                    "secondary": "#00687a",
                    "inverse-primary": "#b2c5ff",
                    "surface-variant": "#e1e2e4",
                    "on-secondary-fixed-variant": "#004e5d",
                    "on-tertiary-container": "#ffc6b2",
                    "inverse-surface": "#2e3132",
                    "primary-container": "#0052cc",
                    "primary-fixed": "#dae2ff",
                    "tertiary-container": "#a33500",
                    "on-primary-fixed-variant": "#0040a2",
                    "on-tertiary": "#ffffff",
                    "surface-container": "#edeef0",
                    "surface": "#f8f9fb",
                    "surface-container-low": "#f3f4f6",
                    "on-surface-variant": "#434654",
                    "inverse-on-surface": "#f0f1f3",
                    "surface-dim": "#d9dadc",
                    "primary": "#003d9b",
                    "primary-fixed-dim": "#b2c5ff",
                    "outline-variant": "#c3c6d6",
                    "surface-container-lowest": "#ffffff",
                    "on-secondary-fixed": "#001f26",
                    "on-surface": "#191c1e",
                    "surface-container-high": "#e7e8ea",
                    "error-container": "#ffdad6",
                    "on-secondary-container": "#006374",
                    "outline": "#737685",
                    "surface-container-highest": "#e1e2e4",
                    "on-tertiary-fixed": "#380d00",
                    "surface-bright": "#f8f9fb",
                    "on-error-container": "#93000a"
            },
            "borderRadius": {
                    "DEFAULT": "0.125rem",
                    "lg": "0.25rem",
                    "xl": "0.5rem",
                    "full": "0.75rem"
            },
            "spacing": {
                    "table-cell-padding": "12px 16px",
                    "base": "8px",
                    "gutter": "24px",
                    "sidebar-width": "260px",
                    "container-margin": "32px"
            },
            "fontFamily": {
                    "h1": ["Public Sans"],
                    "tabular-nums": ["Inter"],
                    "body-base": ["Inter"],
                    "body-sm": ["Inter"],
                    "h2": ["Public Sans"],
                    "label-caps": ["Inter"]
            },
            "fontSize": {
                    "h1": ["32px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                    "tabular-nums": ["14px", {"lineHeight": "1.0", "letterSpacing": "0", "fontWeight": "500"}],
                    "body-base": ["14px", {"lineHeight": "1.5", "letterSpacing": "0", "fontWeight": "400"}],
                    "body-sm": ["13px", {"lineHeight": "1.4", "letterSpacing": "0", "fontWeight": "400"}],
                    "h2": ["24px", {"lineHeight": "1.3", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                    "label-caps": ["11px", {"lineHeight": "1.0", "letterSpacing": "0.06em", "fontWeight": "700"}]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            background-color: #f8f9fb;
            color: #191c1e;
        }
        .mega-mendung-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54.62 9.45c-2.43-1.8-5.32-2.45-8.2-1.85-2.88.6-5.38 2.25-7.05 4.65-1.67-2.4-4.17-4.05-7.05-4.65-2.88-.6-5.77.05-8.2 1.85-2.43 1.8-4.08 4.45-4.63 7.35-.55 2.9.22 5.82 2.15 8.1 1.93 2.28 4.65 3.65 7.63 3.85 2.98.2 5.83-.8 8.1-2.8 2.27 2 5.12 3 8.1 2.8 2.98-.2 5.7-1.57 7.63-3.85 1.93-2.28 2.7-5.2 2.15-8.1-.55-2.9-2.2-5.55-4.63-7.35z' fill='%230052cc' fill-opacity='0.02'/%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="font-body-base antialiased">
<!-- SideNavBar -->
<aside class="w-[260px] h-screen fixed left-0 top-0 z-40 bg-[#0052CC] dark:bg-slate-950 text-white dark:text-blue-400 font-['Public_Sans'] antialiased border-r border-white/10 shadow-xl flex flex-col h-full py-6">
<div class="px-6 mb-10 flex items-center gap-3">
<div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center p-1.5 overflow-hidden">
<img alt="Logo" data-alt="A professional and clean Indonesian government institution logo depicting sovereignty and digital progress. The logo is minimalist with a white background, featuring subtle blue accents that align with the national administrative colors. It is positioned as a primary branding element in a modern, light-mode dashboard interface." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCW8JZwXCjexrFbTz7XZXXO8tw8yPvt64ER8WQcT1wdZFwtb2SgWk3Kf1DYR3hrNWAQXU7VRV6e91h-ow6Qr-XUc_EXUmxPnhcWjF1ympanKOndmlUgEV_H6PVO8XKeFm95LArkq1wenrqqVMeo8sLHni_zVZ6KKLFNIrvIP6ko1LzYD3XWou2MDZ8O-AaO1o_I6CXLlHVSgJc5Hz9wJdHa1B5Q17cMEEx5JwDuZ76_LJcAJzmTRdbty90NFVqdOGRNWWjcrem1dnY"/>
</div>
<div>
<h1 class="text-xl font-bold tracking-tight text-white leading-none">IDM Digital</h1>
<p class="text-[10px] uppercase tracking-widest text-blue-200/60 mt-1">Satu Data Desa</p>
</div>
</div>
<nav class="flex-1 px-3 space-y-1">
<a class="flex items-center gap-3 px-4 py-3 bg-white/10 border-l-4 border-white text-white font-semibold transition-all duration-200 ease-in-out" href="#">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
<span>Beranda</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-blue-100/70 hover:bg-white/5 transition-colors transition-all duration-200 ease-in-out hover:text-white" href="#">
<span class="material-symbols-outlined" data-icon="edit_document">edit_document</span>
<span>Input Data</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-blue-100/70 hover:bg-white/5 transition-colors transition-all duration-200 ease-in-out hover:text-white" href="#">
<span class="material-symbols-outlined" data-icon="verified_user">verified_user</span>
<span>Verifikasi</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-blue-100/70 hover:bg-white/5 transition-colors transition-all duration-200 ease-in-out hover:text-white" href="#">
<span class="material-symbols-outlined" data-icon="analytics">analytics</span>
<span>Analisis IDM</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-blue-100/70 hover:bg-white/5 transition-colors transition-all duration-200 ease-in-out hover:text-white" href="#">
<span class="material-symbols-outlined" data-icon="description">description</span>
<span>Laporan</span>
</a>
</nav>
<div class="px-3 mt-auto pt-6 border-t border-white/10 space-y-1">
<a class="flex items-center gap-3 px-4 py-3 text-blue-100/70 hover:bg-white/5 transition-colors transition-all duration-200 ease-in-out hover:text-white" href="#">
<span class="material-symbols-outlined" data-icon="settings">settings</span>
<span>Pengaturan</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-blue-100/70 hover:bg-white/5 transition-colors transition-all duration-200 ease-in-out hover:text-white" href="#">
<span class="material-symbols-outlined" data-icon="help">help</span>
<span>Bantuan</span>
</a>
</div>
</aside>
<!-- Main Content Area -->
<div class="ml-[260px] min-h-screen mega-mendung-pattern">
<!-- TopNavBar -->
<header class="fixed top-0 right-0 left-[260px] h-16 z-30 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 w-full font-['Public_Sans'] text-sm font-medium">
<div class="flex items-center gap-4">
<div class="relative">
<span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
<span class="material-symbols-outlined text-[20px]">search</span>
</span>
<input class="pl-10 pr-4 py-2 bg-slate-100 dark:bg-slate-800 border-none rounded-lg w-64 focus:ring-2 focus:ring-[#0052CC] text-slate-700" placeholder="Cari data desa..." type="text"/>
</div>
<div class="h-6 w-[1px] bg-slate-200 mx-2"></div>
<span class="text-slate-900 dark:text-white font-bold text-lg">Dashboard Indeks Desa Membangun</span>
</div>
<div class="flex items-center gap-6">
<div class="flex items-center gap-3">
<button class="text-slate-500 hover:text-[#0052CC] transition-colors active:opacity-80">
<span class="material-symbols-outlined">notifications</span>
</button>
<button class="text-slate-500 hover:text-[#0052CC] transition-colors active:opacity-80">
<span class="material-symbols-outlined">history</span>
</button>
<button class="text-slate-500 hover:text-[#0052CC] transition-colors active:opacity-80">
<span class="material-symbols-outlined">help_outline</span>
</button>
</div>
<div class="h-8 w-[1px] bg-slate-200"></div>
<div class="flex items-center gap-3">
<div class="text-right hidden sm:block">
<p class="text-xs font-bold text-slate-900 leading-none">Admin Desa</p>
<p class="text-[10px] text-slate-500">Kab. Wonogiri</p>
</div>
<img alt="User Avatar" class="w-8 h-8 rounded-full bg-slate-200" data-alt="A small, professional user profile avatar for a digital administrative dashboard. The avatar features a neutral placeholder design with the initials 'AD', representing an administrative professional. The aesthetic is clean and corporate, integrated seamlessly into a high-end web application header." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCjYV62Zui6QBkD5JfwpV8wYzR8oDbQJz-wox_rRMaVYN_Hw5i8iqdPihJS635PiCD_FxE_xeADAwmNrqn0gVMgb6L7J6a44GWGIvVLEbF6jxko1jCtKIItRKqysXui6bPK-3i1Rek6vvVn2KnIIeIvu08FU023hVgLChadskQXc-GiE0MsQHKwOje1tJlei_91K-fo5eirWSrfc_EmlexX_B3iw_XiuvS5SQ-YByQBSwONxwM4d0HhIekF1DFnusggLYVbMxOs-hY"/>
<div class="flex gap-2">
<button class="px-3 py-1.5 text-xs font-semibold text-[#0052CC] hover:bg-blue-50 rounded-md transition-colors">Profil</button>
<button class="px-3 py-1.5 text-xs font-semibold bg-red-50 text-red-600 hover:bg-red-100 rounded-md transition-colors">Keluar</button>
</div>
</div>
</div>
</header>
<!-- Page Content -->
<main class="pt-24 pb-12 px-8">
<!-- Header Section -->
<div class="flex justify-between items-end mb-8">
<div>
<h2 class="font-h1 text-h1 text-on-background">Beranda IDM</h2>
<p class="text-slate-500 mt-1">Status dan pemantauan data Indeks Desa Membangun terkini.</p>
</div>
<button class="flex items-center gap-2 bg-[#0052CC] text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-blue-700/20 hover:bg-primary transition-all">
<span class="material-symbols-outlined text-[20px]">add</span>
<span>Input Data Baru</span>
</button>
</div>
<!-- Bento Grid Summary -->
<div class="grid grid-cols-12 gap-gutter mb-gutter">
<!-- IDM Score Card -->
<div class="col-span-12 lg:col-span-4 bg-white border border-outline-variant p-6 rounded-xl flex flex-col justify-between min-h-[220px]">
<div class="flex justify-between items-start">
<div>
<p class="text-label-caps font-label-caps text-outline mb-2">SKOR IDM 2024</p>
<h3 class="text-4xl font-bold text-[#0052CC]">0.8421</h3>
</div>
<div class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1">
<span class="material-symbols-outlined text-sm">trending_up</span>
                            +2.4%
                        </div>
</div>
<div class="mt-4">
<div class="flex justify-between mb-2">
<span class="text-sm font-medium">Status: Desa Mandiri</span>
<span class="text-xs text-slate-400">Target 0.9000</span>
</div>
<div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
<div class="bg-[#0052CC] h-full" style="width: 84%"></div>
</div>
</div>
</div>
<!-- Metrics -->
<div class="col-span-6 lg:col-span-4 grid grid-rows-2 gap-gutter">
<div class="bg-white border border-outline-variant p-5 rounded-xl flex items-center gap-4">
<div class="w-12 h-12 rounded-xl bg-blue-50 text-[#0052CC] flex items-center justify-center">
<span class="material-symbols-outlined">groups</span>
</div>
<div>
<p class="text-xs font-semibold text-outline uppercase tracking-wider">IKS</p>
<p class="text-xl font-bold text-on-background">0.7892</p>
</div>
</div>
<div class="bg-white border border-outline-variant p-5 rounded-xl flex items-center gap-4">
<div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
<span class="material-symbols-outlined">payments</span>
</div>
<div>
<p class="text-xs font-semibold text-outline uppercase tracking-wider">IKE</p>
<p class="text-xl font-bold text-on-background">0.6540</p>
</div>
</div>
</div>
<div class="col-span-6 lg:col-span-4 grid grid-rows-2 gap-gutter">
<div class="bg-white border border-outline-variant p-5 rounded-xl flex items-center gap-4">
<div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
<span class="material-symbols-outlined">forest</span>
</div>
<div>
<p class="text-xs font-semibold text-outline uppercase tracking-wider">IKL</p>
<p class="text-xl font-bold text-on-background">0.9120</p>
</div>
</div>
<div class="bg-white border border-outline-variant p-5 rounded-xl flex items-center gap-4">
<div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
<span class="material-symbols-outlined">task_alt</span>
</div>
<div>
<p class="text-xs font-semibold text-outline uppercase tracking-wider">Data Terverifikasi</p>
<p class="text-xl font-bold text-on-background">12 / 12</p>
</div>
</div>
</div>
</div>
<!-- Upload Area & History Table -->
<div class="grid grid-cols-12 gap-gutter">
<!-- Upload Panel -->
<div class="col-span-12 lg:col-span-4">
<div class="bg-white border border-outline-variant rounded-xl p-6 h-full">
<h3 class="font-h2 text-h2 mb-4">Upload File Data</h3>
<p class="text-sm text-slate-500 mb-6">Unggah dokumen Excel atau PDF hasil kuisioner IDM terbaru untuk divalidasi oleh sistem.</p>
<div class="border-2 border-dashed border-slate-200 rounded-xl p-8 flex flex-col items-center justify-center bg-slate-50/50 hover:bg-blue-50/30 transition-colors cursor-pointer group">
<div class="w-16 h-16 rounded-full bg-blue-50 text-[#0052CC] flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-[32px]">upload_file</span>
</div>
<p class="font-semibold text-slate-700">Tarik file ke sini</p>
<p class="text-xs text-slate-400 mt-1">Maksimal 10MB (XLSX, PDF)</p>
<button class="mt-6 px-4 py-2 border border-[#0052CC] text-[#0052CC] rounded-lg text-sm font-bold hover:bg-[#0052CC] hover:text-white transition-all">Pilih File</button>
</div>
<div class="mt-6 space-y-3">
<div class="p-3 bg-blue-50/50 rounded-lg border border-blue-100 flex items-center gap-3">
<span class="material-symbols-outlined text-blue-600">info</span>
<p class="text-[11px] text-blue-800 leading-snug">Pastikan format kolom sesuai dengan template standar Kementerian Desa 2024.</p>
</div>
</div>
</div>
</div>
<!-- Table Panel -->
<div class="col-span-12 lg:col-span-8">
<div class="bg-white border border-outline-variant rounded-xl overflow-hidden">
<div class="p-6 border-b border-outline-variant flex justify-between items-center">
<h3 class="font-h2 text-h2">Riwayat Upload</h3>
<button class="text-sm font-bold text-[#0052CC] flex items-center gap-1">
<span>Lihat Semua</span>
<span class="material-symbols-outlined text-[18px]">chevron_right</span>
</button>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-slate-50 text-outline text-[11px] uppercase tracking-wider font-bold">
<tr>
<th class="px-6 py-4">Tanggal</th>
<th class="px-6 py-4">Nama File</th>
<th class="px-6 py-4 text-center">Status Verifikasi</th>
<th class="px-6 py-4">Catatan</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant font-tabular-nums text-body-sm">
<tr class="hover:bg-slate-50 transition-colors">
<td class="px-6 py-4 text-on-surface">12 Okt 2024, 09:15</td>
<td class="px-6 py-4 font-medium text-on-surface">IDM_2024_Final.xlsx</td>
<td class="px-6 py-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-green-100 text-green-700">
                                                Disetujui
                                            </span>
</td>
<td class="px-6 py-4 text-slate-500">Data sudah sesuai regulasi terbaru.</td>
</tr>
<tr class="hover:bg-slate-50 transition-colors">
<td class="px-6 py-4 text-on-surface">10 Okt 2024, 14:30</td>
<td class="px-6 py-4 font-medium text-on-surface">Lampiran_Sarpras_Pendidikan.pdf</td>
<td class="px-6 py-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700">
                                                Proses
                                            </span>
</td>
<td class="px-6 py-4 text-slate-500">Menunggu validasi tim teknis kabupaten.</td>
</tr>
<tr class="hover:bg-slate-50 transition-colors">
<td class="px-6 py-4 text-on-surface">05 Okt 2024, 11:20</td>
<td class="px-6 py-4 font-medium text-on-surface">IDM_Draft_V2.xlsx</td>
<td class="px-6 py-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-700">
                                                Ditolak
                                            </span>
</td>
<td class="px-6 py-4 text-slate-500">Format kolom IKS tidak sesuai template.</td>
</tr>
<tr class="hover:bg-slate-50 transition-colors">
<td class="px-6 py-4 text-on-surface">01 Okt 2024, 08:45</td>
<td class="px-6 py-4 font-medium text-on-surface">Data_Ekonomi_Triwulan3.xlsx</td>
<td class="px-6 py-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-green-100 text-green-700">
                                                Disetujui
                                            </span>
</td>
<td class="px-6 py-4 text-slate-500">-</td>
</tr>
</tbody>
</table>
</div>
<div class="p-4 bg-slate-50 border-t border-outline-variant flex items-center justify-center">
<nav class="flex gap-1">
<button class="w-8 h-8 flex items-center justify-center rounded bg-white border border-outline-variant text-outline hover:bg-slate-100">
<span class="material-symbols-outlined text-[18px]">chevron_left</span>
</button>
<button class="w-8 h-8 flex items-center justify-center rounded bg-[#0052CC] text-white border border-[#0052CC] font-bold text-xs">1</button>
<button class="w-8 h-8 flex items-center justify-center rounded bg-white border border-outline-variant text-outline hover:bg-slate-100 font-bold text-xs">2</button>
<button class="w-8 h-8 flex items-center justify-center rounded bg-white border border-outline-variant text-outline hover:bg-slate-100 font-bold text-xs">3</button>
<button class="w-8 h-8 flex items-center justify-center rounded bg-white border border-outline-variant text-outline hover:bg-slate-100">
<span class="material-symbols-outlined text-[18px]">chevron_right</span>
</button>
</nav>
</div>
</div>
</div>
</div>
</main>
</div>
</body></html>