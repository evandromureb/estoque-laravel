import { defineCategoryManagement } from './category-management';
import { registerDeleteConfirmStore } from './delete-confirm-store';
import { defineProductManagement } from './product-management';
import { defineUserManagement } from './user-management';
import { defineWarehouseManagement } from './warehouse-management';

/**
 * @param {import('alpinejs').Alpine} Alpine
 */
export function registerCrudAlpine(Alpine) {
    registerDeleteConfirmStore(Alpine);
    defineProductManagement(Alpine);
    defineCategoryManagement(Alpine);
    defineWarehouseManagement(Alpine);
    defineUserManagement(Alpine);
}
