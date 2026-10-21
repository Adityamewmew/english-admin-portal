@props(['module'])

<x-admin.modal id="deleteSectionModal" title="Hapus Bagian Materi" size="sm:max-w-md">
    <form id="deleteSectionForm" method="POST" action="" navigate-form>
        @csrf
        @method('DELETE')
        <input type="hidden" name="sub_module_id" id="deleteSectionSubModuleId" value="">

        <div class="space-y-4">
            <div class="flex items-center gap-3 p-3.5 bg-red-50 dark:bg-red-950/30 rounded-xl border border-red-100 dark:border-red-900/40 text-red-700 dark:text-red-300">
                <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg shrink-0">
                    <svg class="size-5 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 6h18"/>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold">Konfirmasi Hapus</h4>
                    <p class="text-xs text-red-600/90 dark:text-red-400">Tindakan ini akan menghapus materi dari modul secara permanen.</p>
                </div>
            </div>

            <p class="text-xs text-gray-600 dark:text-neutral-400">
                Apakah Anda yakin ingin menghapus bagian: <strong id="deleteSectionTitle" class="text-gray-900 dark:text-white"></strong>?
            </p>

            <div class="flex justify-end items-center gap-2 pt-2 border-t border-gray-100 dark:border-neutral-700">
                <x-admin.button type="button"
                    size="sm"
                    color="secondary"
                    data-hs-overlay="#deleteSectionModal">
                    Batal
                </x-admin.button>
                <x-admin.button type="submit"
                    size="sm"
                    color="danger">
                    Ya, Hapus Materi
                </x-admin.button>
            </div>
        </div>
    </form>
</x-admin.modal>
