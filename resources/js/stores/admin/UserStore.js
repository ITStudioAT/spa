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
                this.errorMsg(error);
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
