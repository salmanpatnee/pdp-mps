// import type { APIResponse, Journal } from '@/types'
import type { Manuscript, ManuscriptFormPayload } from '@/types'
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import debounce from 'lodash/debounce'

const endpoint = '/manuscripts'

export function useManuscripts() {
  const queryClient = useQueryClient()

  const fetchManuscripts = () => {
    const queries = ref({
      page: 1,
      'filter[search]': '',
    })
    const debouncedSearchTerm = ref('')
    const updateDebouncedSearchTerm = debounce((searchTerm: string) => {
      debouncedSearchTerm.value = searchTerm
    }, 500)

    watch(
      () => queries.value['filter[search]'],
      (val) => {
        queries.value.page = 1
        updateDebouncedSearchTerm(val)
      },
    )

    const getManuscript = async (): Promise<PaginatedResponse<Manuscript>> => {
      const params = new URLSearchParams()
      params.set('page', String(queries.value.page))
      if (debouncedSearchTerm.value) params.set('filter[search]', debouncedSearchTerm.value)
      const { data } = await http.get<PaginatedResponse<Manuscript>>(`${endpoint}?${params}`)
      return data
    }

    const query = useQuery({
      queryKey: ['manuscripts', queries, debouncedSearchTerm],
      queryFn: getManuscript,
      refetchOnWindowFocus: false,
    })

    return { queries, ...query }
  }

  // For fetching a single journal by ID
  const fetchManuscript = (id: Ref<number | null>) => {
    return useQuery({
      queryKey: ['manuscript', id],
      queryFn: () => http.get<Manuscript>(`${endpoint}/${id.value}`).then((r) => r.data),
      enabled: computed(() => id.value !== null),
      refetchOnWindowFocus: false,
    })
  }

  // For creating a new manuscript
  const createManuscript = () => {
    return useMutation({
      mutationFn: (data: ManuscriptFormPayload) =>
        http.post<APIResponse<Manuscript>>(endpoint, data).then((r) => r.data),
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: ['manuscripts'] })
      },
    })
  }

  // For updating a manuscript
  const updateManuscript = () => {
    return useMutation({
      mutationFn: async ({ id, data }: { id: number; data: ManuscriptFormPayload }) => {
        const res = await http.put<APIResponse<Manuscript>>(`${endpoint}/${id}`, data)
        return res.data
      },
      onSuccess: (manuscript) => {
        queryClient.invalidateQueries({ queryKey: ['manuscripts'] })
        queryClient.invalidateQueries({ queryKey: ['manuscript', manuscript.data.id] })
      },
    })
  }

  // For deleting a manuscript
  const deleteManuscript = () => {
    return useMutation({
      mutationFn: (manuscriptId: number) => http.delete(`${endpoint}/${manuscriptId}`),
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: ['manuscripts'] })
      },
    })
  }

  // For downloading a manuscript file
  const downloadFile = async (fileId: number, fileName: string) => {
    try {
      const response = await http.get(`/manuscript-files/${fileId}/download`, {
        responseType: 'blob',
      })

      // Create blob link to download
      const url = window.URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', fileName)
      document.body.appendChild(link)
      link.click()
      link.remove()
      window.URL.revokeObjectURL(url)
    } catch (error) {
      console.error('Download failed:', error)
      throw error
    }
  }

  return {
    fetchManuscripts,
    fetchManuscript,
    createManuscript,
    updateManuscript,
    deleteManuscript,
    downloadFile,
  }
}
