<script setup>
import RecipesDatatable from "@/components/RecipesDatatable.vue";
import {onMounted, ref} from "vue";
import {useRoute, useRouter} from "vue-router";

const router = useRouter();
const route = useRoute();

const recipesPage = ref({});
const loading = ref(true);
const rowsPerPage = ref(25);
const page = ref(1);
const filters = ref({});

const getBackendData = () => {
    loading.value = true;

    const backendUrl = new URL('https://backend.wild-alaskan.test/api/recipes');
    backendUrl.searchParams.set('page', page.value.toString());
    backendUrl.searchParams.set('limit', rowsPerPage.value.toString());

    for (let [key, value] of Object.entries(filters.value)) {
        backendUrl.searchParams.set(key, value.toString());
    }

    router.push({
        name: 'home',
        params: backendUrl.searchParams
    });

    fetch(backendUrl.toString())
        .then(response => response.json())
        .then((data) => {
            recipesPage.value = data;
            loading.value = false;
        });
};

onMounted(() => {
    rowsPerPage.value = Number(route.query?.limit ?? 25);
    page.value = Number(route.query?.page ?? 1);

    if (route.query?.author_email) {

    }

    if (route.query?.keyword) {

    }

    if (route.query?.ingredient) {

    }

    getBackendData();
});

const setPage = (eventPage, eventRowsPerPage) => {
    page.value = eventPage;
    rowsPerPage.value = eventRowsPerPage;
    getBackendData();
};

const setFilters = (eventFilters) => {
    page.value = 1;
    filters.value = eventFilters;
    getBackendData();
};

</script>

<template>
  <main>
      <RecipesDatatable
          :pageData="recipesPage"
          :loading="loading"
          v-model:rowsPerPage="rowsPerPage"
          v-model:page="page"
          v-model:filters="filters"
          @pageChanged="setPage"
          @filtersChanged="setFilters"
      />
  </main>
</template>
