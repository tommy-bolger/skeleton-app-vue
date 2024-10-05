<script setup>
import {RouterView} from 'vue-router'
</script>

<template>
    <div class="px-10 pt-10">
        <header>
            <h1 class="pb-10 font-bold text-2xl">
                <RouterLink :to="{
                    name: 'home'
                }">
                    WAC Recipe Search 3000
                </RouterLink>
            </h1>
        </header>

        <RouterView />
    </div>
</template>
