import { defineStore } from 'pinia'
import { useAdminStore } from "@/stores/admin/AdminStore";
import { useNotificationStore } from "@/stores/spa/NotificationStore";


export const useModelStore = defineStore("AdminModelStore", {
    state: () => ({
        router: null,
        items: [],
        item: null,
        pagination: null,

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
                this.items = response.data.items;
                this.pagination = response.data.pagination;
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
        },

        async update(model, data) {
            const notification = useNotificationStore();
            const adminStore = useAdminStore();
            adminStore.is_loading++;
            try {
                const response = await axios.put("/api/admin/" + model + "/" + data.id, data);
                this.items = response.data.item;
                notification.notify({
                    message: 'Daten wurden erfolreich gespeichert.',
                    type: 'success',
                    timeout: this.timeout,
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
        },
    },
});
