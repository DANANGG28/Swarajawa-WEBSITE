@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6">
        <section class="bg-surface-container-lowest rounded-2xl shadow-sm border border-gray-100">
            <details class="group">
                <summary class="cursor-pointer list-none px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary-600">person_add</span>
                        <span class="font-heading text-heading font-bold text-on-surface">Tambah Akun Siswa</span>
                    </div>
                    <span class="material-symbols-outlined text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <form method="POST" action="{{ route('superadmin.siswa.store') }}" class="px-5 pb-6 pt-2 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">NIS</span>
                        <input type="text" name="nis" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
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
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Kelas</span>
                        <input type="text" name="kelas" placeholder="7A" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
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
                        <span class="material-symbols-outlined text-[20px]">save</span> Simpan Akun Siswa
                    </button>
                </form>
            </details>
        </section>

        <section class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-gray-100">
            <form method="GET" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <label class="flex flex-col gap-1.5 flex-1">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Cari Nama / NIS</span>
                    <input type="text" name="q" value="{{ request('q') }}" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                </label>
                <label class="flex flex-col gap-1.5 sm:w-44">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Kelas</span>
                    <select name="kelas" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                        <option value="">Kabeh Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k }}" @selected(request('kelas') === $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-2.5 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">search</span> Saring
                </button>
            </form>
        </section>

        <section class="bg-surface-container-lowest rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 border-b border-gray-100">
                            <th class="px-5 py-3">Siswa</th>
                            <th class="px-5 py-3">Kelas</th>
                            <th class="px-5 py-3">EXP</th>
                            <th class="px-5 py-3">Streak</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-body text-body">
                        @forelse ($siswaList as $s)
                            <tr class="hover:bg-surface-container-low/40 transition-colors align-top">
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-on-surface">{{ $s->nama_lengkap }}</span>
                                        <span class="font-caption text-caption text-gray-500">NIS {{ $s->nis }} • {{ $s->email }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-on-surface-variant">{{ $s->kelas ?? '-' }}</td>
                                <td class="px-5 py-3.5 font-heading font-extrabold text-primary-700">{{ number_format($s->exp?->total_exp ?? 0) }}</td>
                                <td class="px-5 py-3.5 font-semibold text-orange-500">{{ $s->strek?->current_streak ?? 0 }} dina</td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('superadmin.siswa.destroy', $s) }}" onsubmit="return confirm('Busak akun siswa iki?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-9 h-9 rounded-full bg-error-container/60 text-error flex items-center justify-center hover:bg-error-container transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                    <details class="group mt-2 text-left">
                                        <summary class="cursor-pointer list-none font-caption text-caption font-bold text-primary-600 flex items-center justify-end gap-1">
                                            <span class="material-symbols-outlined text-[16px] group-open:rotate-180 transition-transform">edit</span> Sunting
                                        </summary>
                                        <form method="POST" action="{{ route('superadmin.siswa.update', $s) }}" class="mt-3 pt-3 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            @csrf @method('PUT')
                                            <input type="text" name="nama_lengkap" value="{{ $s->nama_lengkap }}" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500" placeholder="Nama">
                                            <input type="text" name="kelas" value="{{ $s->kelas }}" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500" placeholder="Kelas">
                                            <input type="email" name="email" value="{{ $s->email }}" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500" placeholder="Email">
                                            <input type="password" name="password" placeholder="Sandi anyar (opsional)" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                                            <button type="submit" class="sm:col-span-2 self-start flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-5 py-2.5 shadow-sm">
                                                <span class="material-symbols-outlined text-[18px]">save</span> Simpan
                                            </button>
                                        </form>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-12 text-center text-gray-500 font-body text-body">Durung ana akun siswa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($siswaList->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $siswaList->links() }}</div>
            @endif
        </section>
    </div>
@endsection
