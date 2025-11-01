<script setup lang="ts">
definePage({
  meta: {
    requiresAuth: false,
    isGuest: true,
  },
})
const { login } = useAuthStore()
const router = useRouter()

const handleSubmit = async (payload: LoginFormPayload, node?: FormKitNode) => {
  try {
    const success = await login(payload)

    if (success) {
      router.push('/manuscripts')
    }
  } catch (error) {
    handleInvalidForm(error, node)
  }
}
</script>

<template>
  <div class="card-body login-card-body">
    <p class="login-box-msg">Sign in to start your session</p>
    <FormKit type="form" submit-label="Login" @submit="handleSubmit">
      <FormKit name="email" type="email" placeholder="Email Address" suffix-icon="email" required />
      <FormKit
        name="password"
        type="password"
        placeholder="Password"
        suffix-icon="password"
        required
      />
    </FormKit>
    <!-- <p class="mb-0 text-center">
      <router-link to="/register" class="link-primary"> Dont Have an Account </router-link>
    </p> -->
  </div>
</template>
