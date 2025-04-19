import { createRouter, createWebHistory } from 'vue-router'
import Index from '@/pages/admin/index/Index.vue'
import Login from '@/pages/admin/auth/Login.vue'
import UnknownPassword from '@/pages/admin/auth/UnknownPassword.vue'
import Dashboard from '@/pages/admin/dashboard/Dashboard.vue'
import ApplicationError from '@/pages/application/Error.vue'




const routes = [
    { path: '/admin', component: Index },
    { path: '/admin/login', component: Login },
    { path: '/admin/unknown_password', component: UnknownPassword },
    { path: '/admin/dashboard', component: Dashboard },

    { path: '/admin/error', component: ApplicationError },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});


router.beforeEach(async (to, from, next) => {


    next();
    return;



})


export default router;