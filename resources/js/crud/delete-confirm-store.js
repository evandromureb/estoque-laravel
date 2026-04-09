/**
 * @param {import('alpinejs').Alpine} Alpine
 */
export function registerDeleteConfirmStore(Alpine) {
    Alpine.store('deleteConfirm', {
        open: false,
        formId: '',
        message: '',
        title: 'Confirmar exclusão',

        /**
         * @param {string} formId
         * @param {string} message
         * @param {string} [title]
         */
        openModal(formId, message, title) {
            this.formId = formId;
            this.message = message;
            this.title = title || 'Confirmar exclusão';
            this.open = true;
        },

        close() {
            this.open = false;
            this.formId = '';
            this.message = '';
        },

        confirm() {
            const el = document.getElementById(this.formId);
            if (el instanceof HTMLFormElement) {
                if (typeof el.requestSubmit === 'function') {
                    el.requestSubmit();
                } else {
                    el.submit();
                }
            }
            this.close();
        },
    });
}
