import {createRouter, createWebHistory} from 'vue-router'
import HomeView from '@/views/RecipesView.vue'
import RecipeView from "@/views/RecipeView.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView,
            meta: {
                title: 'Browse Recipes - WAC Recipe Search 3000',
            }
        },
        {
            path: '/:slug',
            name: 'recipe',
            component: RecipeView,
            props: true,
            meta: {
                title: 'View Recipe - WAC Recipe Search 3000',
            }
        },
    ]
});

router.beforeEach((to, from, next) => {
    document.title = to.meta.title;
    next();
});

export default router
