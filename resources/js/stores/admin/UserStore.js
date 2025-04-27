import { defineStore } from 'pinia'
import { useAdminStore } from "@/stores/admin/AdminStore";
import { createBaseStore } from "./BaseStore";

const baseStore = createBaseStore('users', 'user'); // <-- first create it

export const useUserStore = defineStore("AdminUserStore", {
    state: () => ({
        ...baseStore.state(), // merge the base state
    }),

    actions: {
        ...baseStore.actions, // merge the base actions

        async updateWithCode(data) {
            const adminStore = useAdminStore();
            adminStore.is_loading++;
            try {
                const response = await axios.post('/api/admin/users/update_with_code/', data);
                this.user = response.data;
                return true;
            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error')
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
                if (response.data.answer) {
                    return 'enter_password_code';
                }
            } catch (error) {
                this.snackMsg(error.response.status, error.response.data.message, 'error')
                return false;
            } finally {
                adminStore.is_loading--;
            }
        }


    },

    getters: {
        ...baseStore.getters, // if you have getters too
    }
});
