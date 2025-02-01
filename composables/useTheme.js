import { ref } from 'vue'

export function useTheme() {
    const theme = ref(localStorage.getItem('theme') || 'light')

    const toggleTheme = () => {
        theme.value = theme.value === 'light' ? 'dark' : 'light'
        localStorage.setItem('theme', theme.value)
        document.documentElement.classList.toggle('dark')
    }

    return {
        theme,
        toggleTheme
    }
}
