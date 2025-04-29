import { defineStore } from 'pinia'
import { useAdminStore } from "@/stores/admin/AdminStore";
import { createBaseStore } from "./BaseStore";
import { useNotificationStore } from "@/stores/spa/NotificationStore";

const baseStore = createBaseStore('users', 'user');

export const useUserStore = defineStore("AdminUserStore", {
    state: () => ({
        ...baseStore.state(),
    }),

    actions: {
        ...baseStore.actions(),

        async updateWithCode(data) {
            const adminStore = useAdminStore();
            adminStore.is_loading++;
            try {
                const response = await axios.post('/api/admin/users/update_with_code/', data);
                this.user = response.data;
                return true;
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.adminStore?.timeout,
                });
                return false;
            } finally {
                adminStore.is_loading--;
            }
        },

        async savePassword(data) {
            const notification = useNotificationStore();
            const adminStore = useAdminStore();
            adminStore.is_loading++;
            this.api_answer = null;
            try {
                const response = await axios.post('/api/admin/users/save_password/', data);
                if (response.data.step) {
                    return response.data.step;
                }
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: this.adminStore?.timeout,
                });
                return false;
            } finally {
                adminStore.is_loading--;
            }
        },

        async savePasswordWithCode(data) {
            const notification = useNotificationStore();
            const adminStore = useAdminStore();
            adminStore.is_loading++;
            this.api_answer = null;
            try {
                return true;
            } catch (error) {
                notification.notify({
                    status: error.response.status,
                    message: error.response.data.message || 'Fehler passiert.',
                    type: 'error',
                    timeout: adminStore.config?.timeout,
                });
                return false;
            } finally {
                adminStore.is_loading--;
            }
        }
    },
});
