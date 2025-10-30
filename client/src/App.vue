<script setup lang="ts">
import { VueQueryDevtools } from '@tanstack/vue-query-devtools'

const GuestLayout = defineAsyncComponent(() => import('@/components/layouts/GuestLayout.vue'))
const DefaultLayout = defineAsyncComponent(() => import('@/components/layouts/DefaultLayout.vue'))
const { activeError } = storeToRefs(useErrorStore())
const route = useRoute()

// Compute which layout to use based on meta.layout
const layoutComponent = computed(() =>
  (route as any)?.meta?.requiresAuth ? DefaultLayout : GuestLayout,
)
</script>

<template>
  <Component :is="layoutComponent">
    <AppError v-if="activeError" />
    <RouterView v-else v-slot="{ Component, route }">
      <Suspense v-if="Component" :timeout="0">
        <component :is="Component" :key="route.name" />
        <!-- <template #default>
        </template> -->
        <template #fallback>
          <div
            class="d-flex flex-column align-items-center justify-content-center py-5"
            style="min-height: 50vh"
          >
            <div class="text-center">
              <div
                class="spinner-border text-primary"
                role="status"
                style="width: 3rem; height: 3rem"
              >
                <span class="visually-hidden">Loading...</span>
              </div>
              <h5 class="mt-3 text-secondary">Loading...</h5>
              <p class="text-muted mb-0">Please wait while we prepare the page.</p>
            </div>
          </div>
        </template>
      </Suspense>
    </RouterView>
  </Component>
  <VueQueryDevtools />
</template>
