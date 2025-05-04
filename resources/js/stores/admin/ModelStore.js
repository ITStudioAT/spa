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
        async index(model, search_model, page = 1) {
            const notification = useNotificationStore();
            const adminStore = useAdminStore();
            adminStore.is_loading++;
            try {
                const response = await axios.get("/api/admin/" + model, {
                    params: {
                        search_model: search_model,
                        page: page
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
                this.item = response.data.item;
                notification.notify({
                    message: 'Das Speichern war erfolgreich',
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

        async store(model, data) {
            const notification = useNotificationStore();
            const adminStore = useAdminStore();
            adminStore.is_loading++;
            try {
                const response = await axios.post("/api/admin/" + model, data);
                this.item = response.data.item;
                notification.notify({
                    message: 'Das Speichern war erfolgreich',
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

        async destroy(model, data) {
            const notification = useNotificationStore();
            const adminStore = useAdminStore();
            adminStore.is_loading++;
            try {
                const response = await axios.delete("/api/admin/" + model + "/" + data.id, {});
                notification.notify({
                    message: 'Das Löschen war erfolgreich',
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


        async destroyMultiple(model, data) {
            const notification = useNotificationStore();
            const adminStore = useAdminStore();
            adminStore.is_loading++;
            try {
                const response = await axios.post("/api/admin/" + model + "/destroy_multiple", data);
                notification.notify({
                    message: 'Das Löschen war erfolgreich',
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
