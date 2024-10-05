<script setup>

import {defineProps, defineEmits, defineModel, ref, computed} from 'vue';
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import InputText from "primevue/inputtext";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import Button from "primevue/button";
import IngredientsList from "@/components/IngredientsList.vue";
import StepsList from "@/components/StepsList.vue";

const props = defineProps({
    pageData: {
        required: true
    },
    loading: {
        type: Boolean,
        required: true
    },
    rowsPerPage: {
        type: Number,
        required: true
    },
    page: {
        type: Number,
        required: true
    },
    filters: {
        type: Object,
        required: true
    }
});

const localRowsPerPage = ref(props.rowsPerPage);
const localPage = ref(props.page);
const localFilters = ref({
    author_email: {
        value: props.filters?.author_email
    },
    keyword: {
        value: props.filters?.keyword
    },
    ingredient: {
        value: props.filters?.ingredient
    },
});

const emit = defineEmits([
    'pageChanged',
    'filtersChanged',
]);

const rowsPerPageOptions = [
    5,
    10,
    25
];

const getFirstOffset = computed(() => {
    return (localPage.value - 1) * localRowsPerPage.value;
});

const setCurrentPage = (event) => {
    localRowsPerPage.value = event.rows;

    if (localRowsPerPage.value !== props.rowsPerPage) {
        localPage.value = 1;
    } else {
        localPage.value = event.page + 1;
    }

    emit('pageChanged', localPage.value, localRowsPerPage.value);
};

const setCurrentFilters = (eventFilters) => {
    localPage.value = 1;
    localFilters.value = eventFilters;

    const emitFilters = {};

    for (let [key, value] of Object.entries(localFilters.value)) {
        if (value.value) {
            emitFilters[key] = value.value;
        }
    }

    emit('filtersChanged', emitFilters);
};

const clearKeyword = () => {
    localFilters.value.keyword.value = null;
    setCurrentFilters(localFilters.value);
};

</script>

<template>
    <DataTable
        lazy
        :value="pageData?.data ?? []"
        :loading="loading"
        dataKey="slug"
        size="small"
        show-gridlines
        columnResizeMode="expand"
        :paginator="true"
        :first="getFirstOffset"
        :totalRecords="pageData?.meta?.total"
        :rowsPerPageOptions="rowsPerPageOptions"
        :rows="localRowsPerPage"
        v-model:filters="localFilters"
        filterDisplay="row"
        @page="setCurrentPage"
        @filter="setCurrentFilters($event.filters)"
    >
        <template #empty> No Recipes Found. </template>
        <template #loading> Loading Recipes, Please Wait. </template>
        <template #header>
            <div class="flex justify-end">
                <Button
                    v-if="localFilters.keyword.value"
                    type="button"
                    icon="pi pi-filter-slash"
                    label="Clear"
                    outlined
                    class="mr-2"
                    @click="clearKeyword()"
                />
                <IconField>
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText
                        v-model="localFilters['keyword'].value"
                        placeholder="Keyword Search"
                        @keydown.enter="setCurrentFilters(localFilters)"
                    />
                </IconField>
            </div>
        </template>
        <Column
            columnKey="view_button"
        >
            <template #body="slotProps">
                <div class="flex">
                    <RouterLink
                        :to="{
                            name: 'recipe',
                            params: {
                                slug: slotProps.data.slug
                            }
                        }"
                        class="cursor-pointer mx-auto"
                    >
                        <i class="pi pi-eye" />
                    </RouterLink>
                </div>
            </template>
        </Column>
        <Column
            field="author_email"
            header="Author"
            :showFilterMatchModes="false"
            :showFilterMenu="false"
        >
            <template #filter="{ filterModel, filterCallback }">
                <InputText
                    v-model="filterModel.value"
                    type="text"
                    placeholder="Search for Author"
                    @keydown.enter="filterCallback()"
                />
            </template>
        </Column>
        <Column
            field="name"
            header="Name"
        />
        <Column
            field="description"
            header="Description"
        />
        <Column
            field="ingredients"
            header="Ingredients"
            filterField="ingredient"
            :showFilterMatchModes="false"
            :showFilterMenu="false"
        >
            <template #filter="{ filterModel, filterCallback }">
                <InputText
                    v-model="filterModel.value"
                    type="text"
                    placeholder="Search for Ingredient"
                    @keydown.enter="filterCallback()"
                />
            </template>
            <template #body="slotProps">
                <IngredientsList
                    :ingredients="slotProps.data.ingredients"
                    class="pl-5"
                />
            </template>
        </Column>
        <Column
            field="steps"
            header="Steps"
        >
            <template #body="slotProps">
                <StepsList
                    :steps="slotProps.data.ingredients"
                    class="pl-5"
                />
            </template>
        </Column>
    </DataTable>
</template>
