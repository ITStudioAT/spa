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
                this.snackMsg(error.response.status, error.response.data.message, 'error');
                return false;
            } finally {
                adminStore.is_loading--;
            }
        },

        async savePassword(data) {
            this.api_answer = null;
            const adminStore = useAdminStore();
            adminStore.is_loading++;
            try {
                const response = await axios.post('/api/admin/users/save_password/', data);
                if (response.data.step) {
                    return response.data.step;
                }
            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error');
                return false;
            } finally {
                adminStore.is_loading--;
            }
        },

        async savePasswordWithCode(data) {
            const notification = useNotificationStore();
            this.api_answer = null;
            const adminStore = useAdminStore();
            adminStore.is_loading++;
            try {
                const response = await axios.post('/api/admin/users/save_password_with_code/', data);
                if (response.data.step) {
                    return response.data.step;
                }
            } catch (error) {
                notification.notify({
                    message: error.response.data.message || 'Something went wrong',
                    type: 'error',
                });
                return false;
            } finally {
                adminStore.is_loading--;
            }
        }
    },
});
