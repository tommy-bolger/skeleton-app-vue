<script setup>

import {defineProps} from 'vue';

defineProps({
    ingredients: {
        type: Array,
        required: true
    },
});

</script>

<template>
    <ul class="list-disc">
        <li
            v-for="ingredient in ingredients"
        >
            {{ ingredient.description }}
        </li>
    </ul>
</template>
