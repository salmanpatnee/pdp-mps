<script setup lang="ts"></script>

<template>
  <div class="login-page bg-body-secondary">
    <div class="login-box">
      <div class="card card-outline card-primary">
        <div class="card-header text-center">
          <router-link to="/">
            <img src="/public/img/final-Logo.webp" class="img-fluid w-25" alt="Logo" />
          </router-link>
        </div>
        <slot />
      </div>
    </div>
  </div>
</template>
