@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6">
        <section class="bg-surface-container-lowest rounded-2xl shadow-sm border border-gray-100">
            <details class="group">
                <summary class="cursor-pointer list-none px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary-600">person_add</span>
                        <span class="font-heading text-heading font-bold text-on-surface">Daftarake Guru Anyar</span>
                    </div>
                    <span class="material-symbols-outlined text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <form method="POST" action="{{ route('superadmin.guru.store') }}" class="px-5 pb-6 pt-2 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">NIP</span>
                        <input type="text" name="nip" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                    </label>
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Nama Jangkep</span>
                        <input type="text" name="nama_lengkap" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                    </label>
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Jenis Kelamin</span>
                        <select name="jenis_kelamin" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                            <option value="L">Lanang</option>
                            <option value="P">Wadon</option>
                        </select>
                    </label>
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Status Pegawaian</span>
                        <input type="text" name="status_pegawaian" placeholder="PNS / PPPK / GTT" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                    </label>
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">No. Telpon</span>
                        <input type="text" name="no_telpon" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                    </label>
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Email</span>
                        <input type="email" name="email" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                    </label>
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Tembung Sandi</span>
                        <input type="password" name="password" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                    </label>
                    <button type="submit" class="sm:col-span-2 self-start flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-3 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">save</span> Simpan Akun Guru
                    </button>
                </form>
            </details>
        </section>

        <section class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-gray-100">
            <form method="GET" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <label class="flex flex-col gap-1.5 flex-1">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Cari Nama / NIP</span>
                    <input type="text" name="q" value="{{ request('q') }}" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                </label>
                <button type="submit" class="flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-2.5 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">search</span> Saring
                </button>
            </form>
        </section>

        <section class="flex flex-col gap-3">
            @forelse ($guruList as $g)
                <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-full bg-primary-700 text-white flex items-center justify-center font-bold text-sm shrink-0 uppercase">
                                {{ \Illuminate\Support\Str::of($g->nama_lengkap)->substr(0, 2) }}
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-heading text-heading font-bold text-on-surface truncate">{{ $g->nama_lengkap }}</span>
                                <span class="font-caption text-caption text-gray-500">NIP {{ $g->nip }} • {{ $g->status_pegawaian ?? '-' }} • {{ $g->email }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-3 py-1.5 rounded-full bg-primary-fixed text-primary-700 font-caption text-caption font-bold">{{ $g->soal_count }} soal • {{ $g->test_count }} test</span>
                            <form method="POST" action="{{ route('superadmin.guru.destroy', $g) }}" onsubmit="return confirm('Busak akun guru iki?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-full bg-error-container/60 text-error flex items-center justify-center hover:bg-error-container transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    <details class="group mt-3">
                        <summary class="cursor-pointer list-none font-caption text-caption font-bold text-primary-600 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] group-open:rotate-180 transition-transform">edit</span> Sunting akun
                        </summary>
                        <form method="POST" action="{{ route('superadmin.guru.update', $g) }}" class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @csrf @method('PUT')
                            <label class="flex flex-col gap-1.5">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Nama</span>
                                <input type="text" name="nama_lengkap" value="{{ $g->nama_lengkap }}" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                            </label>
                            <label class="flex flex-col gap-1.5">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Status Pegawaian</span>
                                <input type="text" name="status_pegawaian" value="{{ $g->status_pegawaian }}" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                            </label>
                            <label class="flex flex-col gap-1.5">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Email</span>
                                <input type="email" name="email" value="{{ $g->email }}" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                            </label>
                            <label class="flex flex-col gap-1.5">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Sandi Anyar (opsional)</span>
                                <input type="password" name="password" placeholder="••••••••" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                            </label>
                            <button type="submit" class="sm:col-span-2 self-start flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-5 py-2.5 shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">save</span> Simpan Perubahan
                            </button>
                        </form>
                    </details>
                </div>
            @empty
                <div class="bg-surface-container-lowest rounded-2xl p-10 text-center border border-gray-100">
                    <p class="font-body text-body text-gray-500">Durung ana akun guru.</p>
                </div>
            @endforelse

            @if ($guruList->hasPages())
                <div>{{ $guruList->links() }}</div>
            @endif
        </section>
    </div>
@endsection
