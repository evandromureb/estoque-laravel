/**
 * Confirmação global para gerar/revogar token de API na gestão de usuários ($store disponível em qualquer âmbito).
 *
 * @param {import('alpinejs').Alpine} Alpine
 */
export function registerUserTokenActionStore(Alpine) {
    Alpine.store('userTokenAction', {
        open: false,
        formId: '',
        title: '',
        message: '',
        confirmText: 'Confirmar',
        headerClass: 'bg-slate-50 border-b border-slate-100',
        titleClass: 'text-slate-900',
        confirmBtnClass: 'bg-indigo-600 text-white hover:bg-indigo-700',

        /**
         * @param {{
         *   formId: string,
         *   title: string,
         *   message: string,
         *   confirmText: string,
         *   headerClass: string,
         *   titleClass: string,
         *   confirmBtnClass: string,
         * }} payload
         */
        openModal(payload) {
            this.formId = payload.formId;
            this.title = payload.title;
            this.message = payload.message;
            this.confirmText = payload.confirmText;
            this.headerClass = payload.headerClass;
            this.titleClass = payload.titleClass;
            this.confirmBtnClass = payload.confirmBtnClass;
            this.open = true;
        },

        close() {
            this.open = false;
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
