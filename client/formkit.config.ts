import type { DefaultConfigOptions } from '@formkit/vue'
import { createMultiStepPlugin } from '@formkit/addons'
import '@formkit/addons/css/multistep'

export default {
  theme: 'genesis',
  plugins: [createMultiStepPlugin()],
} as DefaultConfigOptions
