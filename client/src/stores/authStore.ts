export const useAuthStore = defineStore('auth', () => {
  const user = ref<null | User>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))

  const register = async (payload: RegisterFormPayload) => {
    try {
      const { data } = await http.post('/register', payload)
      return { success: true, data }
    } catch (error) {
      console.log(error)
      throw error
    }
  }

  const login = async (payload: LoginFormPayload) => {
    try {
      const { data } = await http.post('/login', payload)
      const sanctumToken = data?.message?.token as string | undefined
      if (sanctumToken) {
        localStorage.setItem('auth_token', sanctumToken)
        token.value = sanctumToken
      }
      // Optionally fetch user right after login
      try {
        user.value = await getUser()
      } catch (error) {
        console.log(error)
      }
      return { success: true }
    } catch (error) {
      throw error
    }
  }

  const getUser = async () => {
    if (user.value) return user.value

    try {
      const { data } = await http.get('/user')
      return data ? (data as User) : null
    } catch (error) {
      throw error
    }
  }

  const initUser = async () => {
    // Only attempt if token exists
    if (!token.value) {
      user.value = null
      return
    }
    user.value = await getUser()
  }

  const clearUser = (token: globalThis.Ref<string | null>, user: globalThis.Ref<User | null>) => {
    localStorage.removeItem('auth_token')
    token.value = null
    user.value = null
  }

  const logout = async () => {
    try {
      // Invalidate current access token server-side if logged in
      if (token.value) {
        await http.post('/logout')
      }
      // Clear client token regardless of server response
      clearUser(token, user)
      return { success: true }
    } catch (error) {
      // Even if API fails, clear local state to force re-auth
      clearUser(token, user)
      throw error
    }
  }

  return {
    user,
    token,
    register,
    login,
    getUser,
    initUser,
    logout,
  }
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useAuthStore, import.meta.hot))
}
