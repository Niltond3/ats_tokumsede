<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/components/Button.vue';
import { Head, Link } from '@inertiajs/vue3';
import { RiLoginBoxLine as LoginIcon } from "vue-remix-icons";
import { RiArrowRightWideLine as ArrowRightIcon } from "vue-remix-icons";
import validator from 'validator'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { vAutoAnimate } from '@formkit/auto-animate/vue'
import axios from 'axios';

import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Checkbox } from '@/components/ui/checkbox'

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const formSchema = toTypedSchema(z.object({
    telefone: z.string({ required_error: 'Número de telefone obrigatório' }).refine(validator.isMobilePhone, { message: 'Número de telefone inválido' }),
    senha: z.string({ required_error: 'Senha obrigatória' }),
    remember: z.boolean().default(false).optional(),
}))

const { handleSubmit, isSubmitting } = useForm({
    validationSchema: formSchema,
    initialValues: {
        remember: false
    }
})

const onSubmit = handleSubmit((values, { resetField }) => {
    const phoneRaw = values.telefone.replace(/\D/g, '')

    const ddd = phoneRaw.slice(0, 2);
    const telefone = phoneRaw.slice(2);

    const payload = {
        ddd,
        telefone,
        senha: values.senha,
        remember: values.remember
    }

    axios.post(route('cliente.login'), payload)
        .then((response) => {
            console.log('response')
            console.log(response)
            resetField('senha')
        }).catch((error) => {
            console.log('error')
            console.error(error);
            resetField('senha')
        })

})



</script>

<template>
    <GuestLayout>

        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form class="space-y-6" @submit="onSubmit">
            <FormField v-slot="{ componentField }" name="telefone">
                <FormItem v-auto-animate>
                    <FormLabel>Telefone</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="tel"
                            v-mask="['(##) ####-####', '(##) #####-####']" placeholder="Número de telefone"
                            v-bind="componentField" />
                    </FormControl>
                    <FormDescription>
                        Coloque aqui o seu número de telefone cadastrado
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="senha">
                <FormItem v-auto-animate>
                    <FormLabel>Senha</FormLabel>
                    <FormControl>
                        <Input type="password" placeholder="Senha" v-bind="componentField" />
                    </FormControl>
                    <FormDescription>
                        Digite sua senha
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField, value, handleChange }" name="remember" type="checkbox">
                <FormItem v-auto-animate>
                    <FormControl>
                        <Checkbox v-bind="componentField" :checked="value" @update:checked="handleChange" />
                    </FormControl>
                    <FormLabel>Lembrar-se</FormLabel>
                </FormItem>
            </FormField>
            <div class="flex gap-4p">
                <Button type="submit" :class="{ 'opacity-25': isSubmitting }" :disabled="isSubmitting">
                    <i class="icon text-xl">
                        <LoginIcon />
                    </i>
                    Entrar
                </Button>
                <Button href="#informations" class="ms-4" :disabled="isSubmitting">
                    <span class="hidden min-[768px]:block text-zinc-700">Registrar-se</span>
                    <i class="icon icon--chevron-right text-xl hidden min-[425px]:block">
                        <ArrowRightIcon />
                    </i>
                </Button>
            </div>
        </form>

        <div class="flex items-center justify-end mt-4">
            <Link v-if="canResetPassword" :href="route('password.request')"
                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
            Esqueceu sua senha?
            </Link>
        </div>
    </GuestLayout>
</template>
