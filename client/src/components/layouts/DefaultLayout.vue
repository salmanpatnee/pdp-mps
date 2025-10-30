<script setup lang="ts">
// const { pageData } = storeToRefs(usePageStore())
</script>

<template>
  <div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
      <AppHeader />
      <AppSidebar />
      <div class="app-main">
        <!-- <div class="app-content-header" v-if="pageData.title">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6">
                <h3 class="mb-0">{{ pageData.title }}</h3>
              </div>
            </div>
          </div>
        </div> -->

        <div class="app-content p-3">
          <slot />
        </div>
      </div>
      <AppFooter />
    </div>
  </div>
</template>
