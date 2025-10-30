<script setup lang="ts">
const { logout } = useAuthStore()
const { user } = storeToRefs(useAuthStore())
const router = useRouter()

const handleLogout = async () => {
  try {
    const { success } = await logout()

    if (success) {
      router.replace('/login')
    }
  } catch (error) {
    console.log(error)
  }
}
</script>

<template>
  <nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
            <img
              src="/img/user2-160x160.jpg"
              class="user-image rounded-circle shadow"
              alt="User Image"
            />
            <span class="d-none d-md-inline">{{ user?.name || 'User' }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
            <li class="user-header text-bg-primary">
              <img src="/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image" />
              <p>
                {{ user?.name || 'User' }} - {{ user?.email || 'user@example.com' }}
                <small
                  >Member since
                  {{
                    user?.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A'
                  }}</small
                >
              </p>
            </li>

            <li class="user-footer">
              <router-link to="/profile" class="btn btn-default btn-flat">Profile</router-link>
              <a @click.prevent="handleLogout" href="#" class="btn btn-default btn-flat float-end">
                Sign out
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</template>
