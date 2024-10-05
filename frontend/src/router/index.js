import {createRouter, createWebHistory} from 'vue-router'
import HomeView from '@/views/RecipesView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView
        },
        {
            path: '/:slug',
            name: 'recipe',
            component: HomeView
        },
    ]
})

export default router
