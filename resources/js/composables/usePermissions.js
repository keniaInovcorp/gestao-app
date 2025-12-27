import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function usePermissions() {
    const page = usePage();
    
    const isAdmin = computed(() => {
        return page.props.auth?.user?.roles?.includes('admin') || false;
    });
    
    const userPermissions = computed(() => {
        return page.props.auth?.user?.permissions || [];
    });
    
    const hasPermission = (permission) => {
        if (isAdmin.value) {
            return true;
        }
        return userPermissions.value.includes(permission);
    };
    
    const canCreate = (resource) => hasPermission(`${resource}.create`);
    const canRead = (resource) => hasPermission(`${resource}.read`);
    const canUpdate = (resource) => hasPermission(`${resource}.update`);
    const canDelete = (resource) => hasPermission(`${resource}.delete`);
    
    return {
        isAdmin,
        userPermissions,
        hasPermission,
        canCreate,
        canRead,
        canUpdate,
        canDelete,
    };
}

