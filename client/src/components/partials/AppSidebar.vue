<script setup lang="ts">
import { Icon } from '@iconify/vue'
</script>

<template>
  <aside class="app-sidebar bg-primary shadow">
    <div class="sidebar-brand bg-white">
      <router-link to="/" class="brand-link">
        <img src="/img/logo-sidebar.webp" alt="Logo" class="brand-image" />
        <!-- <span class="brand-text fw-light">Portal</span> -->
      </router-link>
    </div>
    <div class="sidebar-wrapper">
      <nav class="mt-2">
        <ul
          class="nav sidebar-menu flex-column"
          data-lte-toggle="treeview"
          role="navigation"
          aria-label="Main navigation"
          data-accordion="false"
          id="navigation"
        >
          <!-- <li class="nav-item menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon bi bi-speedometer"></i>
              <p>
                Dashboard
                <i class="nav-arrow bi bi-chevron-right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.html" class="nav-link active">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>Dashboard v1</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index2.html" class="nav-link">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>Dashboard v2</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index3.html" class="nav-link">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>Dashboard v3</p>
                </a>
              </li>
            </ul>
          </li> -->
          <li>
            <router-link to="/" class="nav-link" exactActiveClass="bg-white text-dark">
              <i class="nav-icon bi bi-palette"></i>
              <p>Dashboard</p>
            </router-link>
          </li>

          <li>
            <router-link to="/users" class="nav-link" exactActiveClass="bg-white text-dark">
              <!-- <i class="nav-icon bi bi-palette"></i> -->
              <Icon icon="mdi:users-group" width="20" height="20" />
              <p>Users</p>
            </router-link>
          </li>
          <li>
            <router-link to="/journals" class="nav-link" exactActiveClass="bg-white text-dark">
              <!-- <i class="nav-icon bi bi-palette"></i> -->
              <Icon icon="mdi:book-open-outline" width="20" height="20" />
              <p>Journals</p>
            </router-link>
          </li>
          <li>
            <router-link to="/manuscripts" class="nav-link" exactActiveClass="bg-white text-dark">
              <!-- <i class="nav-icon bi bi-palette"></i> -->
              <Icon icon="mdi:paper-add-outline" width="24" height="24" />
              <p>Manuscripts</p>
            </router-link>
          </li>
        </ul>
        <!--end::Sidebar Menu-->
      </nav>
    </div>
  </aside>
</template>
