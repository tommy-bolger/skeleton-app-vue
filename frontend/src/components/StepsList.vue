<script setup>

import {defineProps} from 'vue';

defineProps({
    steps: {
        type: Array,
        required: true
    },
});

</script>

<template>
    <ol class="list-decimal">
        <li
            v-for="step in steps"
        >
            {{ step.description }}
        </li>
    </ol>
</template>
