import { ref, watchEffect } from 'vue'

export function useTheme() {
  const theme = ref(localStorage.getItem('theme') || 'light')

  watchEffect(() => {
    const root = window.document.documentElement
    root.classList.remove('light', 'dark')
    root.classList.add(theme.value)
    localStorage.setItem('theme', theme.value)
  })

  const toggleTheme = () => {
    theme.value = theme.value === 'light' ? 'dark' : 'light'
  }

  return { theme, toggleTheme }
}
