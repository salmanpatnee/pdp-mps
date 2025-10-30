import { useQuery } from '@tanstack/vue-query'

const fetchArticleTypes = async () => {
  const { data } = await http.get<ArticleType[]>('/article_types')
  return data
}

export function useArticleTypes() {
  return useQuery({
    queryKey: ['articleTypes'],
    queryFn: fetchArticleTypes,
  })
}
