@props(['module'])

<x-admin.modal id="editSubModuleModal" title="Ubah Nama Sub-Modul" size="sm:max-w-md">
    <form id="editSubModuleForm" method="POST" action="" navigate-form>
        @csrf
        <input type="hidden" name="wizard" value="{{ request('wizard') }}">
        <input type="hidden" name="step" value="{{ request('step') }}">

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1">
                    Nama / Judul Sub-Modul <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="editSubModuleTitleInput"
                    class="w-full text-xs rounded-xl border-gray-200 bg-white py-2 px-3 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                    required>
            </div>

            <div class="flex justify-end items-center gap-2 pt-2 border-t border-gray-100 dark:border-neutral-700">
                <x-admin.button type="button" size="sm" color="secondary" data-hs-overlay="#editSubModuleModal">
                    Batal
                </x-admin.button>
                <x-admin.button type="submit" size="sm" color="primary">
                    Simpan Perubahan
                </x-admin.button>
            </div>
        </div>
    </form>
</x-admin.modal>
