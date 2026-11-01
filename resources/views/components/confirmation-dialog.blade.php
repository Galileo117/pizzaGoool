<dialog id="confirmation-dialog"
    class="w-[calc(100%-2rem)] max-w-md rounded-3xl border-0 bg-white p-0 text-gray-800 shadow-2xl dark:bg-gray-800 dark:text-gray-100">
    <div class="p-6 text-center sm:p-8">
        <div id="confirmation-dialog-icon"
            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-300">
            <i class="fa-solid fa-triangle-exclamation text-2xl" aria-hidden="true"></i>
        </div>
        <h2 id="confirmation-dialog-title" class="text-xl font-extrabold text-gray-900 dark:text-white">Confirmar acción</h2>
        <p id="confirmation-dialog-message" class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300"></p>

        <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-center">
            <button type="button" id="confirmation-dialog-cancel"
                class="rounded-xl border border-gray-200 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                Volver
            </button>
            <button type="button" id="confirmation-dialog-confirm"
                class="rounded-xl bg-red-600 px-5 py-3 font-bold text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Confirmar
            </button>
        </div>
    </div>
</dialog>
