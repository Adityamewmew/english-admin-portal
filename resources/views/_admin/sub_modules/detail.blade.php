@extends('_admin._layout.app')

@section('title', 'Sub Module - ' . $subModule->title)

@section('content')
    <div class="space-y-6">
        <x-admin.page-header :title="$subModule->title" subtitle="Sub-modul kurikulum pembelajaran" :backUrl="route('admin.sub_modules.index')">
            <div class="flex items-center gap-2">
                <x-admin.button href="{{ route('admin.sub_modules.update', $subModule->id) }}" variant="secondary" size="sm" as="a">
                    @include('_admin._layout.icons.pencil')
                    Edit Sub-Modul
                </x-admin.button>
                <form action="{{ route('admin.sub_modules.delete', $subModule->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus sub-modul ini?')">
                    @csrf
                    @method('DELETE')
                    <x-admin.button type="submit" variant="danger" size="sm">
                        @include('_admin._layout.icons.trash')
                        Hapus
                    </x-admin.button>
                </form>
            </div>
        </x-admin.page-header>

        {{-- Meta Card --}}
        <x-admin.card variant="white">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-surface dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700 card-tactile">
                    <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider mb-1">Sub-Module ID</span>
                    <span class="text-sm font-mono font-bold text-primary dark:text-accent break-all">
                        {{ $subModule->id }}
                    </span>
                </div>
                <div class="p-4 rounded-xl bg-surface dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700 card-tactile">
                    <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider mb-1">Skill Category</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-heading font-extrabold bg-surface text-secondary border border-secondary/30">
                        {{ ucfirst($subModule->skill_type) }}
                    </span>
                </div>
                <div class="p-4 rounded-xl bg-surface dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700 card-tactile">
                    <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider mb-1">Parent Module</span>
                    <a href="{{ route('admin.modules.detail', $subModule->module_id) }}" class="text-sm font-heading font-bold text-primary dark:text-white hover:text-secondary transition-colors truncate block">
                        [{{ $subModule->module_cefr }}] {{ $subModule->module_title }}
                    </a>
                </div>
                <div class="p-4 rounded-xl bg-surface dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700 card-tactile">
                    <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider mb-1">Urutan</span>
                    <span class="text-sm font-mono font-bold text-primary dark:text-white">
                        #{{ $subModule->order_index }}
                    </span>
                </div>
            </div>
        </x-admin.card>

        {{-- Sections List --}}
        <x-admin.card variant="white" class="space-y-4">
            <h3 class="font-heading font-extrabold text-sm text-primary dark:text-white uppercase tracking-wider">
                Bagian Materi Pembelajaran ({{ count($sections) }})
            </h3>

            @forelse ($sections as $sec)
                <div class="p-4 rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface/50 dark:bg-neutral-900/40 space-y-2 card-tactile">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-heading font-bold text-primary dark:text-white">
                            #{{ $sec->order_index }} - {{ $sec->title }}
                        </span>
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded-lg bg-surface text-secondary border border-secondary/30">
                            {{ $sec->section_type }}
                        </span>
                    </div>
                    @if (!empty($sec->content))
                        <pre class="text-xs bg-white dark:bg-neutral-900 p-3 rounded-lg border border-slate-200 dark:border-neutral-700 font-mono text-slate-700 dark:text-neutral-300 overflow-x-auto max-h-48 shadow-[inset_0_1px_2px_rgba(0,0,0,0.03)]">{{ json_encode($sec->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    @endif
                </div>
            @empty
                <div class="text-center py-8">
                    <x-admin.empty-state text="Belum ada bagian materi di dalam sub-modul ini" />
                </div>
            @endforelse
        </x-admin.card>
    </div>
@endsection
