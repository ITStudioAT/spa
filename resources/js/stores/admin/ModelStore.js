import { defineStore } from 'pinia'
import { useAdminStore } from "@/stores/admin/AdminStore";
import { useNotificationStore } from "@/stores/spa/NotificationStore";


export const useModelStore = defineStore("AdminModelStore", {
    state: () => ({
        router: null,

    }),

    actions: {
        initialize(router) {
            this.router = router;
        },
        async index(model, search_model) {
            const notification = useNotificationStore();
            const adminStore = useAdminStore();
            adminStore.is_loading++;
            try {
                const response = await axios.get("/api/admin/" + model, {
                    params: {
                        search_model
                    }
                });
                return true;
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.timeout,
                });
                return false;
            } finally {
                adminStore.is_loading = false;
            }
        }
    },
});
