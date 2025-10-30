<script setup lang="ts">
definePage({
  meta: {
    requiresAuth: true,
  },
})

usePageStore().pageData.title = 'Dashboard'
</script>

<template>
  <div>
    <!-- Data -->
    <template>
      <div class="container-fluid">
        <div class="row">
          <div class="card">
            <div class="card-header">
              <div class="d-flex align-items-center justify-content-between">
                <PageTitle text="Dashboard" />
                <!-- <div class="d-flex align-items-center justify-content-between">
                  <AddButton label="Add User" path="/users/form" />
                </div> -->
              </div>
            </div>
            <div class="card-body"></div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
