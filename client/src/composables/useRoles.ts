import { useQuery } from '@tanstack/vue-query'

const fetchRoles = async () => {
  const { data } = await http.get<Role[]>('/roles')
  return data
}

export function useRoles() {
  return useQuery({
    queryKey: ['roles'],
    queryFn: fetchRoles,
  })
}
