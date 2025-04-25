import { createRouter, createWebHistory } from 'vue-router'
import Index from '@/pages/admin/index/Index.vue'
import Login from '@/pages/admin/auth/Login.vue'
import UnknownPassword from '@/pages/admin/auth/UnknownPassword.vue'
import Register from '@/pages/admin/auth/Register.vue'
import Dashboard from '@/pages/admin/dashboard/Dashboard.vue'


const routes = [
    { path: '/admin', component: Index },
    { path: '/admin/login', component: Login },
    { path: '/admin/unknown_password', component: UnknownPassword },
    { path: '/admin/register', component: Register },
    { path: '/admin/dashboard', component: Dashboard },
    { path: '/admin/test_route/:id', component: Dashboard },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});


router.beforeEach(async (to, from, next) => {

    // /application wird nicht wieter geprüft
    if (to.path.startsWith('/application')) {
        next();
        return;
    }

    const matched = to.matched[0];

    // Extract base path (remove everything from first `/:` onward)
    const basePath = matched?.path.replace(/\/:.*/g, '');

    // Find route whose path starts with the base path and contains dynamic params
    const matchingRoute = routes.find(route => {
        const staticRoutePath = route.path.replace(/\/:.*/g, '');
        return staticRoutePath === basePath;
    });

    const data = {
        route: 'admin',
        from: from?.path ?? null,
        to: to?.path ?? null,
        matching_path: matchingRoute?.path ?? null,
        base_path: basePath ?? null
    };

    const answer = await isRouteAllowed(data);
    if (answer) { next(); } else { next(false); }

})

async function isRouteAllowed(data) {
    try {
        const answer = await axios.post("/api/routes/is_route_allowed", { data });
        return true;
    } catch (error) {
        const redirectUrl = '/application/error?status=' + error.response.status + '&message=' + encodeURIComponent(error.response.data.message) + '&type=' + error;
        window.location.href = redirectUrl;
        return false;
    } finally {

    }
}

export default router;