/**
 * @param {import('alpinejs').Alpine} Alpine
 */
export function defineUserManagement(Alpine) {
    Alpine.data('userManagement', (routes) => ({
        routes,
        modalOpen: false,
        infoModalOpen: false,
        isEditing: false,
        formAction: routes.storeUrl,
        form: { id: null, name: '', email: '', additional_info: '' },
        openCreateModal() {
            this.isEditing = false;
            this.formAction = this.routes.storeUrl;
            this.form = { id: null, name: '', email: '', additional_info: '' };
            this.modalOpen = true;
        },
        openEditModal(user) {
            this.isEditing = true;
            this.formAction = `${this.routes.baseUrl}/${user.id}`;
            this.form = { ...user };
            this.modalOpen = true;
        },
        openInfoModal(user) {
            this.formAction = `${this.routes.baseUrl}/${user.id}`;
            this.form = { ...user };
            this.infoModalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
        },
    }));
}
