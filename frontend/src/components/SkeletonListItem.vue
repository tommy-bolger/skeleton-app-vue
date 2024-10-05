<script setup>
import Skeleton from "primevue/skeleton";
</script>

<template>
    <li class="mb-4">
        <div class="flex">
            <Skeleton
                shape="circle"
                size="4rem"
                class="mr-2"
            />
            <div
                class="self-center"
                style="flex: 1"
            >
                <Skeleton
                    width="100%"
                    class="mb-2"
                />
                <Skeleton width="75%" />
            </div>
        </div>
    </li>
</template>
