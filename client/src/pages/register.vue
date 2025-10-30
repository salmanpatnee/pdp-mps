<script setup lang="ts">
definePage({
  meta: {
    requiresAuth: false,
    isGuest: true,
  },
})
const { register } = useAuthStore()
const router = useRouter()

const handleSubmit = async (payload: RegisterFormPayload, node?: FormKitNode) => {
  try {
    const success = await register(payload)
    if (success) {
      router.push('/user')
    }
  } catch (error) {
    handleInvalidForm(error, node)
  }
}
</script>

<template>
  <div class="card-body register-card-body">
    <p class="register-box-msg">Register a new membership</p>
    <FormKit type="form" submit-label="Register" @submit="handleSubmit">
      <FormKit label="Name" name="name" type="text" suffix-icon="avatarMan" required />
      <FormKit label="Email" name="email" type="email" suffix-icon="email" required />
      <FormKit label="Password" name="password" type="password" suffix-icon="password" required />
      <FormKit
        label="Password Confirmation"
        name="password_confirmation"
        type="password"
        suffix-icon="password"
        required
      />
    </FormKit>

    <p class="mb-0 text-center">
      <router-link to="/login" class="link-primary text-center">
        I already have an account
      </router-link>
    </p>
  </div>
</template>
