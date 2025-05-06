import { useAdminStore } from "@/stores/admin/AdminStore";
import { useNotificationStore } from "@/stores/spa/NotificationStore";

export function createBaseStore(modelName, itemKey = 'item') {
    return {
        state: () => ({
            items: [],
            [itemKey]: null,
            is_loading: false,
            error: {
                status: null,
                message: null,
                is_error: false,
                is_success: false,
            },
            router: null,
            api_answer: null,
            timeout: 3000,
        }),

        actions() {
            return {
                initialize(router) {
                    this.router = router;
                },

                async index() {
                    console.log("BASESTORE.INDEX");
                    const notification = useNotificationStore();
                    const adminStore = useAdminStore();
                    adminStore.is_loading++;
                    try {
                        const response = await axios.get(`/api/admin/${modelName}`);
                        this.items = response.data;
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
                        this.is_loading = false;
                    }
                },

                async show(id) {
                    console.log("BASESTORE.SHOW");
                    const notification = useNotificationStore();
                    const adminStore = useAdminStore();
                    adminStore.is_loading++;
                    try {
                        const response = await axios.get(`/api/admin/${modelName}/${id}`);
                        this[itemKey] = response.data;
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
                        adminStore.is_loading--;
                    }
                },

                async update(data) {
                    console.log("BASESTORE.UPDATE");
                    const notification = useNotificationStore();
                    const adminStore = useAdminStore();
                    adminStore.is_loading++;
                    try {
                        const response = await axios.put(`/api/admin/${modelName}/${data.id}`, data);
                        if (response.data.answer) {
                            this.api_answer = response.data;
                        } else {
                            this[itemKey] = response.data;
                        }
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
                        adminStore.is_loading--;
                    }
                },

                async destroy(data) {
                    console.log("BASESTORE.DESTROY");
                    const notification = useNotificationStore();
                    const adminStore = useAdminStore();
                    adminStore.is_loading++;
                    try {
                        const response = await axios.delete('/api/admin/users/' + data.id, {});
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
                        adminStore.is_loading--;
                    }
                },

                redirect(status, message, type) {
                    const redirectUrl = '/application/error?status=' + status + '&message=' + encodeURIComponent(message) + '&type=' + type;
                    window.location.href = redirectUrl; // This is a real redirect
                },


            };
        }
    };
}
