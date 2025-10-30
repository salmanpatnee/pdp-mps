/// <reference types="vite/client" />
/// <reference types="unplugin-vue-router/client" />

declare module 'laravel-vue-pagination' {
  export const Bootstrap4Pagination: any
  export const Bootstrap5Pagination: any
}

declare module 'lodash/debounce' {
  const debounce: <T extends (...args: any[]) => any>(
    func: T,
    wait?: number,
  ) => T & { cancel: () => void; flush: () => void }
  export default debounce
}
