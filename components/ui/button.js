import { defineComponent, h } from 'vue'

export const Button = defineComponent({
    props: {
        variant: {
            type: String,
            default: 'default'
        },
        size: {
            type: String,
            default: 'default'
        }
    },
    setup(props, { slots }) {
        return () => h('button', {
            class: [
                'inline-flex items-center justify-center rounded-md font-medium',
                'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2',
                props.variant === 'default' && 'bg-primary text-primary-foreground hover:bg-primary/90',
                props.variant === 'ghost' && 'hover:bg-accent hover:text-accent-foreground',
                props.size === 'default' && 'h-10 px-4 py-2',
                props.size === 'icon' && 'h-10 w-10'
            ]
        }, slots.default?.())
    }
})
