import type { APIResponse, Journal } from '@/types'
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import debounce from 'lodash/debounce'

const endpoint = '/journals'

export function useJournals() {
  const queryClient = useQueryClient()

  // For fetching all journals with pagination and filtering
  const fetchJournals = () => {
    const queries = ref({
      page: 1,
      'filter[name]': '',
    })
    const debouncedName = ref('')
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

    const getJournals = async (): Promise<PaginatedResponse<Journal>> => {
      const params = new URLSearchParams()
      params.set('page', String(queries.value.page))
      if (debouncedName.value) params.set('filter[name]', debouncedName.value)
      const { data } = await http.get<PaginatedResponse<Journal>>(`${endpoint}?${params}`)
      return data
    }

    const query = useQuery({
      queryKey: ['journals', queries, debouncedName],
      queryFn: getJournals,
      refetchOnWindowFocus: false,
    })

    return { queries, ...query }
  }

  // For fetching a single journal by ID
  const fetchJournal = (id: Ref<number | null>) => {
    return useQuery({
      queryKey: ['journal', id],
      queryFn: () => http.get<Journal>(`${endpoint}/${id.value}`).then((r) => r.data),
      enabled: computed(() => id.value !== null),
      refetchOnWindowFocus: false,
    })
  }

  // For creating a new journal
  const createJournal = () => {
    return useMutation({
      mutationFn: (journalData: Omit<Journal, 'id'>) =>
        http.post<APIResponse<Journal>>(endpoint, journalData).then((r) => r.data),
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: ['journals'] })
      },
    })
  }

  // For updating a journal
  const updateJournal = () => {
    return useMutation({
      mutationFn: async ({ id, data }: { id: number; data: Journal }) => {
        const res = await http.put<APIResponse<Journal>>(`${endpoint}/${id}`, data)
        return res.data
      },
      onSuccess: (journal) => {
        queryClient.invalidateQueries({ queryKey: ['journals'] })
        queryClient.invalidateQueries({ queryKey: ['journal', journal.data.id] })
      },
    })
  }

  // For deleting a journal
  const deleteJournal = () => {
    return useMutation({
      mutationFn: (journalId: number) => http.delete(`${endpoint}/${journalId}`),
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: ['journals'] })
      },
    })
  }

  return { fetchJournals, fetchJournal, createJournal, updateJournal, deleteJournal }
}
