import { createRouter, createWebHistory } from 'vue-router'
import { routes, handleHotUpdate } from 'vue-router/auto-routes'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()
  const { user, token } = storeToRefs(auth)
  const { initUser } = auth
  const { requiresAuth, isGuest } = to.meta as { requiresAuth?: boolean; isGuest?: boolean }

  const hasToken = !!token?.value || !!localStorage.getItem('auth_token')
  let isLoggedIn = !!user?.value

  // Protected routes: require a token and a valid user
  if (requiresAuth) {
    if (!hasToken) {
      return next({ path: '/login', query: { redirect: to.fullPath } })
    }
    if (!isLoggedIn) {
      try {
        await initUser()
        isLoggedIn = !!user.value
      } catch (error) {
        console.log(error)
      }
      if (!isLoggedIn) {
        return next({ path: '/login', query: { redirect: to.fullPath } })
      }
    }
  }

  // Guest-only routes: redirect authenticated users away
  if (isGuest) {
    if (isLoggedIn) {
      return next({ path: '/' })
    }
    if (hasToken && !isLoggedIn) {
      try {
        await initUser()
        if (auth.user) {
          return next({ path: '/' })
        }
      } catch (error) {
        console.log(error)
      }
    }
  }

  return next()
})

export default router

if (import.meta.hot) {
  handleHotUpdate(router)
}
