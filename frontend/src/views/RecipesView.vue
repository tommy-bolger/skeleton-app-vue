<script setup>
import RecipesDatatable from "@/components/RecipesDatatable.vue";
import {onMounted, ref, watch} from "vue";
import {useRoute, useRouter} from "vue-router";

const router = useRouter();
const route = useRoute();

const recipesPage = ref({});
const loading = ref(true);
const rowsPerPage = ref(25);
const page = ref(1);
const filters = ref({});

const initializeBackendVariables = () => {
    rowsPerPage.value = Number(route.query?.limit ?? 25);
    page.value = Number(route.query?.page ?? 1);

    const queryStringFilters = {};

    if (route.query?.author_email) {
        queryStringFilters['author_email'] = route.query.author_email;
    }

    if (route.query?.keyword) {
        queryStringFilters['keyword'] = route.query.keyword;
    }

    if (route.query?.ingredient) {
        queryStringFilters['ingredient'] = route.query.ingredient;
    }

    filters.value = queryStringFilters;
};

initializeBackendVariables();

const setSearchParams = (urlSearchParams) => {
    urlSearchParams.set('page', page.value.toString());
    urlSearchParams.set('limit', rowsPerPage.value.toString());

    for (let [key, value] of Object.entries(filters.value)) {
        urlSearchParams.set(key, value.toString());
    }
};

const updateQueryString = () => {
    const urlSearchParams = new URLSearchParams();
    setSearchParams(urlSearchParams);

    router.push({
        name: 'home',
        query: Object.fromEntries(urlSearchParams.entries())
    });
};

const getBackendData = () => {
    loading.value = true;

    const backendUrl = new URL('https://backend.wild-alaskan.test/api/recipes');
    setSearchParams(backendUrl.searchParams);

    fetch(backendUrl.toString())
        .then(response => response.json())
        .then((data) => {
            recipesPage.value = data;
            loading.value = false;
        });
};

onMounted(() => {
    initializeBackendVariables();
    getBackendData();
});

watch(() => route.query, () => {
    initializeBackendVariables();
    getBackendData();
});

const setPage = (eventPage, eventRowsPerPage) => {
    page.value = eventPage;
    rowsPerPage.value = eventRowsPerPage;
    updateQueryString();
};

const setFilters = (eventFilters) => {
    page.value = 1;
    filters.value = eventFilters;
    updateQueryString();
};

</script>

<template>
  <main>
      <RecipesDatatable
          :pageData="recipesPage"
          :loading="loading"
          :rowsPerPage="rowsPerPage"
          :page="page"
          :filters="filters"
          @pageChanged="setPage"
          @filtersChanged="setFilters"
      />
  </main>
</template>
