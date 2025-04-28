import { useAdminStore } from "@/stores/admin/AdminStore";
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
            snack_message: {
                show: false,
                status: null,
                message: null,
                type: 'error',
            },
        }),

        actions() {
            return {
                initialize(router) {
                    this.router = router;
                },

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

                async show(id) {
                    const adminStore = useAdminStore();
                    adminStore.is_loading++;
                    try {
                        const response = await axios.get(`/api/admin/${modelName}/${id}`);
                        this[itemKey] = response.data;
                    } catch (error) {
                        this.snackMsg(error.response?.status, error.response?.data.message, 'error');
                    } finally {
                        adminStore.is_loading--;
                    }
                },

                async update(data) {
                    const adminStore = useAdminStore();
                    adminStore.is_loading++;
                    try {
                        const response = await axios.put(`/api/admin/${modelName}/${data.id}`, data);
                        if (response.data.answer) {
                            this.api_answer = response.data;
                        } else {
                            this[itemKey] = response.data;
                        }
                    } catch (error) {
                        this.snackMsg(error.response.status, error.response.data.message, 'error');
                    } finally {
                        adminStore.is_loading--;
                    }
                },

                snackMsg(status, message, type = 'error') {
                    this.snack_message.status = status;
                    this.snack_message.message = message;
                    this.snack_message.type = type;
                    this.snack_message.show = true;
                }
            };
        }
    };
}
