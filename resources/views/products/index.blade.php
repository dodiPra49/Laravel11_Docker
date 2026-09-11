<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk | Laravel Docker [BANYAK ULAH]</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via CDN for instant styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .gradient-text {
            background: linear-gradient(135deg, #38bdf8 0%, #818cf8 50%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gradient-btn {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            transition: all 0.3s ease;
        }

        .gradient-btn:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.5);
            transform: translateY(-2px);
        }

        .input-field {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #f8fafc;
            transition: all 0.2s ease;
        }

        .input-field:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
            background: rgba(15, 23, 42, 0.85);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
    </style>
</head>
<body class="py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-card p-6 rounded-2xl">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 mb-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    Laravel 11 Docker Environment
                </div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Input & Data <span class="gradient-text">Produk</span>
                </h1>
                <p class="text-slate-400 text-sm mt-1">Form penyimpanan data produk langsung ke database MySQL.</p>
            </div>
            
            <!-- Quick Stats -->
            <div class="flex items-center gap-4">
                <div class="bg-slate-800/80 px-4 py-3 rounded-xl border border-slate-700/50 text-center min-w-[110px]">
                    <span class="text-xs text-slate-400 block font-medium">Total Produk</span>
                    <span class="text-xl font-bold text-indigo-400">{{ $products->count() }}</span>
                </div>
                <div class="bg-slate-800/80 px-4 py-3 rounded-xl border border-slate-700/50 text-center min-w-[110px]">
                    <span class="text-xs text-slate-400 block font-medium">Total Stok</span>
                    <span class="text-xl font-bold text-sky-400">{{ $products->sum('stock') }}</span>
                </div>
            </div>
        </div>

        <!-- Alert Success -->
        @if (session('success'))
            <div class="bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 p-4 rounded-xl flex items-center gap-3 shadow-lg animate-fade-in">
                <svg class="w-6 h-6 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Form Section -->
            <div class="lg:col-span-5">
                <div class="glass-card p-6 sm:p-8 rounded-2xl space-y-6">
                    <div class="border-b border-slate-700/60 pb-4">
                        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Tambah Produk Baru
                        </h2>
                        <p class="text-slate-400 text-xs mt-1">Isi 4 field utama di bawah ini untuk menyimpan ke MySQL.</p>
                    </div>

                    <form action="{{ route('products.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Field 1: Nama Produk -->
                        <div>
                            <label for="name" class="block text-xs font-semibold text-slate-300 mb-1">
                                1. Nama Produk <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" 
                                placeholder="Contoh: Laptop Asus ROG" 
                                class="input-field w-full px-4 py-2.5 rounded-xl text-sm @error('name') border-rose-500 @enderror" 
                                required>
                            @error('name')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Field 2: Deskripsi -->
                        <div>
                            <label for="description" class="block text-xs font-semibold text-slate-300 mb-1">
                                2. Deskripsi Produk
                            </label>
                            <textarea id="description" name="description" rows="3" 
                                placeholder="Tuliskan spesifikasi atau keterangan singkat..." 
                                class="input-field w-full px-4 py-2.5 rounded-xl text-sm @error('description') border-rose-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Grid 2 Kolom untuk Harga dan Stok -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Field 3: Harga -->
                            <div>
                                <label for="price" class="block text-xs font-semibold text-slate-300 mb-1">
                                    3. Harga (Rp) <span class="text-rose-400">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-2.5 text-xs text-slate-400 font-semibold">Rp</span>
                                    <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0"
                                        placeholder="15000000" 
                                        class="input-field w-full pl-9 pr-4 py-2.5 rounded-xl text-sm @error('price') border-rose-500 @enderror" 
                                        required>
                                </div>
                                @error('price')
                                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Field 4: Stok -->
                            <div>
                                <label for="stock" class="block text-xs font-semibold text-slate-300 mb-1">
                                    4. Jumlah Stok <span class="text-rose-400">*</span>
                                </label>
                                <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0"
                                    placeholder="10" 
                                    class="input-field w-full px-4 py-2.5 rounded-xl text-sm @error('stock') border-rose-500 @enderror" 
                                    required>
                                @error('stock')
                                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="gradient-btn w-full py-3 px-6 rounded-xl font-semibold text-sm text-white flex items-center justify-center gap-2 mt-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            Simpan ke Database
                        </button>
                    </form>
                </div>
            </div>

            <!-- Table Section -->
            <div class="lg:col-span-7">
                <div class="glass-card p-6 sm:p-8 rounded-2xl space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-700/60 pb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                                Daftar Produk Terdaftar
                            </h2>
                            <p class="text-slate-400 text-xs mt-1">Data yang tersimpan di tabel <code class="text-indigo-300">products</code> MySQL.</p>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto rounded-xl border border-slate-700/50">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="bg-slate-800/90 text-xs text-slate-400 uppercase tracking-wider border-b border-slate-700/60">
                                <tr>
                                    <th scope="col" class="px-4 py-3 font-semibold">#</th>
                                    <th scope="col" class="px-4 py-3 font-semibold">Produk</th>
                                    <th scope="col" class="px-4 py-3 font-semibold">Harga</th>
                                    <th scope="col" class="px-4 py-3 font-semibold text-center">Stok</th>
                                    <th scope="col" class="px-4 py-3 font-semibold">Waktu Input</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse ($products as $index => $product)
                                    <tr class="hover:bg-slate-800/50 transition-colors">
                                        <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-slate-100">{{ $product->name }}</div>
                                            @if ($product->description)
                                                <div class="text-xs text-slate-400 line-clamp-1 mt-0.5">{{ $product->description }}</div>
                                            @else
                                                <div class="text-xs text-slate-500 italic mt-0.5">Tanpa deskripsi</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-emerald-400">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->stock > 0 ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                                {{ $product->stock }} {{ $product->stock > 0 ? 'unit' : 'Habis' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-slate-400 whitespace-nowrap">
                                            {{ $product->created_at ? $product->created_at->diffForHumans() : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                </svg>
                                                <p class="text-sm font-medium">Belum ada data produk di database.</p>
                                                <p class="text-xs text-slate-600">Gunakan form di samping untuk menambahkan produk pertama Anda.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</body>
</html>
