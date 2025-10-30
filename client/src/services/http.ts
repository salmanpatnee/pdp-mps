const http = axios.create({
  baseURL: import.meta.env.VITE_BACKEND_URL,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// Attach Bearer token to every request if present
http.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers = config.headers ?? {}
    config.headers.Authorization = `Bearer ${token}`
  } else if (config.headers && 'Authorization' in config.headers) {
    delete config.headers.Authorization
  }
  return config
})

http.interceptors.response.use(
  (response) => response,
  (error) => {
    if (
      [401, 419].includes(error.response.status) &&
      !error.request.responseURL.endsWith('/api/user')
    ) {
      console.log('Unauthenticate:', error)
      const { logout } = useAuthStore()
      logout()
    } else {
      return Promise.reject(error)
    }
  },
)

export default http
