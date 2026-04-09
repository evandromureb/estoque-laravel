/**
 * @param {import('alpinejs').Alpine} Alpine
 */
export function defineCategoryManagement(Alpine) {
    Alpine.data('categoryManagement', (routes) => ({
        routes,
        modalOpen: false,
        infoModalOpen: false,
        isEditing: false,
        formAction: routes.storeUrl,
        form: { id: null, name: '', description: '', additional_info: '' },
        openCreateModal() {
            this.isEditing = false;
            this.formAction = this.routes.storeUrl;
            this.form = { id: null, name: '', description: '', additional_info: '' };
            this.modalOpen = true;
        },
        openEditModal(category) {
            this.isEditing = true;
            this.formAction = `${this.routes.baseUrl}/${category.id}`;
            this.form = { ...category };
            this.modalOpen = true;
        },
        openInfoModal(category) {
            this.formAction = `${this.routes.baseUrl}/${category.id}`;
            this.form = { ...category };
            this.infoModalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
        },
    }));
}
