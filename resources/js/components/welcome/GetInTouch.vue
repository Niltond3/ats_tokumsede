<script setup lang="ts">
import { h } from 'vue'
import validator from 'validator';
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import querystring from 'querystring'
import * as z from 'zod'

import Button from '../Button.vue';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage
} from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { toast } from '@/components/ui/toast'
import axios from 'axios';


const formSchema = toTypedSchema(z.object({
    name: z.string({ required_error: 'Nome obrigatório' }).min(5).max(50).refine(validator.isAlpha, { message: 'Nome inválido' }),
    email: z.string({ required_error: 'Email obrigatório' }).refine(validator.isEmail, { message: 'Email inválido' }),
    phone: z.string({ required_error: 'Número de telefone obrigatório' }).refine(validator.isMobilePhone, { message: 'Número de telefone inválido' }),
    messager: z.string({ required_error: 'Menssagem obrigatória' }).min(10, { message: 'Menssagem muito curta, seja mais eloquente' }).max(100, { message: 'Menssagem muito longa, seja mais conciso' }),
}))

const { handleSubmit } = useForm({
    validationSchema: formSchema,
})

const onSubmit = handleSubmit((values) => {
    console.log(values)
    axios.post('app/Controllers/Api/IndexController.php/enviaEmail', querystring.stringify(values)).then(res => {
        console.log(res)
    });

    toast({
        title: 'You submitted the following values:',
        description: h('pre', { class: 'mt-2 w-[340px] rounded-md bg-slate-950 p-4' }, h('code', { class: 'text-white' }, JSON.stringify(values, null, 2))),
    })
})
</script>

<template>
    <section class="container flex [&_label]:text-slate-700 [&>*]:flex-1 flex-col gap-4 xl:px-64 mt-20 py-10"
        id="faleconosco">
        <h2 class="my-8 text-slate-700">Fale Conosco</h2>
        <form class="w-2/3 space-y-6" @submit="onSubmit" ref="form">
            <FormField v-slot="{ componentField }" name="name">
                <FormItem v-auto-animate>
                    <FormLabel>Nome</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="text" placeholder="Nome completo"
                            v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="email">
                <FormItem v-auto-animate>
                    <FormLabel>E-Mail</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="text" placeholder="e-mail válido"
                            v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="phone">
                <FormItem v-auto-animate>
                    <FormLabel>Telefone</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="tel"
                            v-mask="['(##) ####-####', '(##) #####-####']" placeholder="Número de telefone"
                            v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="messager">
                <FormItem v-auto-animate>
                    <FormLabel>Mensságem</FormLabel>
                    <FormControl>
                        <Input class="focus-visible:ring-slate-500" type="text" placeholder="Digite aqui sua menságem"
                            v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <Button type="submit">
                Submit
            </Button>
        </form>
    </section>

</template>
