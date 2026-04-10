/**
 * @param {import('alpinejs').Alpine} Alpine
 */
export function defineProductManagement(Alpine) {
    Alpine.data('productManagement', (config) => ({
        routes: config.routes,
        warehouses: config.warehouses ?? [],
        modalOpen: false,
        infoModalOpen: false,
        viewModalOpen: false,
        viewProduct: null,
        viewTab: 'geral',
        activeViewImage: '',
        isEditing: false,
        editTab: 'geral',
        formAction: config.routes.storeUrl,
        form: {
            id: null,
            category_id: '',
            name: '',
            sku: '',
            price: '',
            minimum_stock: 0,
            description: '',
            additional_info: '',
            images: [],
            stockLocations: [],
        },
        openCreateModal() {
            this.isEditing = false;
            this.editTab = 'geral';
            this.formAction = this.routes.storeUrl;
            this.form = {
                id: null,
                category_id: '',
                name: '',
                sku: '',
                price: '',
                minimum_stock: 0,
                description: '',
                additional_info: '',
                images: [],
                stockLocations: [],
            };
            this.modalOpen = true;
        },
        openEditModal(product) {
            this.isEditing = true;
            this.editTab = 'geral';
            this.formAction = `${this.routes.baseUrl}/${product.id}`;
            this.form = {
                ...product,
                minimum_stock: product.minimum_stock ?? 0,
                images: product.images ?? [],
                stockLocations: product.stockLocations ?? [],
            };
            this.modalOpen = true;
        },
        openInfoModal(product) {
            this.formAction = `${this.routes.baseUrl}/${product.id}`;
            this.form = {
                ...product,
                minimum_stock: product.minimum_stock ?? 0,
                images: product.images ?? [],
                stockLocations: product.stockLocations ?? [],
            };
            this.infoModalOpen = true;
        },
        openViewModal(payload) {
            this.viewProduct = payload;
            this.viewTab = 'geral';
            this.activeViewImage =
                payload.images && payload.images.length > 0 ? payload.images[0].url : '';
            this.viewModalOpen = true;
        },
        closeViewModal() {
            this.viewModalOpen = false;
            this.viewProduct = null;
            this.viewTab = 'geral';
            this.activeViewImage = '';
        },
        openEditFromView() {
            const payload = this.viewProduct?.editPayload;
            if (!payload) {
                return;
            }
            this.closeViewModal();
            this.openEditModal(payload);
        },
        closeModal() {
            this.modalOpen = false;
            this.editTab = 'geral';
        },
    }));
}
