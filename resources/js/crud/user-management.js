/**
 * @param {import('alpinejs').Alpine} Alpine
 */
export function defineUserManagement(Alpine) {
    Alpine.data('userManagement', (routes, defaultApiTokenName = 'Painel web') => ({
        routes,
        defaultApiTokenName,
        modalOpen: false,
        infoModalOpen: false,
        isEditing: false,
        formAction: routes.storeUrl,
        form: {
            id: null,
            name: '',
            email: '',
            additional_info: '',
            hasDefaultApiToken: false,
        },
        openCreateModal() {
            this.isEditing = false;
            this.formAction = this.routes.storeUrl;
            this.form = {
                id: null,
                name: '',
                email: '',
                additional_info: '',
                hasDefaultApiToken: false,
            };
            this.modalOpen = true;
        },
        openEditModal(user) {
            this.isEditing = true;
            this.formAction = `${this.routes.baseUrl}/${user.id}`;
            this.form = {
                id: null,
                name: '',
                email: '',
                additional_info: '',
                hasDefaultApiToken: false,
                ...user,
            };
            this.modalOpen = true;
        },
        openInfoModal(user) {
            this.formAction = `${this.routes.baseUrl}/${user.id}`;
            this.form = {
                id: null,
                name: '',
                email: '',
                additional_info: '',
                hasDefaultApiToken: false,
                ...user,
            };
            this.infoModalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
        },
        promptGenerateToken() {
            const name = this.defaultApiTokenName;
            Alpine.store('userTokenAction').openModal({
                formId: `user-api-token-generate-${this.form.id}`,
                title: 'Gerar token de API?',
                message: `Será criado um token Sanctum com o nome «${name}». Depois de confirmar, copie o valor mostrado na página — ele não será exibido de novo. Qualquer pessoa com o token poderá chamar a API como este usuário.`,
                confirmText: 'Gerar token',
                headerClass: 'bg-indigo-50 border-b border-indigo-100',
                titleClass: 'text-indigo-900',
                confirmBtnClass: 'bg-indigo-600 text-white hover:bg-indigo-700',
            });
        },
        promptRevokeToken() {
            const name = this.defaultApiTokenName;
            Alpine.store('userTokenAction').openModal({
                formId: `user-api-token-revoke-${this.form.id}`,
                title: 'Revogar token de API?',
                message: `O token «${name}» deixará de funcionar na hora. Integrações que ainda o usem falharão até que um novo token seja gerado.`,
                confirmText: 'Revogar token',
                headerClass: 'bg-red-50 border-b border-red-100',
                titleClass: 'text-red-900',
                confirmBtnClass: 'bg-red-600 text-white hover:bg-red-700',
            });
        },
    }));
}
