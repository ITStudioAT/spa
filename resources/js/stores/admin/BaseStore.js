import { useAdminStore } from "@/stores/admin/AdminStore";

export function createBaseStore(modelName, itemKey = 'item') {
    return {
        state: () => ({
            items: [], // List of items (e.g., users, customers)
            [itemKey]: null, // Single item (dynamic key!)
            is_loading: false,
            error: {
                status: null,
                message: null,
                is_error: false,
            },
            router: null,
            api_answer: null,
        }),

        actions: {
            initialize(router) {
                this.router = router;
            },


            // Get list
            async index() {
                this.is_loading++;
                try {
                    const response = await axios.get(`/api/admin/${modelName}`);
                    this.items = response.data;
                } catch (error) {

                    this.errorMsg(error);
                } finally {
                    this.is_loading = false;
                }
            },

            // Get single item
            async show(id) {
                this.api_answer = null;
                const adminStore = useAdminStore();
                adminStore.is_loading++;
                try {
                    const response = await axios.get(`/api/admin/${modelName}/${id}`);
                    this[itemKey] = response.data; // <-- dynamic assignment!
                } catch (error) {
                    this.errorMsg(error);
                } finally {
                    adminStore.is_loading--;
                }
            },

            async update(data) {
                this.api_answer = null;
                const adminStore = useAdminStore();
                adminStore.is_loading++;
                try {
                    const response = await axios.put(`/api/admin/${modelName}/${data.id}`, data);
                    if (response.data.answer) {
                        this.api_answer = response.data;
                        return;
                    } else {
                        this[itemKey] = response.data;
                    }


                } catch (error) {
                    this.errorMsg(error);
                } finally {
                    adminStore.is_loading--;
                }
            },

            errorMsg(error) {
                const adminStore = useAdminStore();
                adminStore.error.status = error.response?.status;
                adminStore.error.message = error.response?.data?.message || error.message;
                adminStore.error.is_error = true;
            },
        },


    };
}
