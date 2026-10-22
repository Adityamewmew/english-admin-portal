@props(['module'])

<x-admin.modal id="editSubModuleModal" title="Ubah Nama Sub-Modul" size="sm:max-w-md">
    <form id="editSubModuleForm" method="POST" action="" navigate-form>
        @csrf
        <input type="hidden" name="wizard" value="{{ request('wizard') }}">
        <input type="hidden" name="step" value="{{ request('step') }}">

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1">
                    Nama / Judul Sub-Modul <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" id="editSubModuleTitleInput"
                    class="w-full text-xs rounded-[10px] border-slate-300 bg-white py-2 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20"
                    required>
            </div>

            <div class="flex justify-end items-center gap-2 pt-3 border-t border-slate-200 dark:border-neutral-700">
                <x-admin.button type="button" size="sm" variant="outline" data-hs-overlay="#editSubModuleModal">
                    Batal
                </x-admin.button>
                <x-admin.button type="submit" size="sm" variant="secondary">
                    Simpan Perubahan
                </x-admin.button>
            </div>
        </div>
    </form>
</x-admin.modal>
