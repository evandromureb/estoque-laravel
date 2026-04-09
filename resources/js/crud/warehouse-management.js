/**
 * @param {import('alpinejs').Alpine} Alpine
 */
export function defineWarehouseManagement(Alpine) {
    Alpine.data('warehouseManagement', (routes) => ({
        routes,
        modalOpen: false,
        infoModalOpen: false,
        isEditing: false,
        formAction: routes.storeUrl,
        form: { id: null, name: '', location_string: '', description: '', additional_info: '' },
        openCreateModal() {
            this.isEditing = false;
            this.formAction = this.routes.storeUrl;
            this.form = { id: null, name: '', location_string: '', description: '', additional_info: '' };
            this.modalOpen = true;
        },
        openEditModal(warehouse) {
            this.isEditing = true;
            this.formAction = `${this.routes.baseUrl}/${warehouse.id}`;
            this.form = { ...warehouse };
            this.modalOpen = true;
        },
        openInfoModal(warehouse) {
            this.formAction = `${this.routes.baseUrl}/${warehouse.id}`;
            this.form = { ...warehouse };
            this.infoModalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
        },
    }));
}
