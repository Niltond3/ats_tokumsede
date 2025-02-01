import { defineComponent, h } from 'vue'

export const Card = defineComponent({
    setup(_, { slots }) {
        return () => h('div', {
            class: 'rounded-lg border bg-card text-card-foreground shadow-sm'
        }, slots.default?.())
    }
})

export const CardHeader = defineComponent({
    setup(_, { slots }) {
        return () => h('div', {
            class: 'flex flex-col space-y-1.5 p-6'
        }, slots.default?.())
    }
})

// Similar pattern for CardTitle, CardDescription, CardContent, CardFooter
