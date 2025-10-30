import type { APIResponse, User } from '@/types'
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import debounce from 'lodash/debounce'

const endpoint = '/users'

export function useUsers() {
  const queryClient = useQueryClient()

  const fetchUsers = () => {
    const queries = ref({ page: 1, 'filter[name]': '' })
    const debouncedName = ref('')

    // Debounce the name filter for better UX
    const updateDebouncedName = debounce((name: string) => {
      debouncedName.value = name
    }, 500)

    watch(
      () => queries.value['filter[name]'],
      (val) => {
        queries.value.page = 1
        updateDebouncedName(val)
      },
    )

    const getUsers = async (): Promise<PaginatedResponse<User>> => {
      const params = new URLSearchParams()
      params.set('page', String(queries.value.page))
      if (debouncedName.value) params.set('filter[name]', debouncedName.value)
      const { data } = await http.get<PaginatedResponse<User>>(`${endpoint}?${params}`)
      return data
    }

    const query = useQuery({
      queryKey: ['users', queries, debouncedName],
      queryFn: getUsers,
      refetchOnWindowFocus: false,
    })

    return { queries, ...query }
  }

  const fetchUser = (id: Ref<number | null>) => {
    return useQuery({
      queryKey: ['user', id],
      queryFn: () => http.get<User>(`${endpoint}/${id.value}`).then((res) => res.data),
      enabled: computed(() => id.value !== null),
    })
  }

  const createUser = () => {
    return useMutation({
      mutationFn: (userData: UserFormPayload) =>
        http.post<APIResponse<User>>(`${endpoint}`, userData).then((res) => res.data),
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: ['users'] })
      },
    })
  }

  const updateUser = () => {
    return useMutation({
      mutationFn: async ({ id, data }: { id: number; data: Omit<User, 'id'> }) => {
        const res = await http.put<APIResponse<User>>(`${endpoint}/${id}`, data)
        return res.data
      },
      onSuccess: (user) => {
        queryClient.invalidateQueries({ queryKey: ['users'] })
        queryClient.invalidateQueries({ queryKey: ['user', user.data.id] })
      },
    })
  }

  const deleteUser = () => {
    return useMutation({
      mutationFn: (userId: number) => http.delete(`${endpoint}/${userId}`),
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: ['users'] })
      },
    })
  }

  const searchAuthors = async (query: string) => {
    try {
      const response = await http.get<User[]>(`/authors/search?query=${query}`)
      return response.data
    } catch (error: any) {
      if (error.response && error.response.status === 404) {
        return []
      }
      throw error
    }
  }

  return { fetchUsers, fetchUser, createUser, updateUser, deleteUser, searchAuthors }
}
