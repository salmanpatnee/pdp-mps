import { useQuery } from '@tanstack/vue-query'
import debounce from 'lodash/debounce'

export function useTickets() {
  const queries = ref<{ page: number; 'filter[title]': string }>({
    page: 1,
    'filter[title]': '',
  })

  const debouncedTitle = ref('')
  const applyDebounced = debounce((val: string) => (debouncedTitle.value = val), 500)

  watch(
    () => queries.value['filter[title]'],
    (val) => {
      queries.value.page = 1
      applyDebounced(val)
    },
  )

  const fetchTickets = async (): Promise<PaginatedResponse<Ticket>> => {
    const params = new URLSearchParams({
      page: String(queries.value.page),
      ...(debouncedTitle.value && { 'filter[title]': debouncedTitle.value }),
    })
    const { data } = await http.get<PaginatedResponse<Ticket>>(`/tickets?${params}`)
    return data
  }

  const query = useQuery({
    queryKey: ['tickets', queries, debouncedTitle], // Vue Query automatically tracks refs
    queryFn: fetchTickets,
    refetchOnWindowFocus: false,
  })

  return { queries, ...query }
}
