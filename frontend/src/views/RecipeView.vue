<script setup>
import {computed, defineProps, onMounted, ref} from "vue";
import Breadcrumb from "primevue/breadcrumb";
import Skeleton from "primevue/skeleton";
import {useRouter} from "vue-router";
import SkeletonList from "@/components/SkeletonList.vue";
import IngredientsList from "@/components/IngredientsList.vue";
import StepsList from "@/components/StepsList.vue";

const props = defineProps({
    slug: {
        type: String,
        required: true
    }
});

const router = useRouter();
const loading = ref(true);
const recipe = ref({});

const getBackendData = () => {
    loading.value = true;

    fetch(`https://backend.wild-alaskan.test/api/recipes/${props.slug}`)
        .then(response => response.json())
        .then((data) => {
            recipe.value = data.data;
            loading.value = false;
        });
};

onMounted(() => {
    getBackendData();
});

const home = computed(() => {
    const lastPath = router.options.history.state.back;

    return {
        label: '<- Back to Recipes',
        url: lastPath ?? '/'
    };
});

</script>

<template>
  <main>
      <Breadcrumb
          :home="home"
          class="mb-4"
      >
          <template #item="{ item, props }">
              <RouterLink :to="item.url">
                  {{ item.label }}
              </RouterLink>
          </template>
      </Breadcrumb>
      <div class="mb-1">
          <Skeleton
              v-if="loading"
          />
          <h1
              v-else
              class="text-xl font-bold"
          >
              {{ recipe.name }}
          </h1>
      </div>
      <div class="mb-5">
          <Skeleton
              v-if="loading"
              width="25%"
          />
          <p
              v-else
          >
              <span class="text-sm">By {{ recipe.author_email }}</span>
          </p>
      </div>
      <div class="mb-5">
          <Skeleton
              v-if="loading"
          />
          <p
              v-else
          >
              {{ recipe.description }}
          </p>
      </div>
      <div class="mb-5">
          <SkeletonList
              v-if="loading"
          />
          <div
              v-else
          >
              <h2 class="font-bold">
                  Ingredients
              </h2>
              <IngredientsList
                  :ingredients="recipe.ingredients"
                  class="pl-5"
              />
          </div>
      </div>
      <div
          v-if="!loading"
      >
          <h2 class="font-bold">
              Steps
          </h2>
          <StepsList
              :steps="recipe.steps"
              class="pl-5"
          />
      </div>
  </main>
</template>
